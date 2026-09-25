<?php

namespace App\Livewire\Inbox;

use App\Events\ConversationUpdated;
use App\Events\NewMessageReceived;
use App\Models\Conversation;
use App\Models\ConversationNote;
use App\Models\Message;
use App\Models\Tag;
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
    public string $statusFilter = 'open';
    public ?int $selectedConversationId = null;
    public string $messageBody = '';
    public string $internalNoteBody = '';
    public ?int $selectedTagId = null;

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
            'channel' => $conversation->channel ?? 'whatsapp_cloud',
            'sent_by_user_id' => $user->id,
        ]);

        // 2. Dispatch via Meta WhatsApp Cloud API
        $service = CloudApiService::forWorkspace($this->workspaceId);
        if ($service) {
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
            // Simulated sent for local development or manual test mode
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
        // Automatically refreshes the component reactive state
    }

    public function render()
    {
        $query = Conversation::where('workspace_id', $this->workspaceId)
            ->with(['contact', 'tags']);

        if ($this->statusFilter === 'unread') {
            $query->where('unread_count', '>', 0);
        } elseif (in_array($this->statusFilter, ['open', 'pending', 'closed'])) {
            $query->where('status', $this->statusFilter);
        }

        if (! empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('sender_name', 'like', $search)
                  ->orWhere('sender_mobile', 'like', $search)
                  ->orWhere('last_message', 'like', $search);
            });
        }

        $conversations = $query->orderBy('updated_at', 'desc')->get();

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

        return view('livewire.inbox.inbox-page', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'activeMessages' => $activeMessages,
            'conversationNotes' => $conversationNotes,
            'availableTags' => $availableTags,
        ]);
    }
}
