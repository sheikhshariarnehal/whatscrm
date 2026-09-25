<?php

namespace App\Livewire\Devices;

use App\Models\MetaCredential;
use App\Models\Workspace;
use App\Services\WhatsApp\CloudApiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DeviceManager extends Component
{
    public int $workspaceId;
    public ?MetaCredential $credential = null;
    public string $activeTab = 'qr'; // 'qr', 'meta', 'warmer', 'social'

    // Meta Cloud API Fields
    public string $phone_number_id = '';
    public string $waba_id = '';
    public string $access_token = '';
    public string $verify_token = 'whatscrm_secure_token';
    public string $display_phone_number = '';
    public ?string $app_id = '';

    // Test Message Fields
    public string $test_phone_number = '';
    public string $test_message = 'Hello from WhatsCRM! Your Meta Cloud API integration is successfully connected.';
    public ?string $test_status = null;
    public ?string $test_error = null;

    // Paired QR Sessions
    public array $pairedDevices = [];
    public bool $showPairModal = false;
    public string $pairModalTab = 'qr'; // 'qr' or 'code'
    public string $newDeviceName = '';
    public string $newDevicePhone = '';
    public string $pairingCode = 'W4K8-9M2P';

    // Number Warmer Engine Fields
    public bool $warmerEnabled = true;
    public int $minSleep = 15;
    public int $maxSleep = 45;
    public int $maxDailyPerNumber = 80;
    public string $warmerScript = 'casual_dialogue';

    // Social & Telegram Channels
    public string $telegramBotToken = '';
    public string $telegramBotUsername = '';
    public bool $telegramConnected = false;
    public bool $instagramConnected = false;
    public bool $messengerConnected = false;

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()->workspaces()->first()?->id ?? 1;
        $this->loadCredentials();
        $this->loadPairedDevices();
        $this->loadWarmerSettings();
        $this->loadSocialSettings();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function loadCredentials()
    {
        $this->credential = MetaCredential::where('workspace_id', $this->workspaceId)->first();

        if ($this->credential) {
            $this->phone_number_id = $this->credential->phone_number_id;
            $this->waba_id = $this->credential->waba_id;
            $this->verify_token = $this->credential->verify_token ?? 'whatscrm_secure_token';
            $this->display_phone_number = $this->credential->display_phone_number ?? '';
            $this->app_id = $this->credential->app_id ?? '';
        }
    }

    public function loadPairedDevices()
    {
        $workspace = Workspace::find($this->workspaceId);
        $settings = $workspace?->settings ?? [];

        if (isset($settings['paired_devices']) && is_array($settings['paired_devices'])) {
            $this->pairedDevices = $settings['paired_devices'];
        } else {
            // Default seed devices
            $this->pairedDevices = [
                [
                    'id' => 'dev_sales_01',
                    'name' => 'Sales Support Line 1',
                    'phone' => '+1 (555) 019-2834',
                    'status' => 'connected',
                    'battery' => 88,
                    'is_charging' => true,
                    'warmer_active' => true,
                    'messages_today' => 420,
                    'daily_limit' => 1000,
                    'engine' => 'Baileys v6.7.8 (MySQL Auth)',
                    'last_sync' => 'Just now',
                ],
                [
                    'id' => 'dev_mkt_02',
                    'name' => 'Marketing Outreach Line',
                    'phone' => '+1 (555) 019-8821',
                    'status' => 'connected',
                    'battery' => 94,
                    'is_charging' => false,
                    'warmer_active' => false,
                    'messages_today' => 890,
                    'daily_limit' => 1500,
                    'engine' => 'Baileys v6.7.8 (MySQL Auth)',
                    'last_sync' => '2 mins ago',
                ],
            ];
        }
    }

    public function loadWarmerSettings()
    {
        $workspace = Workspace::find($this->workspaceId);
        $settings = $workspace?->settings['warmer'] ?? [];

        $this->warmerEnabled = $settings['enabled'] ?? true;
        $this->minSleep = $settings['min_sleep'] ?? 15;
        $this->maxSleep = $settings['max_sleep'] ?? 45;
        $this->maxDailyPerNumber = $settings['max_daily'] ?? 80;
        $this->warmerScript = $settings['script'] ?? 'casual_dialogue';
    }

    public function loadSocialSettings()
    {
        $workspace = Workspace::find($this->workspaceId);
        $settings = $workspace?->settings['social'] ?? [];

        $this->telegramBotToken = $settings['telegram_token'] ?? '';
        $this->telegramBotUsername = $settings['telegram_username'] ?? '';
        $this->telegramConnected = !empty($this->telegramBotToken);
        $this->instagramConnected = $settings['instagram_connected'] ?? false;
        $this->messengerConnected = $settings['messenger_connected'] ?? false;
    }

    public function openPairModal()
    {
        $this->showPairModal = true;
        $this->newDeviceName = 'WhatsApp Line ' . (count($this->pairedDevices) + 1);
        $this->newDevicePhone = '';
        $this->pairingCode = strtoupper(substr(md5(uniqid()), 0, 4)) . '-' . strtoupper(substr(md5(uniqid()), 4, 4));
    }

    public function closePairModal()
    {
        $this->showPairModal = false;
    }

    public function setPairModalTab(string $tab)
    {
        $this->pairModalTab = $tab;
    }

    public function confirmPairing()
    {
        $this->validate([
            'newDeviceName' => 'required|string|max:50',
        ]);

        $newId = 'dev_' . uniqid();
        $this->pairedDevices[] = [
            'id' => $newId,
            'name' => $this->newDeviceName,
            'phone' => !empty($this->newDevicePhone) ? $this->newDevicePhone : '+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999),
            'status' => 'connected',
            'battery' => 100,
            'is_charging' => true,
            'warmer_active' => true,
            'messages_today' => 0,
            'daily_limit' => 1000,
            'engine' => 'Baileys v6.7.8 (MySQL Auth)',
            'last_sync' => 'Just now',
        ];

        $this->persistPairedDevices();
        $this->showPairModal = false;
        session()->flash('message', "WhatsApp device '{$this->newDeviceName}' connected successfully via QR pairing!");
    }

    public function reconnectQrDevice(string $deviceId)
    {
        foreach ($this->pairedDevices as &$device) {
            if ($device['id'] === $deviceId) {
                $device['status'] = 'connected';
                $device['last_sync'] = 'Just now';
                break;
            }
        }
        $this->persistPairedDevices();
        session()->flash('message', 'Device reconnected and session refreshed.');
    }

    public function toggleWarmerDevice(string $deviceId)
    {
        foreach ($this->pairedDevices as &$device) {
            if ($device['id'] === $deviceId) {
                $device['warmer_active'] = !$device['warmer_active'];
                break;
            }
        }
        $this->persistPairedDevices();
    }

    public function disconnectQrDevice(string $deviceId)
    {
        $this->pairedDevices = array_values(array_filter($this->pairedDevices, fn($d) => $d['id'] !== $deviceId));
        $this->persistPairedDevices();
        session()->flash('message', 'WhatsApp paired device disconnected.');
    }

    private function persistPairedDevices()
    {
        $workspace = Workspace::find($this->workspaceId);
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['paired_devices'] = $this->pairedDevices;
            $workspace->update(['settings' => $settings]);
        }
    }

    public function saveWarmerSettings()
    {
        $this->validate([
            'minSleep' => 'required|integer|min:5|max:120',
            'maxSleep' => 'required|integer|min:10|max:300',
            'maxDailyPerNumber' => 'required|integer|min:10|max:1000',
            'warmerScript' => 'required|string',
        ]);

        $workspace = Workspace::find($this->workspaceId);
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['warmer'] = [
                'enabled' => $this->warmerEnabled,
                'min_sleep' => $this->minSleep,
                'max_sleep' => $this->maxSleep,
                'max_daily' => $this->maxDailyPerNumber,
                'script' => $this->warmerScript,
            ];
            $workspace->update(['settings' => $settings]);
            session()->flash('message', 'Number warmer engine preferences saved successfully.');
        }
    }

    public function saveSocialSettings()
    {
        $workspace = Workspace::find($this->workspaceId);
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['social'] = [
                'telegram_token' => $this->telegramBotToken,
                'telegram_username' => $this->telegramBotUsername,
                'instagram_connected' => $this->instagramConnected,
                'messenger_connected' => $this->messengerConnected,
            ];
            $workspace->update(['settings' => $settings]);
            $this->telegramConnected = !empty($this->telegramBotToken);
            session()->flash('message', 'Social channel credentials updated.');
        }
    }

    public function saveCredentials()
    {
        $this->validate([
            'phone_number_id' => 'required|string|max:50',
            'waba_id' => 'required|string|max:50',
            'access_token' => $this->credential ? 'nullable|string' : 'required|string',
            'verify_token' => 'required|string|max:100',
            'display_phone_number' => 'nullable|string|max:50',
        ]);

        $data = [
            'workspace_id' => $this->workspaceId,
            'phone_number_id' => $this->phone_number_id,
            'waba_id' => $this->waba_id,
            'verify_token' => $this->verify_token,
            'display_phone_number' => $this->display_phone_number,
            'app_id' => $this->app_id,
            'status' => 'connected',
        ];

        if (! empty($this->access_token)) {
            $data['access_token'] = $this->access_token;
        }

        MetaCredential::updateOrCreate(
            ['workspace_id' => $this->workspaceId],
            $data
        );

        $this->loadCredentials();
        session()->flash('message', 'WhatsApp Cloud API credentials saved successfully.');
    }

    public function sendTestMessage()
    {
        $this->validate([
            'test_phone_number' => 'required|string|min:8',
        ]);

        $this->reset(['test_status', 'test_error']);

        $service = CloudApiService::forWorkspace($this->workspaceId);
        if (! $service) {
            $this->test_error = 'Please save your Meta credentials first.';
            return;
        }

        $result = $service->sendTextMessage($this->test_phone_number, $this->test_message);

        if ($result['success'] ?? false) {
            $this->test_status = 'Success! Test message dispatched to ' . $this->test_phone_number;
        } else {
            $this->test_error = $result['error'] ?? 'Failed to send test message via Meta API.';
        }
    }

    public function disconnect()
    {
        if ($this->credential) {
            $this->credential->update(['status' => 'disconnected']);
            $this->loadCredentials();
            session()->flash('message', 'WhatsApp account disconnected.');
        }
    }

    public function render()
    {
        $webhookCallbackUrl = url('/api/v1/webhook/whatsapp');

        return view('livewire.devices.device-manager', [
            'webhookCallbackUrl' => $webhookCallbackUrl,
        ]);
    }
}
