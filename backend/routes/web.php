<?php

use App\Models\Workspace;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    // Workspace Switcher
    Route::post('/workspace/switch/{workspace}', function (Workspace $workspace) {
        $user = auth()->user();
        if ($user->workspaces()->where('workspaces.id', $workspace->id)->where('workspace_members.is_active', true)->exists()) {
            session(['current_workspace_id' => $workspace->id]);
        }
        return back();
    })->name('workspace.switch');

    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // CRM Modules
    Route::get('inbox', \App\Livewire\Inbox\InboxPage::class)->name('inbox');
    Route::get('devices', \App\Livewire\Devices\DeviceManager::class)->name('devices');
    Route::get('crm', \App\Livewire\Crm\KanbanBoard::class)->name('crm');
    Route::get('contacts', \App\Livewire\Contacts\ContactTable::class)->name('contacts');
    Route::get('campaigns', \App\Livewire\Campaigns\CampaignManager::class)->name('campaigns');
    Route::get('automations', \App\Livewire\Automations\AutomationHub::class)->name('automations');
    Route::get('team', \App\Livewire\Team\TeamManager::class)->name('team');
    Route::get('developer', \App\Livewire\Developer\DeveloperHub::class)->name('developer');
    Route::get('settings', \App\Livewire\Settings\WorkspaceSettings::class)->name('settings');

    // User Profile
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
