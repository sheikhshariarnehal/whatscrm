<?php

namespace App\Livewire\Devices;

use App\Models\MetaCredential;
use App\Services\WhatsApp\CloudApiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DeviceManager extends Component
{
    public int $workspaceId;
    public ?MetaCredential $credential = null;

    public string $phone_number_id = '';
    public string $waba_id = '';
    public string $access_token = '';
    public string $verify_token = 'whatscrm_secure_token';
    public string $display_phone_number = '';

    public string $test_phone_number = '';
    public string $test_message = 'Hello from WhatsCRM! Your Meta Cloud API integration is successfully connected.';
    public ?string $test_status = null;
    public ?string $test_error = null;

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()->workspaces()->first()?->id ?? 1;
        $this->loadCredentials();
    }

    public function loadCredentials()
    {
        $this->credential = MetaCredential::where('workspace_id', $this->workspaceId)->first();

        if ($this->credential) {
            $this->phone_number_id = $this->credential->phone_number_id;
            $this->waba_id = $this->credential->waba_id;
            $this->verify_token = $this->credential->verify_token ?? 'whatscrm_secure_token';
            $this->display_phone_number = $this->credential->display_phone_number ?? '';
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
