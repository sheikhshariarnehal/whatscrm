<?php

namespace App\Livewire\Dashboard;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MetaCredential;
use Livewire\Attributes\On;
use Livewire\Component;

class LiveStats extends Component
{
    public ?int $workspaceId = null;

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? 1;
    }

    public function render()
    {
        $totalContacts = Contact::count();
        $activeConversations = Conversation::where('status', 'open')->count();
        $totalMessagesSent = Message::where('direction', 'outbound')->count();
        $totalMessagesReceived = Message::where('direction', 'inbound')->count();

        $metaAccount = MetaCredential::first();
        $recentConversations = Conversation::with(['contact', 'tags'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.dashboard.live-stats', [
            'totalContacts' => $totalContacts,
            'activeConversations' => $activeConversations,
            'totalMessagesSent' => $totalMessagesSent,
            'totalMessagesReceived' => $totalMessagesReceived,
            'metaAccount' => $metaAccount,
            'recentConversations' => $recentConversations,
        ]);
    }

    #[On('echo:workspace.{workspaceId},NewMessageReceived')]
    #[On('echo:workspace.{workspaceId},ConversationUpdated')]
    public function refreshStats()
    {
        // Automatically triggers re-render when WebSocket events arrive
    }
}
