<?php

namespace App\Livewire\Developer;

use App\Models\WebhookEndpoint;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class DeveloperHub extends Component
{
    use WithPagination;

    public string $activeTab = 'tokens'; // 'tokens', 'webhooks', 'logs', 'docs'

    // Token creation
    public bool $showTokenModal = false;
    public string $tokenName = '';
    public ?string $newlyCreatedToken = null;

    // Webhook creation
    public bool $showWebhookModal = false;
    public string $webhookName = '';
    public string $webhookUrl = '';
    public array $webhookEvents = [
        'message.received' => true,
        'message.status' => true,
        'contact.created' => false,
        'campaign.completed' => false,
    ];
    public string $webhookSecret = '';

    // Log inspection modal
    public bool $showPayloadModal = false;
    public ?array $inspectedPayload = null;

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // --- API TOKENS ---
    public function openTokenModal()
    {
        $this->reset(['tokenName', 'newlyCreatedToken']);
        $this->showTokenModal = true;
    }

    public function createToken()
    {
        $this->validate([
            'tokenName' => 'required|string|max:60',
        ]);

        $user = auth()->user();
        $token = $user->createToken($this->tokenName, ['*']);

        $this->newlyCreatedToken = $token->plainTextToken;
        session()->flash('success', 'API Token generated! Copy it now as it will not be shown again.');
    }

    public function revokeToken(int $tokenId)
    {
        auth()->user()->tokens()->where('id', $tokenId)->delete();
        session()->flash('info', 'API Token revoked.');
    }

    // --- WEBHOOKS ---
    public function openWebhookModal()
    {
        $this->reset(['webhookName', 'webhookUrl']);
        $this->webhookSecret = Str::random(32);
        $this->showWebhookModal = true;
    }

    public function saveWebhook()
    {
        $this->validate([
            'webhookName' => 'required|string|max:100',
            'webhookUrl' => 'required|url|max:255',
        ]);

        $workspace = auth()->user()->currentWorkspace();
        $selectedEvents = array_keys(array_filter($this->webhookEvents));

        WebhookEndpoint::create([
            'workspace_id' => $workspace->id ?? 1,
            'name' => $this->webhookName,
            'url' => $this->webhookUrl,
            'secret' => $this->webhookSecret,
            'events' => $selectedEvents,
            'is_active' => true,
        ]);

        $this->showWebhookModal = false;
        session()->flash('success', 'Webhook endpoint registered!');
    }

    public function toggleWebhook(int $id)
    {
        $endpoint = WebhookEndpoint::find($id);
        if ($endpoint) {
            $endpoint->update(['is_active' => !$endpoint->is_active]);
        }
    }

    public function deleteWebhook(int $id)
    {
        $endpoint = WebhookEndpoint::find($id);
        if ($endpoint) {
            $endpoint->delete();
            session()->flash('info', 'Webhook deleted.');
        }
    }

    public function testWebhook(int $id)
    {
        $endpoint = WebhookEndpoint::find($id);
        if (!$endpoint) return;

        $payload = [
            'event' => 'ping.test',
            'timestamp' => now()->toIso8601String(),
            'workspace_id' => $endpoint->workspace_id,
            'data' => [
                'message' => 'WhatsCRM Webhook Test Ping',
                'status' => 'verified',
            ],
        ];

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-WhatsCRM-Signature' => hash_hmac('sha256', json_encode($payload), $endpoint->secret ?? ''),
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint->url, $payload);

            WebhookLog::create([
                'workspace_id' => $endpoint->workspace_id,
                'webhook_endpoint_id' => $endpoint->id,
                'direction' => 'outbound',
                'event' => 'ping.test',
                'payload' => $payload,
                'response_status' => $response->status(),
                'response_body' => Str::limit($response->body(), 500),
            ]);

            session()->flash('success', "Test ping dispatched! Response code: {$response->status()}");
        } catch (\Throwable $e) {
            WebhookLog::create([
                'workspace_id' => $endpoint->workspace_id,
                'webhook_endpoint_id' => $endpoint->id,
                'direction' => 'outbound',
                'event' => 'ping.test',
                'payload' => $payload,
                'response_status' => 500,
                'response_body' => $e->getMessage(),
            ]);

            session()->flash('error', "Webhook ping failed: {$e->getMessage()}");
        }
    }

    // --- LOGS ---
    public function inspectLog(int $logId)
    {
        $log = WebhookLog::find($logId);
        if ($log) {
            $this->inspectedPayload = [
                'id' => $log->id,
                'event' => $log->event,
                'direction' => $log->direction,
                'status' => $log->response_status,
                'payload' => $log->payload,
                'response' => $log->response_body,
                'time' => $log->created_at->toDateTimeString(),
            ];
            $this->showPayloadModal = true;
        }
    }

    public function render()
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace();

        $tokens = $user->tokens()->latest()->get();
        $webhooks = WebhookEndpoint::where('workspace_id', $workspace->id ?? 1)->latest()->get();
        $logs = WebhookLog::where('workspace_id', $workspace->id ?? 1)->latest()->paginate(15);

        return view('livewire.developer.developer-hub', [
            'tokens' => $tokens,
            'webhooks' => $webhooks,
            'logs' => $logs,
        ])->layout('layouts.app');
    }
}
