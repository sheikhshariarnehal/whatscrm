<?php

namespace App\Services\WhatsApp;

use App\Events\ConversationUpdated;
use App\Events\MessageStatusUpdated;
use App\Events\NewMessageReceived;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MetaCredential;
use App\Scopes\WorkspaceScope;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WebhookProcessor
{
    /**
     * Process an incoming Meta WhatsApp webhook payload.
     */
    public function process(array $payload): void
    {
        if (($payload['object'] ?? '') !== 'whatsapp_business_account') {
            return;
        }

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? '') === 'message_template_status_update') {
                    $this->handleTemplateStatusUpdate($entry['id'] ?? null, $change['value'] ?? []);
                    continue;
                }

                if (($change['field'] ?? '') !== 'messages') {
                    continue;
                }

                $value = $change['value'] ?? [];
                $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;

                if (! $phoneNumberId) {
                    continue;
                }

                // Locate matching workspace credential
                $credential = MetaCredential::withoutGlobalScope(WorkspaceScope::class)
                    ->where('phone_number_id', $phoneNumberId)
                    ->first();

                if (! $credential) {
                    Log::warning("Meta Webhook received for unregistered phone_number_id: {$phoneNumberId}");
                    continue;
                }

                $workspaceId = $credential->workspace_id;
                App::instance('current_workspace_id', $workspaceId);

                // 1. Process Inbound Messages
                if (! empty($value['messages'])) {
                    $contactsMeta = collect($value['contacts'] ?? [])->keyBy('wa_id');

                    foreach ($value['messages'] as $messageData) {
                        $this->handleInboundMessage($workspaceId, $messageData, $contactsMeta, $credential);
                    }
                }

                // 2. Process Message Status Updates (sent, delivered, read, failed)
                if (! empty($value['statuses'])) {
                    foreach ($value['statuses'] as $statusData) {
                        $this->handleStatusUpdate($workspaceId, $statusData);
                    }
                }
            }
        }
    }

    protected function handleInboundMessage(int $workspaceId, array $msg, $contactsMeta, ?MetaCredential $credential = null): void
    {
        $senderMobile = '+' . preg_replace('/[^0-9]/', '', $msg['from'] ?? '');
        $waId         = $msg['from'] ?? '';
        $contactMeta  = $contactsMeta->get($waId) ?? [];
        $senderName   = $contactMeta['profile']['name'] ?? $senderMobile;

        // Extract profile photo directly from webhook payload (delivered on some API tiers)
        $webhookProfilePicture = $contactMeta['profile']['picture'] ?? null;

        $messageId = $msg['id'] ?? null;

        // Prevent duplicate processing
        if ($messageId && Message::withoutGlobalScope(WorkspaceScope::class)->where('external_id', $messageId)->exists()) {
            return;
        }

        // Find or create Contact
        $contact = Contact::withoutGlobalScope(WorkspaceScope::class)->firstOrCreate(
            ['workspace_id' => $workspaceId, 'mobile' => $senderMobile],
            [
                'name'   => $senderName,
                'source' => 'whatsapp_inbound',
            ]
        );

        // Find or create Conversation
        $conversation = Conversation::withoutGlobalScope(WorkspaceScope::class)->firstOrCreate(
            ['workspace_id' => $workspaceId, 'chat_id' => $senderMobile],
            [
                'contact_id'    => $contact->id,
                'channel'       => 'whatsapp_cloud',
                'sender_name'   => $senderName,
                'sender_mobile' => $senderMobile,
                'status'        => 'open',
            ]
        );

        // ── Sync WhatsApp Profile Photo ──────────────────────────────────────
        $this->syncProfilePhoto($conversation, $contact, $webhookProfilePicture, $credential);
        // ────────────────────────────────────────────────────────────────────

        // Determine message type & content
        $type = $msg['type'] ?? 'text';
        $content = null;
        $mediaUrl = null;
        $caption = null;

        switch ($type) {
            case 'text':
                $content = $msg['text']['body'] ?? '';
                break;
            case 'image':
                $mediaUrl = $msg['image']['id'] ?? null;
                $caption = $msg['image']['caption'] ?? null;
                $content = $caption ?? '[Image]';
                break;
            case 'document':
                $mediaUrl = $msg['document']['id'] ?? null;
                $caption = $msg['document']['filename'] ?? null;
                $content = $caption ? "[Document: {$caption}]" : '[Document]';
                break;
            case 'audio':
                $mediaUrl = $msg['audio']['id'] ?? null;
                $content = '[Voice Message]';
                break;
            case 'video':
                $mediaUrl = $msg['video']['id'] ?? null;
                $content = '[Video]';
                break;
            case 'location':
                $loc = $msg['location'] ?? [];
                $content = "Location: Lat {$loc['latitude']}, Long {$loc['longitude']}";
                break;
            case 'button':
                $content = $msg['button']['text'] ?? '[Button Response]';
                break;
            case 'interactive':
                $reply = $msg['interactive']['button_reply'] ?? $msg['interactive']['list_reply'] ?? [];
                $content = $reply['title'] ?? '[Interactive Reply]';
                break;
            default:
                $content = "[{$type} message]";
        }

        // Create Message
        $message = Message::withoutGlobalScope(WorkspaceScope::class)->create([
            'workspace_id' => $workspaceId,
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'type' => $type,
            'content' => $content,
            'media_url' => $mediaUrl,
            'caption' => $caption,
            'status' => 'delivered',
            'channel' => 'whatsapp_cloud',
            'external_id' => $messageId,
            'context' => $msg['context'] ?? null,
            'metadata' => $msg,
        ]);

        // Update Conversation summary
        $conversation->update([
            'last_message' => $content,
            'last_message_at' => now(),
            'unread_count' => $conversation->unread_count + 1,
            'status' => 'open',
        ]);

        // Broadcast Realtime Event to Active Inboxes
        event(new NewMessageReceived($message));
        event(new ConversationUpdated($conversation));

        // Trigger Flow & Chatbot Automations
        try {
            app(\App\Services\Automation\FlowExecutionService::class)->handleIncomingMessage($conversation, $message, $contact);
        } catch (\Throwable $e) {
            Log::error('Automation Flow Execution Error: ' . $e->getMessage());
        }
    }

    /**
     * Sync the WhatsApp profile photo for a conversation/contact.
     *
     * Priority order:
     *  1. Photo URL delivered directly in the webhook payload contacts[].profile.picture
     *  2. Photo fetched live from Meta Graph API (cached per-contact for 24 hours)
     *
     * The photo is only updated when:
     *  - The conversation has no profile_url yet, OR
     *  - A newer URL has been delivered via the webhook payload
     *
     * Uses a 24h cache key per contact number to avoid hammering the Graph API.
     */
    protected function syncProfilePhoto(
        Conversation  $conversation,
        Contact       $contact,
        ?string       $webhookProfilePicture,
        ?MetaCredential $credential
    ): void {
        $cacheKey = "wa_profile_photo_{$contact->mobile}";

        // If the webhook delivered a photo URL, use it immediately
        if ($webhookProfilePicture && filter_var($webhookProfilePicture, FILTER_VALIDATE_URL)) {
            $conversation->update(['profile_url' => $webhookProfilePicture]);
            $contact->update(['avatar_url' => $webhookProfilePicture]);
            Cache::put($cacheKey, $webhookProfilePicture, now()->addHours(24));
            return;
        }

        // Skip if the conversation already has a profile photo saved
        if (!empty($conversation->profile_url)) {
            return;
        }

        // Skip if we already tried and failed within the last 24 hours
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if ($cached && !empty($conversation->profile_url)) {
                return;
            }
        }

        // Attempt to fetch live from Meta Graph API
        if ($credential) {
            try {
                $service = new CloudApiService($credential);
                $url = $service->fetchContactProfilePhotoUrl($contact->mobile);

                // Cache result (even null = "we tried") for 24 hours to rate-limit API calls
                Cache::put($cacheKey, $url ?? 'none', now()->addHours(24));

                if ($url) {
                    $conversation->update(['profile_url' => $url]);
                    $contact->update(['avatar_url' => $url]);
                    Log::info("Profile photo synced for {$contact->mobile}");
                }
            } catch (\Throwable $e) {
                Log::debug('syncProfilePhoto failed: ' . $e->getMessage());
            }
        }
    }

    protected function handleStatusUpdate(int $workspaceId, array $statusData): void
    {
        $messageId = $statusData['id'] ?? null;
        $status = $statusData['status'] ?? null;

        if (! $messageId || ! $status) {
            return;
        }

        $message = Message::withoutGlobalScope(WorkspaceScope::class)
            ->where('workspace_id', $workspaceId)
            ->where('external_id', $messageId)
            ->first();

        if (! $message) {
            return;
        }

        $updates = ['status' => $status];
        if (!empty($statusData['errors'])) {
            $existingMeta = $message->metadata ?? [];
            $errorDetails = $statusData['errors'][0]['error_data']['details'] ?? $statusData['errors'][0]['message'] ?? 'Delivery failed';
            $updates['metadata'] = array_merge($existingMeta, [
                'errors' => $statusData['errors'],
                'failed_reason' => $errorDetails,
                'error' => $errorDetails,
            ]);
        }

        $message->update($updates);

        event(new MessageStatusUpdated($message));
    }

    protected function handleTemplateStatusUpdate(?string $wabaId, array $value): void
    {
        Log::info('Meta Template Status Update received', [
            'waba_id' => $wabaId,
            'event'   => $value['event'] ?? null,
            'template_name' => $value['message_template_name'] ?? null,
            'reason'  => $value['reason'] ?? null,
        ]);

        if (! $wabaId) {
            return;
        }

        $credential = MetaCredential::withoutGlobalScope(WorkspaceScope::class)
            ->where('waba_id', $wabaId)
            ->first();

        if ($credential) {
            $service = new CloudApiService($credential);
            $service->getMessageTemplates();
        }
    }
}
