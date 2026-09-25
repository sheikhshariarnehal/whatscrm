<?php

namespace App\Livewire\Team;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\WorkspaceMember;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class TeamManager extends Component
{
    use WithPagination;

    public string $activeTab = 'members'; // 'members', 'permissions', 'performance'

    // Invite / Add Agent Form
    public bool $showInviteModal = false;
    public string $inviteName = '';
    public string $inviteEmail = '';
    public string $invitePassword = '';
    public string $inviteRole = 'agent';

    // Edit Member Form
    public bool $showEditModal = false;
    public ?int $editingMemberId = null;
    public string $editRole = 'agent';
    public bool $editIsActive = true;

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function openInviteModal()
    {
        $this->reset(['inviteName', 'inviteEmail', 'invitePassword']);
        $this->inviteRole = 'agent';
        $this->showInviteModal = true;
    }

    public function inviteMember()
    {
        $this->validate([
            'inviteName' => 'required|string|max:100',
            'inviteEmail' => 'required|email|max:255',
            'invitePassword' => 'required|string|min:8',
            'inviteRole' => 'required|in:admin,agent',
        ]);

        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) {
            return;
        }

        // Find or create User
        $user = User::firstOrCreate(
            ['email' => $this->inviteEmail],
            [
                'name' => $this->inviteName,
                'password' => Hash::make($this->invitePassword),
            ]
        );

        // Check if already a member of this workspace
        $existing = WorkspaceMember::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $this->addError('inviteEmail', 'User is already a member of this workspace.');
            return;
        }

        WorkspaceMember::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => $this->inviteRole,
            'permissions' => [
                'can_view_all_chats' => $this->inviteRole === 'admin',
                'can_send_broadcasts' => $this->inviteRole === 'admin',
                'can_export_contacts' => $this->inviteRole === 'admin',
                'can_manage_automations' => $this->inviteRole === 'admin',
            ],
            'is_active' => true,
        ]);

        $this->showInviteModal = false;
        session()->flash('success', "Team member {$this->inviteName} added successfully!");
    }

    public function editMember(int $memberId)
    {
        $member = WorkspaceMember::find($memberId);
        if ($member) {
            $this->editingMemberId = $member->id;
            $this->editRole = $member->role;
            $this->editIsActive = $member->is_active;
            $this->showEditModal = true;
        }
    }

    public function updateMember()
    {
        $member = WorkspaceMember::find($this->editingMemberId);
        if ($member && $member->role !== 'owner') {
            $member->update([
                'role' => $this->editRole,
                'is_active' => $this->editIsActive,
            ]);
            $this->showEditModal = false;
            session()->flash('success', 'Member updated.');
        }
    }

    public function toggleMemberStatus(int $memberId)
    {
        $member = WorkspaceMember::find($memberId);
        if ($member && $member->role !== 'owner') {
            $member->update(['is_active' => !$member->is_active]);
        }
    }

    public function removeMember(int $memberId)
    {
        $member = WorkspaceMember::find($memberId);
        if ($member && $member->role !== 'owner') {
            $member->delete();
            session()->flash('info', 'Team member removed from workspace.');
        }
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace();
        $members = WorkspaceMember::with('user')
            ->where('workspace_id', $workspace->id ?? 1)
            ->get();

        // Calculate agent performance stats
        $stats = [];
        foreach ($members as $member) {
            $assignedChats = Conversation::where('workspace_id', $workspace->id ?? 1)
                ->where('assigned_member_id', $member->id)
                ->count();

            $messagesSent = Message::where('workspace_id', $workspace->id ?? 1)
                ->where('sent_by_user_id', $member->user_id)
                ->count();

            $stats[$member->id] = [
                'assigned_chats' => $assignedChats,
                'messages_sent' => $messagesSent,
            ];
        }

        return view('livewire.team.team-manager', [
            'members' => $members,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
