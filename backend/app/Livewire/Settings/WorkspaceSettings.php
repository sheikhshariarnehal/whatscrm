<?php

namespace App\Livewire\Settings;

use App\Models\Contact;
use App\Models\Message;
use App\Models\Plan;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class WorkspaceSettings extends Component
{
    use WithFileUploads;

    public string $activeTab = 'company'; // 'company', 'security', 'notifications', 'billing'

    // Company profile
    public string $companyName = '';
    public string $timezone = 'UTC';
    public string $countryCode = '+1';
    public $logo;
    public ?string $existingLogoUrl = null;

    // Security
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPassword_confirmation = '';

    // Notifications & Sounds
    public bool $soundEnabled = true;
    public bool $browserPushEnabled = true;
    public bool $campaignDigestEnabled = true;

    public function mount()
    {
        $workspace = auth()->user()->currentWorkspace();
        if ($workspace) {
            $this->companyName = $workspace->name;
            $this->timezone = $workspace->timezone ?? 'UTC';
            $this->countryCode = $workspace->settings['country_code'] ?? '+1';
            $this->existingLogoUrl = $workspace->settings['logo_url'] ?? null;

            $notifs = $workspace->settings['notifications'] ?? [];
            $this->soundEnabled = $notifs['sound'] ?? true;
            $this->browserPushEnabled = $notifs['browser_push'] ?? true;
            $this->campaignDigestEnabled = $notifs['campaign_digest'] ?? true;
        }
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function saveCompanySettings()
    {
        $this->validate([
            'companyName' => 'required|string|max:100',
            'timezone' => 'required|string|max:100',
            'countryCode' => 'required|string|max:10',
            'logo' => 'nullable|image|max:2048',
        ]);

        $workspace = auth()->user()->currentWorkspace();
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['country_code'] = $this->countryCode;

            if ($this->logo) {
                $path = $this->logo->store('logos', 'public');
                $settings['logo_url'] = '/storage/' . $path;
                $this->existingLogoUrl = $settings['logo_url'];
            }

            $workspace->update([
                'name' => $this->companyName,
                'timezone' => $this->timezone,
                'settings' => $settings,
            ]);

            session()->flash('success', 'Company profile updated successfully!');
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'The current password you provided does not match our records.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset(['currentPassword', 'newPassword', 'newPassword_confirmation']);
        session()->flash('success', 'Your password has been changed securely.');
    }

    public function saveNotificationSettings()
    {
        $workspace = auth()->user()->currentWorkspace();
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['notifications'] = [
                'sound' => $this->soundEnabled,
                'browser_push' => $this->browserPushEnabled,
                'campaign_digest' => $this->campaignDigestEnabled,
            ];

            $workspace->update(['settings' => $settings]);
            session()->flash('success', 'Notification preferences saved.');
        }
    }

    public function changePlan(int $planId)
    {
        $workspace = auth()->user()->currentWorkspace();
        $plan = Plan::find($planId);

        if ($workspace && $plan) {
            $workspace->update([
                'plan_id' => $plan->id,
                'plan_expires_at' => now()->addMonth(),
            ]);

            session()->flash('success', "Workspace upgraded to the {$plan->name} plan!");
        }
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace();
        $currentPlan = $workspace ? $workspace->plan : null;
        $availablePlans = Plan::where('is_active', true)->get();

        // Usage stats
        $totalContacts = Contact::count();
        $totalMembers = WorkspaceMember::where('workspace_id', $workspace->id ?? 1)->count();
        $messagesThisMonth = Message::where('direction', 'outbound')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $planLimits = $currentPlan->limits ?? [
            'messages_per_month' => 50000,
            'contacts' => 10000,
            'seats' => 5,
        ];

        return view('livewire.settings.workspace-settings', [
            'workspace' => $workspace,
            'currentPlan' => $currentPlan,
            'availablePlans' => $availablePlans,
            'totalContacts' => $totalContacts,
            'totalMembers' => $totalMembers,
            'messagesThisMonth' => $messagesThisMonth,
            'planLimits' => $planLimits,
        ])->layout('layouts.app');
    }
}
