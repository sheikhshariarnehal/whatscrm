<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load(['conversation.contact', 'sentByUser']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('workspace.' . $this->message->workspace_id),
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NewMessageReceived';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'workspace_id' => $this->message->workspace_id,
            'conversation_id' => $this->message->conversation_id,
            'direction' => $this->message->direction,
            'type' => $this->message->type,
            'content' => $this->message->content,
            'media_url' => $this->message->media_url,
            'status' => $this->message->status,
            'sender_name' => $this->message->conversation->sender_name ?? $this->message->conversation->sender_mobile,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
