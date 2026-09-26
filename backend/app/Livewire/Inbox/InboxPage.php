<?php

namespace App\Livewire\Inbox;

use App\Events\ConversationUpdated;
use App\Events\NewMessageReceived;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\ConversationNote;
use App\Models\Instance;
use App\Models\Message;
use App\Models\Phonebook;
use App\Models\QuickReply;
use App\Models\Tag;
use App\Models\WorkspaceMember;
use App\Services\WhatsApp\BaileysService;
use App\Services\WhatsApp\CloudApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class InboxPage extends Component
{
    use WithFileUploads;

    public int $workspaceId;
    public string $search = '';
    public string $statusFilter = 'open'; // 'open', 'unread', 'closed', 'all'
    public string $channelFilter = 'all'; // 'all', 'whatsapp', 'instagram', 'telegram', 'messenger', 'unassigned'
    public ?int $selectedConversationId = null;
    public string $messageBody = '';
    public string $internalNoteBody = '';
    public int $internalNoteRating = 5;
    public ?int $selectedTagId = null;

    // Media file upload
    public $attachment = null;
    public ?string $attachmentType = null; // 'image', 'document', 'audio'
    public string $attachmentCaption = '';

    // New Conversation Modal State
    public bool $newChatModalOpen = false;
    public string $newChatChannel = 'whatsapp_cloud'; // 'whatsapp_cloud' or 'baileys'
    public ?string $newChatInstanceId = null;
    public string $newChatMobile = '';
    public string $newChatName = '';
    public string $newChatMessage = '';
    public string $newChatTemplateName = '';
    public array $newChatTemplateVariables = [];

    // Template Dispatch Modal State
    public bool $templateModalOpen = false;
    public ?string $selectedTemplateName = null;
    public array $templateVariables = [];

    // Contact Editing in Sidebar
    public string $editingContactName = '';
    public string $editingContactEmail = '';
    public ?int $editingContactPhonebookId = null;

    // Contact Avatar Upload
    public $contactAvatar = null;
    public string $editingContactAvatarUrl = '';

    // AI Smart Reply suggestions
    public array $smartReplies = [
        'Hi there! I would be delighted to assist you with pricing and custom packages.',
        'Thank you for reaching out! Our team is reviewing your request and will get back shortly.',
        'Sure thing! Here is a link to our full catalog and enterprise specifications.',
    ];

    public function updatedNewChatName($value)
    {
        if (!empty($value) && empty($this->newChatTemplateVariables['1'])) {
            $this->newChatTemplateVariables['1'] = trim($value);
        }
    }

    public function updatedNewChatTemplateName($value)
    {
        if (!empty($value) && !empty($this->newChatName) && empty($this->newChatTemplateVariables['1'])) {
            $this->newChatTemplateVariables['1'] = trim($this->newChatName);
        }
    }

    public function getNewChatSelectedTemplateProperty(): ?array
    {
        if (empty($this->newChatTemplateName)) {
            return null;
        }
        return collect($this->templates)->firstWhere('name', $this->newChatTemplateName)
            ?? collect($this->templates)->firstWhere('id', $this->newChatTemplateName);
    }

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()->workspaces()->first()?->id ?? 1;

        // Support deep-linking from Contacts Directory (?mobile=8801... or ?chat=13)
        $mobileParam = request()->query('mobile');
        $chatParam = request()->query('chat');

        if ($mobileParam) {
            $clean = preg_replace('/[^0-9]/', '', $mobileParam);
            $conv = Conversation::where('workspace_id', $this->workspaceId)
                ->where(function ($q) use ($clean) {
                    $q->where('sender_mobile', $clean)
                      ->orWhere('chat_id', 'like', "%{$clean}%");
                })
                ->first();

            if (!$conv) {
                $contact = Contact::where('workspace_id', $this->workspaceId)->where('mobile', $clean)->first();
                $name = $contact?->name ?: $clean;
                $conv = Conversation::create([
                    'workspace_id' => $this->workspaceId,
                    'contact_id' => $contact?->id,
                    'chat_id' => "meta_{$clean}",
                    'channel' => 'whatsapp_cloud',
                    'sender_name' => $name,
                    'sender_mobile' => $clean,
                    'status' => 'open',
                    'unread_count' => 0,
                ]);
            }

            $this->selectConversation($conv->id);
            return;
        }

        if ($chatParam) {
            $conv = Conversation::where('workspace_id', $this->workspaceId)->find($chatParam);
            if ($conv) {
                $this->selectConversation($conv->id);
                return;
            }
        }

        // Auto-select first conversation if available
        $first = Conversation::where('workspace_id', $this->workspaceId)
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($first) {
            $this->selectConversation($first->id);
        }
    }

    public function setChannelFilter(string $channel)
    {
        $this->channelFilter = $channel;
    }

    public function selectConversation(int $id)
    {
        $this->selectedConversationId = $id;
        unset($this->serviceWindow);

        $conversation = Conversation::find($id);
        if ($conversation) {
            $conversation->withoutTimestamps(function () use ($conversation) {
                // Update last_inbound_at if null and inbound messages exist
                if (!$conversation->last_inbound_at) {
                    $latestInbound = $conversation->messages()->where('direction', 'inbound')->latest()->first();
                    if ($latestInbound) {
                        $conversation->update(['last_inbound_at' => $latestInbound->created_at]);
                    }
                }

                if ($conversation->unread_count > 0) {
                    $conversation->update(['unread_count' => 0]);
                }
            });

            if ($conversation->unread_count > 0) {
                // Mark read on WhatsApp Cloud API
                $lastInbound = $conversation->messages()->where('direction', 'inbound')->latest()->first();
                if ($lastInbound && $lastInbound->external_id) {
                    $service = CloudApiService::forWorkspace($this->workspaceId);
                    if ($service) {
                        $service->markAsRead($lastInbound->external_id);
                    }
                }
            }

            // Sync contact editing state
            if ($conversation->contact) {
                $this->editingContactName = $conversation->contact->name ?? '';
                $this->editingContactEmail = $conversation->contact->email ?? '';
                $this->editingContactPhonebookId = $conversation->contact->phonebook_id;
            } else {
                $this->editingContactName = $conversation->sender_name ?? '';
                $this->editingContactEmail = '';
                $this->editingContactPhonebookId = null;
            }
        }
    }

    public function refreshMessages()
    {
        // Polling fallback to keep UI in sync
    }

    /**
     * Compute the 24-hour WhatsApp Customer Service Window for the selected conversation.
     */
    #[Computed]
    public function serviceWindow(): array
    {
        if (!$this->selectedConversationId) {
            return ['is_in_window' => true, 'is_expired' => false, 'requires_template' => false, 'hours_remaining' => 24, 'label' => '24h Open'];
        }

        $conv = Conversation::find($this->selectedConversationId);
        if (!$conv) {
            return ['is_in_window' => true, 'is_expired' => false, 'requires_template' => false, 'hours_remaining' => 24, 'label' => '24h Open'];
        }

        // Window only strictly applies to Meta WhatsApp Cloud conversations
        $isMeta = in_array($conv->channel, ['whatsapp', 'whatsapp_cloud']);
        if (!$isMeta) {
            return ['is_in_window' => true, 'is_expired' => false, 'requires_template' => false, 'hours_remaining' => 24, 'label' => 'Session Active'];
        }

        $lastInboundAt = $conv->last_inbound_at;
        if (!$lastInboundAt) {
            $latestInbound = $conv->messages()->where('direction', 'inbound')->latest()->first();
            if ($latestInbound) {
                $lastInboundAt = $latestInbound->created_at;
            }
        }

        if (!$lastInboundAt) {
            // Outbound-initiated conversation without inbound reply requires template
            return [
                'is_in_window' => false,
                'is_expired' => true,
                'requires_template' => true,
                'hours_remaining' => 0,
                'label' => 'No Inbound Reply',
            ];
        }

        $windowExpiresAt = Carbon::parse($lastInboundAt)->addHours(24);

        if (now()->greaterThanOrEqualTo($windowExpiresAt)) {
            return [
                'is_in_window' => false,
                'is_expired' => true,
                'requires_template' => true,
                'hours_remaining' => 0,
                'label' => 'Window Expired',
            ];
        }

        $remainingMinutes = now()->diffInMinutes($windowExpiresAt);
        $hours = floor($remainingMinutes / 60);
        $mins = $remainingMinutes % 60;

        return [
            'is_in_window' => true,
            'is_expired' => false,
            'requires_template' => false,
            'hours_remaining' => round($remainingMinutes / 60, 1),
            'label' => "{$hours}h {$mins}m remaining",
        ];
    }

    /**
     * Switch channel of active conversation to Baileys device.
     */
    public function switchChannelToBaileys(?string $instanceId = null)
    {
        if (!$this->selectedConversationId) return;

        $conv = Conversation::find($this->selectedConversationId);
        if ($conv) {
            $instanceQuery = Instance::where('uid', (string) $this->workspaceId)->where('status', 'ACTIVE');
            $instance = $instanceId ? $instanceQuery->where('uniqueId', $instanceId)->first() : $instanceQuery->first();

            $conv->update([
                'channel' => 'baileys',
                'instance_id' => $instance?->uniqueId ?? $conv->instance_id,
            ]);

            unset($this->serviceWindow);
            event(new ConversationUpdated($conv));
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Switched to Baileys Session']);
        }
    }

    /**
     * Switch channel of active conversation to Meta Cloud API.
     */
    public function switchChannelToMeta()
    {
        if (!$this->selectedConversationId) return;

        $conv = Conversation::find($this->selectedConversationId);
        if ($conv) {
            $conv->update([
                'channel' => 'whatsapp_cloud',
            ]);

            unset($this->serviceWindow);
            event(new ConversationUpdated($conv));
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Switched to Meta WhatsApp Cloud API']);
        }
    }

    /**
     * Send standard text message.
     */
    public function sendMessage()
    {
        $this->validate([
            'messageBody' => 'required|string|min:1',
        ]);

        if (!$this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selectedConversationId);
        $user = Auth::user();

        // Guard: If conversation channel is Meta Cloud and 24h customer window is closed,
        // Meta will reject free-form text with Error 131047. Require an approved Template.
        $isMetaCloud = in_array($conversation->channel ?? 'whatsapp', ['whatsapp', 'whatsapp_cloud']);
        if ($isMetaCloud && ($this->serviceWindow['requires_template'] ?? false)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot send freeform text: 24h Meta customer window is closed. Please send an approved Template message or switch to Baileys QR session.',
            ]);
            $this->templateModalOpen = true;
            return;
        }

        // 1. Create Outbound Message in DB
        $message = Message::create([
            'workspace_id' => $this->workspaceId,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'text',
            'content' => $this->messageBody,
            'status' => 'pending',
            'channel' => $conversation->channel ?? 'whatsapp_cloud',
            'sent_by_user_id' => $user->id,
        ]);

        // 2. Dispatch via Meta WhatsApp Cloud API or Baileys device
        $dispatched = false;
        $sendError = null;

        $cloudService = CloudApiService::forWorkspace($this->workspaceId);
        if ($cloudService && in_array($conversation->channel ?? 'whatsapp', ['whatsapp', 'whatsapp_cloud'])) {
            $result = $cloudService->sendTextMessage($conversation->sender_mobile ?? $conversation->chat_id, $this->messageBody);
            if ($result['success'] ?? false) {
                $externalId = $result['data']['messages'][0]['id'] ?? null;
                $message->update([
                    'status' => 'sent',
                    'external_id' => $externalId,
                ]);
                $dispatched = true;
            } else {
                $sendError = $result['error'] ?? 'Cloud API sending failed';
            }
        }

        // If not sent via Cloud API, check for active Baileys session
        if (!$dispatched) {
            $instanceQuery = Instance::where('uid', (string) $this->workspaceId)->where('status', 'ACTIVE');
            $instance = !empty($conversation->instance_id)
                ? ((clone $instanceQuery)->where('uniqueId', $conversation->instance_id)->first() ?? $instanceQuery->first())
                : $instanceQuery->first();

            if ($instance) {
                $baileys = new BaileysService();
                $targetPhone = $conversation->sender_mobile ?: $conversation->chat_id;
                $result = $baileys->sendTextMessage($instance->uniqueId, $targetPhone, $this->messageBody);

                if ($result['success'] ?? false) {
                    $message->update([
                        'status' => 'sent',
                        'channel' => 'baileys',
                        'external_id' => $result['messageId'] ?? null,
                    ]);
                    $dispatched = true;
                } else {
                    $sendError = $result['error'] ?? 'Baileys device sending failed';
                }
            }
        }

        if ($dispatched) {
            // Success
        } elseif ($sendError) {
            $message->update([
                'status' => 'failed',
                'metadata' => ['error' => $sendError, 'failed_reason' => $sendError],
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Message delivery failed: {$sendError}",
            ]);
        } else {
            $message->update(['status' => 'sent']);
        }

        // 3. Update Conversation summary
        $conversation->update([
            'last_message' => $this->messageBody,
            'last_message_at' => now(),
        ]);

        // 4. Broadcast Real-time Event
        event(new NewMessageReceived($message));
        event(new ConversationUpdated($conversation));

        $this->messageBody = '';
        $this->dispatch('message-sent');
    }

    /**
     * Send real media attachment (Image, Document, Audio).
     */
    public function sendMediaAttachment()
    {
        $this->validate([
            'attachment' => 'required|file|max:25600', // 25MB max
        ]);

        if (!$this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selectedConversationId);
        $user = Auth::user();

        // Detect type based on MIME
        $mime = $this->attachment->getMimeType();
        $originalName = $this->attachment->getClientOriginalName();
        $type = 'document';

        if (str_starts_with($mime, 'image/')) {
            $type = 'image';
        } elseif (str_starts_with($mime, 'audio/')) {
            $type = 'audio';
        } elseif (str_starts_with($mime, 'video/')) {
            $type = 'video';
        }

        // Store file publicly
        $path = $this->attachment->store('chat_media', 'public');
        $mediaUrl = asset('storage/' . $path);

        $caption = !empty($this->attachmentCaption) ? $this->attachmentCaption : ($type === 'document' ? $originalName : null);

        // 1. Create message row
        $message = Message::create([
            'workspace_id' => $this->workspaceId,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => $type,
            'content' => $originalName,
            'media_url' => $mediaUrl,
            'media_mime_type' => $mime,
            'caption' => $caption,
            'status' => 'pending',
            'channel' => $conversation->channel ?? 'whatsapp_cloud',
            'sent_by_user_id' => $user->id,
        ]);

        // 2. Dispatch
        $dispatched = false;
        $sendError = null;
        $targetPhone = $conversation->sender_mobile ?: $conversation->chat_id;

        $cloudService = CloudApiService::forWorkspace($this->workspaceId);
        if ($cloudService && in_array($conversation->channel ?? 'whatsapp', ['whatsapp', 'whatsapp_cloud'])) {
            $result = $cloudService->sendMediaMessage($targetPhone, $type, $mediaUrl, $caption);
            if ($result['success'] ?? false) {
                $externalId = $result['data']['messages'][0]['id'] ?? null;
                $message->update(['status' => 'sent', 'external_id' => $externalId]);
                $dispatched = true;
            } else {
                $sendError = $result['error'] ?? 'Meta Cloud media upload failed';
            }
        }

        if (!$dispatched) {
            $instanceQuery = Instance::where('uid', (string) $this->workspaceId)->where('status', 'ACTIVE');
            $instance = !empty($conversation->instance_id)
                ? ((clone $instanceQuery)->where('uniqueId', $conversation->instance_id)->first() ?? $instanceQuery->first())
                : $instanceQuery->first();

            if ($instance) {
                $baileys = new BaileysService();
                $result = $baileys->sendMediaMessage($instance->uniqueId, $targetPhone, $mediaUrl, $caption ?? '', $type);
                if ($result['success'] ?? false) {
                    $message->update(['status' => 'sent', 'channel' => 'baileys', 'external_id' => $result['messageId'] ?? null]);
                    $dispatched = true;
                } else {
                    $sendError = $result['error'] ?? 'Baileys media send failed';
                }
            }
        }

        if (!$dispatched && $sendError) {
            $message->update(['status' => 'failed', 'metadata' => ['error' => $sendError]]);
        } else {
            $message->update(['status' => 'sent']);
        }

        $lastSummary = match($type) {
            'image' => '📷 Photo: ' . ($caption ?: $originalName),
            'audio' => '🎤 Voice Note',
            'video' => '🎥 Video: ' . ($caption ?: $originalName),
            default => '📄 Document: ' . $originalName,
        };

        $conversation->update([
            'last_message' => $lastSummary,
            'last_message_at' => now(),
        ]);

        event(new NewMessageReceived($message));
        event(new ConversationUpdated($conversation));

        $this->reset(['attachment', 'attachmentType', 'attachmentCaption']);
        $this->dispatch('message-sent');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Attachment sent successfully!']);
    }

    /**
     * Send simulated voice note.
     */
    public function sendVoiceNote()
    {
        if (!$this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selectedConversationId);
        $user = Auth::user();

        $message = Message::create([
            'workspace_id' => $this->workspaceId,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'audio',
            'content' => 'Voice message (0:14)',
            'media_url' => 'https://actions.google.com/sounds/v1/ambiences/coffee_shop.ogg',
            'media_mime_type' => 'audio/ogg',
            'status' => 'sent',
            'channel' => $conversation->channel ?? 'whatsapp_cloud',
            'sent_by_user_id' => $user->id,
            'metadata' => ['duration' => 14],
        ]);

        $conversation->update([
            'last_message' => '🎤 Voice Note (0:14)',
            'last_message_at' => now(),
        ]);

        event(new NewMessageReceived($message));
        event(new ConversationUpdated($conversation));
        $this->dispatch('message-sent');
    }

    /**
     * Dispatch dynamic Meta template with parameter substitutions.
     */
    public function sendTemplateMessage(string $templateName, array $customVariables = [])
    {
        if (!$this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selectedConversationId);
        $user = Auth::user();

        $cloudService = CloudApiService::forWorkspace($this->workspaceId);
        $templates = $cloudService ? $cloudService->getCachedOrSavedTemplates() : CloudApiService::getDefaultTemplates();
        $selected = collect($templates)->firstWhere('name', $templateName) ?? collect($templates)->firstWhere('id', $templateName);

        $templateTitle = $selected['title'] ?? $selected['name'] ?? $templateName;
        $body = $selected['body'] ?? "Template [{$templateName}] dispatched.";
        $languageCode = $selected['language'] ?? 'en';

        // Merge passed variables with component variables
        $variables = !empty($customVariables) ? $customVariables : $this->templateVariables;

        // Auto-detect variable placeholders in template body (e.g. {{1}}, {{name}})
        preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $body, $matches);
        $requiredVars = array_values(array_unique($matches[1] ?? []));

        // If template requires variables, ensure every variable has a valid parameter
        $components = [];
        if (!empty($requiredVars)) {
            $params = [];
            foreach ($requiredVars as $vKey) {
                // If not provided, fallback to contact name or phone or 'Customer'
                $val = trim((string) ($variables[$vKey] ?? ''));
                if ($val === '') {
                    $val = $conversation->sender_name ?: ($conversation->contact?->name ?: 'Customer');
                }
                $body = str_replace("{{" . $vKey . "}}", $val, $body);
                $params[] = ['type' => 'text', 'text' => (string) $val];
            }
            $components[] = [
                'type' => 'body',
                'parameters' => $params,
            ];
        }

        $targetPhone = $conversation->sender_mobile ?: $conversation->chat_id;

        // Dispatch via Cloud API
        $dispatched = false;
        $externalId = null;
        $sendError = null;

        if ($cloudService) {
            $res = $cloudService->sendTemplateMessage($targetPhone, $templateName, $languageCode, $components);
            if ($res['success'] ?? false) {
                $externalId = $res['data']['messages'][0]['id'] ?? null;
                $dispatched = true;
            } else {
                $sendError = $res['error'] ?? 'Meta Cloud API rejected template delivery';
            }
        } else {
            $sendError = 'Meta Cloud API credentials not configured for this workspace.';
        }

        $status = $dispatched ? 'sent' : 'failed';

        $message = Message::create([
            'workspace_id' => $this->workspaceId,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'template',
            'content' => "📋 Meta Template: {$templateTitle}\n\n{$body}",
            'status' => $status,
            'external_id' => $externalId,
            'channel' => 'whatsapp_cloud',
            'sent_by_user_id' => $user->id,
            'metadata' => [
                'template' => $templateName,
                'variables' => $variables,
                'error' => $sendError,
                'failed_reason' => $sendError,
            ],
        ]);

        $conversation->update([
            'last_message' => "📋 {$templateTitle}",
            'last_message_at' => now(),
        ]);

        event(new NewMessageReceived($message));
        event(new ConversationUpdated($conversation));

        $this->templateModalOpen = false;
        $this->selectedTemplateName = null;
        $this->templateVariables = [];
        $this->dispatch('message-sent');

        if ($dispatched) {
            $this->dispatch('notify', ['type' => 'success', 'message' => "Template '{$templateTitle}' sent!"]);
        } else {
            $this->dispatch('notify', ['type' => 'error', 'message' => "Failed to send template: {$sendError}"]);
        }
    }

    /**
     * Start a brand new conversation to an arbitrary number.
     */
    public function startNewConversation()
    {
        $this->validate([
            'newChatMobile' => 'required|string|min:8',
            'newChatChannel' => 'required|in:whatsapp_cloud,baileys',
        ]);

        $cleanPhone = preg_replace('/[^0-9]/', '', $this->newChatMobile);
        $name = !empty($this->newChatName) ? trim($this->newChatName) : $cleanPhone;

        // Auto-associate with default Phonebook and Contact
        $phonebook = Phonebook::firstOrCreate(
            ['workspace_id' => $this->workspaceId, 'name' => 'Default Audience'],
            ['workspace_id' => $this->workspaceId, 'name' => 'Default Audience']
        );

        $contact = Contact::firstOrCreate(
            ['workspace_id' => $this->workspaceId, 'mobile' => $cleanPhone],
            [
                'workspace_id' => $this->workspaceId,
                'phonebook_id' => $phonebook->id,
                'name' => $name,
            ]
        );

        // Find or create conversation
        $chatId = $this->newChatChannel === 'whatsapp_cloud' ? "meta_{$cleanPhone}" : "baileys_{$cleanPhone}";
        $conversation = Conversation::firstOrCreate(
            ['workspace_id' => $this->workspaceId, 'sender_mobile' => $cleanPhone],
            [
                'workspace_id' => $this->workspaceId,
                'contact_id' => $contact->id,
                'chat_id' => $chatId,
                'channel' => $this->newChatChannel,
                'instance_id' => $this->newChatInstanceId,
                'sender_name' => $name,
                'sender_mobile' => $cleanPhone,
                'status' => 'open',
                'kanban_stage' => 'lead',
                'unread_count' => 0,
            ]
        );

        $this->selectedConversationId = $conversation->id;
        $this->selectConversation($conversation->id);

        // Send initial message if provided
        if ($this->newChatChannel === 'baileys' && !empty($this->newChatMessage)) {
            $this->messageBody = $this->newChatMessage;
            $this->sendMessage();
        } elseif ($this->newChatChannel === 'whatsapp_cloud' && !empty($this->newChatTemplateName)) {
            $vars = $this->newChatTemplateVariables;
            if (empty($vars['1']) && !empty($name)) {
                $vars['1'] = $name;
            }
            $this->sendTemplateMessage($this->newChatTemplateName, $vars);
        }

        $this->reset(['newChatModalOpen', 'newChatMobile', 'newChatName', 'newChatMessage', 'newChatTemplateName', 'newChatTemplateVariables']);
        $this->dispatch('notify', ['type' => 'success', 'message' => "New chat with {$name} opened!"]);
    }

    /**
     * Export conversation as CSV download.
     */
    public function exportConversationCsv(): StreamedResponse
    {
        $conv = Conversation::with('contact')->findOrFail($this->selectedConversationId);
        $messages = $conv->messages()->orderBy('created_at', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"chat_{$conv->sender_mobile}_" . now()->format('Ymd_His') . ".csv\"",
        ];

        return response()->stream(function () use ($messages, $conv) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Message ID', 'Direction', 'Type', 'Sender Name', 'Mobile', 'Content', 'Media URL', 'Status', 'Timestamp']);

            foreach ($messages as $msg) {
                fputcsv($handle, [
                    $msg->id,
                    $msg->direction,
                    $msg->type,
                    $msg->direction === 'outbound' ? 'Agent' : ($conv->sender_name ?? $conv->sender_mobile),
                    $conv->sender_mobile,
                    $msg->content,
                    $msg->media_url ?? '',
                    $msg->status,
                    $msg->created_at->toIso8601String(),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export conversation as JSON download.
     */
    public function exportConversationJson(): StreamedResponse
    {
        $conv = Conversation::with(['contact', 'tags', 'notes.user'])->findOrFail($this->selectedConversationId);
        $messages = $conv->messages()->orderBy('created_at', 'asc')->get();

        $data = [
            'conversation' => [
                'id' => $conv->id,
                'chat_id' => $conv->chat_id,
                'channel' => $conv->channel,
                'sender_name' => $conv->sender_name,
                'sender_mobile' => $conv->sender_mobile,
                'status' => $conv->status,
                'kanban_stage' => $conv->kanban_stage,
                'tags' => $conv->tags->pluck('title'),
                'notes' => $conv->notes->map(fn($n) => ['agent' => $n->user->name ?? 'Agent', 'note' => $n->note, 'rating' => $n->rating, 'created_at' => $n->created_at]),
            ],
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id,
                'direction' => $m->direction,
                'type' => $m->type,
                'content' => $m->content,
                'media_url' => $m->media_url,
                'status' => $m->status,
                'created_at' => $m->created_at,
            ]),
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"chat_{$conv->sender_mobile}_" . now()->format('Ymd_His') . ".json\"",
        ];

        return response()->stream(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }, 200, $headers);
    }

    /**
     * Save updated Contact information from sidebar.
     */
    public function saveContactDetails()
    {
        if (!$this->selectedConversationId) return;

        $conv = Conversation::findOrFail($this->selectedConversationId);
        $cleanPhone = preg_replace('/[^0-9]/', '', $conv->sender_mobile);

        $contact = $conv->contact;
        if (!$contact) {
            $contact = Contact::create([
                'workspace_id' => $this->workspaceId,
                'phonebook_id' => $this->editingContactPhonebookId,
                'name' => $this->editingContactName ?: ($conv->sender_name ?: $cleanPhone),
                'mobile' => $cleanPhone,
                'email' => $this->editingContactEmail ?: null,
            ]);
            $conv->update(['contact_id' => $contact->id]);
        } else {
            $contact->update([
                'name' => $this->editingContactName,
                'email' => $this->editingContactEmail ?: null,
                'phonebook_id' => $this->editingContactPhonebookId,
            ]);
        }

        $conv->update(['sender_name' => $this->editingContactName]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Contact details saved!']);
    }

    /**
     * Save a new avatar for the selected conversation's contact.
     * Accepts either a file upload ($contactAvatar) or a URL ($editingContactAvatarUrl).
     */
    public function saveContactAvatar()
    {
        if (!$this->selectedConversationId) return;

        $conv = Conversation::findOrFail($this->selectedConversationId);
        $avatarUrl = null;

        if ($this->contactAvatar) {
            $this->validate([
                'contactAvatar' => 'image|max:2048',
            ]);
            $path = $this->contactAvatar->store('contact-avatars', 'public');
            $avatarUrl = Storage::url($path);
        } elseif (!empty($this->editingContactAvatarUrl)) {
            $avatarUrl = filter_var($this->editingContactAvatarUrl, FILTER_VALIDATE_URL)
                ? $this->editingContactAvatarUrl
                : null;
        }

        if ($avatarUrl) {
            $conv->update(['profile_url' => $avatarUrl]);

            if ($conv->contact) {
                $conv->contact->update(['avatar_url' => $avatarUrl]);
            }

            $this->contactAvatar = null;
            $this->editingContactAvatarUrl = '';
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Profile photo updated!']);
        } else {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please upload a valid image or enter a valid URL.']);
        }
    }

    public function applySmartReply(string $text)
    {
        $this->messageBody = $text;
    }

    public function updateStatus(string $status)
    {
        if (!$this->selectedConversationId || !in_array($status, ['open', 'pending', 'closed'])) {
            return;
        }

        $conversation = Conversation::find($this->selectedConversationId);
        if ($conversation) {
            $conversation->update(['status' => $status]);
            event(new ConversationUpdated($conversation));
        }
    }

    public function updateKanbanStage(string $stage)
    {
        if (!$this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::find($this->selectedConversationId);
        if ($conversation) {
            $conversation->update(['kanban_stage' => $stage]);
            event(new ConversationUpdated($conversation));
        }
    }

    public function assignAgent(?int $memberId)
    {
        if (!$this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::find($this->selectedConversationId);
        if ($conversation) {
            $conversation->update(['assigned_member_id' => $memberId ?: null]);
            event(new ConversationUpdated($conversation));
        }
    }

    public function addNote()
    {
        $this->validate(['internalNoteBody' => 'required|string|min:1']);

        if (!$this->selectedConversationId) {
            return;
        }

        ConversationNote::create([
            'workspace_id' => $this->workspaceId,
            'conversation_id' => $this->selectedConversationId,
            'user_id' => Auth::id(),
            'note' => $this->internalNoteBody,
            'rating' => $this->internalNoteRating,
        ]);

        $this->internalNoteBody = '';
        $this->internalNoteRating = 5;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Internal note added!']);
    }

    public function deleteNote(int $noteId)
    {
        ConversationNote::where('workspace_id', $this->workspaceId)
            ->where('id', $noteId)
            ->delete();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Note deleted']);
    }

    public function attachTag(int $tagId)
    {
        if (!$this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::find($this->selectedConversationId);
        if ($conversation) {
            $conversation->tags()->syncWithoutDetaching([$tagId]);
            event(new ConversationUpdated($conversation));
        }
    }

    public function detachTag(int $tagId)
    {
        if (!$this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::find($this->selectedConversationId);
        if ($conversation) {
            $conversation->tags()->detach($tagId);
            event(new ConversationUpdated($conversation));
        }
    }

    #[On('echo:workspace.{workspaceId},NewMessageReceived')]
    #[On('echo:workspace.{workspaceId},MessageStatusUpdated')]
    #[On('echo:workspace.{workspaceId},ConversationUpdated')]
    public function onRealtimeUpdate()
    {
        // Automatically refreshes reactive component state
    }

    #[Computed]
    public function availableTags()
    {
        return Tag::where('workspace_id', $this->workspaceId)->get();
    }

    #[Computed]
    public function teamMembers()
    {
        return WorkspaceMember::where('workspace_id', $this->workspaceId)->with('user')->get();
    }

    #[Computed]
    public function quickReplies()
    {
        return QuickReply::where('workspace_id', $this->workspaceId)->get();
    }

    #[Computed]
    public function phonebooks()
    {
        return Phonebook::where('workspace_id', $this->workspaceId)->get();
    }

    #[Computed]
    public function activeInstances()
    {
        return Instance::where('uid', (string) $this->workspaceId)
            ->where('status', 'ACTIVE')
            ->get();
    }

    #[Computed]
    public function templates()
    {
        $cloudService = CloudApiService::forWorkspace($this->workspaceId);
        return $cloudService ? $cloudService->getCachedOrSavedTemplates() : CloudApiService::getDefaultTemplates();
    }

    public function render()
    {
        $query = Conversation::where('workspace_id', $this->workspaceId)
            ->with(['contact', 'tags', 'assignedMember.user']);

        // Channel filter
        if ($this->channelFilter === 'unassigned') {
            $query->whereNull('assigned_member_id');
        } elseif (in_array($this->channelFilter, ['whatsapp', 'instagram', 'telegram', 'messenger'])) {
            $query->where(function($q) {
                if ($this->channelFilter === 'whatsapp') {
                    $q->where('channel', 'whatsapp')->orWhere('channel', 'whatsapp_cloud')->orWhere('channel', 'baileys');
                } else {
                    $q->where('channel', $this->channelFilter);
                }
            });
        }

        // Status filter
        if ($this->statusFilter === 'unread') {
            $query->where('unread_count', '>', 0);
        } elseif (in_array($this->statusFilter, ['open', 'pending', 'closed'])) {
            $query->where('status', $this->statusFilter);
        }

        // Search
        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('sender_name', 'like', $search)
                  ->orWhere('sender_mobile', 'like', $search)
                  ->orWhere('last_message', 'like', $search);
            });
        }

        $conversations = $query->orderBy('updated_at', 'desc')->get();

        // Optimized single aggregate query for channel counts
        $channelStats = Conversation::where('workspace_id', $this->workspaceId)
            ->selectRaw("
                COUNT(*) as total_all,
                SUM(CASE WHEN channel IN ('whatsapp', 'whatsapp_cloud', 'baileys') THEN 1 ELSE 0 END) as total_whatsapp,
                SUM(CASE WHEN channel = 'instagram' THEN 1 ELSE 0 END) as total_instagram,
                SUM(CASE WHEN channel = 'telegram' THEN 1 ELSE 0 END) as total_telegram,
                SUM(CASE WHEN channel = 'messenger' THEN 1 ELSE 0 END) as total_messenger,
                SUM(CASE WHEN assigned_member_id IS NULL THEN 1 ELSE 0 END) as total_unassigned
            ")
            ->first();

        $channelCounts = [
            'all' => (int) ($channelStats->total_all ?? 0),
            'whatsapp' => (int) ($channelStats->total_whatsapp ?? 0),
            'instagram' => (int) ($channelStats->total_instagram ?? 0),
            'telegram' => (int) ($channelStats->total_telegram ?? 0),
            'messenger' => (int) ($channelStats->total_messenger ?? 0),
            'unassigned' => (int) ($channelStats->total_unassigned ?? 0),
        ];

        $selectedConversation = null;
        $activeMessages = collect();
        $conversationNotes = collect();

        if ($this->selectedConversationId) {
            $selectedConversation = Conversation::with(['contact', 'tags', 'assignedMember.user'])
                ->find($this->selectedConversationId);

            if ($selectedConversation) {
                $activeMessages = $selectedConversation->messages()->with('sentByUser')->get();
                $conversationNotes = $selectedConversation->notes()->with('user')->latest()->get();
            }
        }

        return view('livewire.inbox.inbox-page', [
            'conversations' => $conversations,
            'channelCounts' => $channelCounts,
            'selectedConversation' => $selectedConversation,
            'activeMessages' => $activeMessages,
            'conversationNotes' => $conversationNotes,
            'availableTags' => $this->availableTags,
            'teamMembers' => $this->teamMembers,
            'quickReplies' => $this->quickReplies,
            'phonebooks' => $this->phonebooks,
            'activeInstances' => $this->activeInstances,
            'templates' => $this->templates,
            'serviceWindow' => $this->serviceWindow,
        ]);
    }
}
