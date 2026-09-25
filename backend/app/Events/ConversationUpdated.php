<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Conversation $conversation;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation->load(['contact', 'tags', 'assignedMember.user']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('workspace.' . $this->conversation->workspace_id),
            new PrivateChannel('conversation.' . $this->conversation->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ConversationUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->conversation->id,
            'workspace_id' => $this->conversation->workspace_id,
            'status' => $this->conversation->status,
            'unread_count' => $this->conversation->unread_count,
            'last_message' => $this->conversation->last_message,
            'last_message_at' => $this->conversation->last_message_at?->toIso8601String(),
            'assigned_to' => $this->conversation->assignedMember?->user?->name,
            'kanban_order' => $this->conversation->kanban_order,
            'tags' => $this->conversation->tags->map(fn ($t) => ['id' => $t->id, 'title' => $t->title, 'color' => $t->hex_color]),
        ];
    }
}
