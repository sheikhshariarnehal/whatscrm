<?php

namespace App\Livewire\Inbox;

use App\Events\ConversationUpdated;
use App\Events\NewMessageReceived;
use App\Models\Conversation;
use App\Models\ConversationNote;
use App\Models\Message;
use App\Models\QuickReply;
use App\Models\Tag;
use App\Models\WorkspaceMember;
use App\Services\WhatsApp\CloudApiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class InboxPage extends Component
{
    public int $workspaceId;
    public string $search = '';
    public string $statusFilter = 'open'; // 'open', 'unread', 'closed', 'all'
    public string $channelFilter = 'all'; // 'all', 'whatsapp', 'instagram', 'telegram', 'messenger', 'unassigned'
    public ?int $selectedConversationId = null;
    public string $messageBody = '';
    public string $internalNoteBody = '';
    public ?int $selectedTagId = null;

    // AI Smart Reply suggestions
    public array $smartReplies = [
        'Hi there! I would be delighted to assist you with pricing and custom packages.',
        'Thank you for reaching out! Our team is reviewing your request and will get back shortly.',
        'Sure thing! Here is a link to our full catalog and enterprise specifications.',
    ];

    // Pre-approved WhatsApp Templates
    public array $templates = [
        [
            'name' => 'order_confirmation_v2',
            'title' => 'Order Confirmation',
            'category' => 'Utility',
            'body' => 'Hi {{name}}, thank you for your order! Your order has been confirmed and is now being prepared for dispatch.',
            'badge' => 'Pre-approved',
        ],
        [
            'name' => 'pricing_quote_followup',
            'title' => 'Special Pricing Offer',
            'category' => 'Marketing',
            'body' => 'Hello {{name}}, here is the custom quote we discussed for your team with an exclusive 20% discount valid for 48 hours.',
            'badge' => 'High Conversion',
        ],
        [
            'name' => 'appointment_reminder',
            'title' => 'Appointment Reminder',
            'category' => 'Utility',
            'body' => 'Hi {{name}}, this is a friendly reminder for your upcoming onboarding call with our technical team.',
            'badge' => 'Automated',
        ],
        [
            'name' => 'customer_feedback_survey',
            'title' => 'Customer Feedback',
            'category' => 'Service',
            'body' => 'Hi {{name}}, how was your recent support experience with WhatsCRM? Please rate your experience!',
            'badge' => 'CSAT',
        ],
    ];

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()->workspaces()->first()?->id ?? 1;

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

        $conversation = Conversation::find($id);
        if ($conversation && $conversation->unread_count > 0) {
            $conversation->update(['unread_count' => 0]);
            
            // Mark read on WhatsApp Cloud API
            $lastInbound = $conversation->messages()->where('direction', 'inbound')->latest()->first();
            if ($lastInbound && $lastInbound->external_id) {
                $service = CloudApiService::forWorkspace($this->workspaceId);
                if ($service) {
                    $service->markAsRead($lastInbound->external_id);
                }
            }
        }
    }

    public function applySmartReply(string $text)
    {
        $this->messageBody = $text;
    }

    public function sendMessage()
    {
        $this->validate([
            'messageBody' => 'required|string|min:1',
        ]);

        if (! $this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selectedConversationId);
        $user = Auth::user();

        // 1. Create Outbound Message in DB
        $message = Message::create([
            'workspace_id' => $this->workspaceId,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'text',
            'content' => $this->messageBody,
            'status' => 'pending',
            'channel' => $conversation->channel ?? 'whatsapp',
            'sent_by_user_id' => $user->id,
        ]);

        // 2. Dispatch via Meta WhatsApp Cloud API if WhatsApp channel
        $service = CloudApiService::forWorkspace($this->workspaceId);
        if ($service && in_array($conversation->channel ?? 'whatsapp', ['whatsapp', 'whatsapp_cloud'])) {
            $result = $service->sendTextMessage($conversation->sender_mobile ?? $conversation->chat_id, $this->messageBody);
            if ($result['success'] ?? false) {
                $externalId = $result['data']['messages'][0]['id'] ?? null;
                $message->update([
                    'status' => 'sent',
                    'external_id' => $externalId,
                ]);
            } else {
                $message->update([
                    'status' => 'failed',
                    'metadata' => ['error' => $result['error'] ?? 'Sending failed'],
                ]);
            }
        } else {
            // Simulated instant sent for dev or other channels
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

    public function sendTemplateMessage(string $templateName)
    {
        if (! $this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selectedConversationId);
        $user = Auth::user();

        $selected = collect($this->templates)->firstWhere('name', $templateName);
        $templateTitle = $selected['title'] ?? $templateName;
        $content = "📄 **Meta Template: {$templateTitle}**\n\n" . ($selected['body'] ?? "Template [{$templateName}] dispatched to {$conversation->sender_name}");

        $message = Message::create([
            'workspace_id' => $this->workspaceId,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'template',
            'content' => $content,
            'status' => 'sent',
            'channel' => $conversation->channel ?? 'whatsapp',
            'sent_by_user_id' => $user->id,
            'metadata' => ['template' => $templateName],
        ]);

        $conversation->update([
            'last_message' => "📄 {$templateTitle}",
            'last_message_at' => now(),
        ]);

        event(new NewMessageReceived($message));
        event(new ConversationUpdated($conversation));
        $this->dispatch('message-sent');
    }

    public function sendVoiceNote()
    {
        if (! $this->selectedConversationId) {
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
            'channel' => $conversation->channel ?? 'whatsapp',
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

    public function sendMediaAttachment(string $type = 'image')
    {
        if (! $this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selectedConversationId);
        $user = Auth::user();

        $payload = $type === 'document' ? [
            'type' => 'document',
            'content' => 'Product_Catalog_2026.pdf',
            'media_url' => '#',
            'caption' => '📄 Product_Catalog_2026.pdf (3.4 MB)',
            'last_msg' => '📄 Document: Product_Catalog_2026.pdf',
        ] : [
            'type' => 'image',
            'content' => 'Catalog_Preview.png',
            'media_url' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&w=600&q=80',
            'caption' => 'Here is the product catalog preview you requested! Let me know if you have questions.',
            'last_msg' => '📷 Photo: Catalog_Preview.png',
        ];

        $message = Message::create([
            'workspace_id' => $this->workspaceId,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => $payload['type'],
            'content' => $payload['content'],
            'media_url' => $payload['media_url'],
            'caption' => $payload['caption'],
            'status' => 'sent',
            'channel' => $conversation->channel ?? 'whatsapp',
            'sent_by_user_id' => $user->id,
        ]);

        $conversation->update([
            'last_message' => $payload['last_msg'],
            'last_message_at' => now(),
        ]);

        event(new NewMessageReceived($message));
        event(new ConversationUpdated($conversation));
        $this->dispatch('message-sent');
    }

    public function updateStatus(string $status)
    {
        if (! $this->selectedConversationId || ! in_array($status, ['open', 'pending', 'closed'])) {
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
        if (! $this->selectedConversationId) {
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
        if (! $this->selectedConversationId) {
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

        if (! $this->selectedConversationId) {
            return;
        }

        ConversationNote::create([
            'workspace_id' => $this->workspaceId,
            'conversation_id' => $this->selectedConversationId,
            'user_id' => Auth::id(),
            'note' => $this->internalNoteBody,
        ]);

        $this->internalNoteBody = '';
    }

    public function attachTag(int $tagId)
    {
        if (! $this->selectedConversationId) {
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
        if (! $this->selectedConversationId) {
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

    public function render()
    {
        $query = Conversation::where('workspace_id', $this->workspaceId)
            ->with(['contact', 'tags', 'assignedMember.user']);

        // Channel filter
        if ($this->channelFilter === 'unassigned') {
            $query->whereNull('assigned_member_id');
        } elseif (in_array($this->channelFilter, ['whatsapp', 'instagram', 'telegram', 'messenger'])) {
            $query->where('channel', $this->channelFilter);
        }

        // Status filter
        if ($this->statusFilter === 'unread') {
            $query->where('unread_count', '>', 0);
        } elseif (in_array($this->statusFilter, ['open', 'pending', 'closed'])) {
            $query->where('status', $this->statusFilter);
        }

        // Search
        if (! empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('sender_name', 'like', $search)
                  ->orWhere('sender_mobile', 'like', $search)
                  ->orWhere('last_message', 'like', $search);
            });
        }

        $conversations = $query->orderBy('updated_at', 'desc')->get();

        // Channel counts
        $channelCounts = [
            'all' => Conversation::where('workspace_id', $this->workspaceId)->count(),
            'whatsapp' => Conversation::where('workspace_id', $this->workspaceId)->where(function($q) {
                $q->where('channel', 'whatsapp')->orWhere('channel', 'whatsapp_cloud');
            })->count(),
            'instagram' => Conversation::where('workspace_id', $this->workspaceId)->where('channel', 'instagram')->count(),
            'telegram' => Conversation::where('workspace_id', $this->workspaceId)->where('channel', 'telegram')->count(),
            'messenger' => Conversation::where('workspace_id', $this->workspaceId)->where('channel', 'messenger')->count(),
            'unassigned' => Conversation::where('workspace_id', $this->workspaceId)->whereNull('assigned_member_id')->count(),
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

        $availableTags = Tag::where('workspace_id', $this->workspaceId)->get();
        $teamMembers = WorkspaceMember::where('workspace_id', $this->workspaceId)->with('user')->get();
        $quickReplies = QuickReply::where('workspace_id', $this->workspaceId)->get();

        return view('livewire.inbox.inbox-page', [
            'conversations' => $conversations,
            'channelCounts' => $channelCounts,
            'selectedConversation' => $selectedConversation,
            'activeMessages' => $activeMessages,
            'conversationNotes' => $conversationNotes,
            'availableTags' => $availableTags,
            'teamMembers' => $teamMembers,
            'quickReplies' => $quickReplies,
            'templates' => $this->templates,
        ]);
    }
}
