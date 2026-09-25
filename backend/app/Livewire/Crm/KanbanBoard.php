<?php

namespace App\Livewire\Crm;

use App\Events\ConversationUpdated;
use App\Models\Conversation;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class KanbanBoard extends Component
{
    public int $workspaceId;
    public string $search = '';
    public bool $showCreateTagModal = false;
    public string $newTagTitle = '';
    public string $newTagColor = '#2a85ff';

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()->workspaces()->first()?->id ?? 1;
    }

    public function moveConversation(int $conversationId, int $targetTagId)
    {
        $conversation = Conversation::where('workspace_id', $this->workspaceId)->find($conversationId);
        $targetTag = Tag::where('workspace_id', $this->workspaceId)->find($targetTagId);

        if (! $conversation || ! $targetTag) {
            return;
        }

        // Remove all current kanban stage tags from this conversation
        $kanbanTagIds = Tag::where('workspace_id', $this->workspaceId)
            ->where('show_on_kanban', true)
            ->pluck('id');

        $conversation->tags()->detach($kanbanTagIds);

        // Attach target stage tag
        $conversation->tags()->attach($targetTagId);

        event(new ConversationUpdated($conversation));
    }

    public function createStage()
    {
        $this->validate([
            'newTagTitle' => 'required|string|max:50',
            'newTagColor' => 'required|string|max:20',
        ]);

        Tag::create([
            'workspace_id' => $this->workspaceId,
            'title' => $this->newTagTitle,
            'hex_color' => $this->newTagColor,
            'show_on_kanban' => true,
        ]);

        $this->newTagTitle = '';
        $this->newTagColor = '#2a85ff';
        $this->showCreateTagModal = false;
    }

    #[On('echo:workspace.{workspaceId},ConversationUpdated')]
    #[On('echo:workspace.{workspaceId},NewMessageReceived')]
    public function onRealtimeUpdate()
    {
        // Automatically re-renders Kanban cards on WebSocket events
    }

    public function render()
    {
        // Retrieve kanban stages
        $stages = Tag::where('workspace_id', $this->workspaceId)
            ->where('show_on_kanban', true)
            ->get();

        // Retrieve conversations mapped to stages
        $query = Conversation::where('workspace_id', $this->workspaceId)
            ->with(['contact', 'tags']);

        if (! empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('sender_name', 'like', $search)
                  ->orWhere('sender_mobile', 'like', $search)
                  ->orWhere('last_message', 'like', $search);
            });
        }

        $conversations = $query->orderBy('updated_at', 'desc')->get();

        // Group conversations by stage ID
        $stageConversations = [];
        foreach ($stages as $stage) {
            $stageConversations[$stage->id] = $conversations->filter(function ($conv) use ($stage) {
                return $conv->tags->contains('id', $stage->id);
            });
        }

        // Also track unassigned/uncategorized conversations
        $uncategorized = $conversations->filter(function ($conv) use ($stages) {
            $stageIds = $stages->pluck('id');
            return ! $conv->tags->contains(function ($tag) use ($stageIds) {
                return $stageIds->contains($tag->id);
            });
        });

        return view('livewire.crm.kanban-board', [
            'stages' => $stages,
            'stageConversations' => $stageConversations,
            'uncategorized' => $uncategorized,
        ]);
    }
}
