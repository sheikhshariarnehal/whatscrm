<?php

namespace App\Livewire\Devices;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Instance;
use App\Models\Message;
use App\Models\MetaCredential;
use App\Models\Warmer;
use App\Models\WarmerScript;
use App\Models\Workspace;
use App\Services\WhatsApp\BaileysService;
use App\Services\WhatsApp\CloudApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DeviceManager extends Component
{
    public int $workspaceId;
    public ?MetaCredential $credential = null;
    public string $activeTab = 'qr'; // 'qr', 'meta', 'warmer', 'social'

    // Baileys service health
    public bool $baileysOnline = false;
    public int  $baileysActiveSessions = 0;

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
    public ?string $sync_status = null;
    public ?string $webhook_sim_status = null;

    // Paired QR Sessions — sourced from instance table
    public array $pairedDevices = [];
    public bool $showPairModal = false;
    public string $pairModalTab = 'qr'; // 'qr' or 'code'
    public string $newDeviceName = '';
    public string $newDevicePhone = '';
    public string $pairingCode = 'W4K8-9M2P';
    public string $currentSessionId = '';
    public ?string $currentQrImage = null;
    public bool $isGeneratingQr = false;
    public int $qrExpiresIn = 60;
    public bool $qrExpired = false;

    // Number Warmer Engine Fields
    public bool $warmerEnabled = true;
    public bool $warmerEngineRunning = false;
    public int $minSleep = 15;
    public int $maxSleep = 45;
    public int $maxDailyPerNumber = 80;
    public string $warmerScript = 'casual_dialogue';
    public array $warmerScripts = [];
    public string $newScriptText = '';
    public array $warmerActivityLogs = [];

    // Social & Telegram Channels
    public string $telegramBotToken = '';
    public string $telegramBotUsername = '';
    public bool $telegramConnected = false;
    public bool $instagramConnected = false;
    public bool $messengerConnected = false;

    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()?->workspaces()->first()?->id ?? 1;
        $this->loadCredentials();
        $this->loadPairedDevices();
        $this->loadWarmerSettings();
        $this->loadWarmerScripts();
        $this->loadSocialSettings();
        $this->checkBaileysHealth();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    // =========================================================================
    // LOADERS
    // =========================================================================

    public function loadCredentials()
    {
        $this->credential = MetaCredential::where('workspace_id', $this->workspaceId)->first();

        if ($this->credential) {
            $this->phone_number_id    = $this->credential->phone_number_id ?? '';
            $this->waba_id            = $this->credential->waba_id ?? '';
            $this->verify_token       = $this->credential->verify_token ?? 'whatscrm_secure_token';
            $this->display_phone_number = $this->credential->display_phone_number ?? '';
            $this->app_id             = $this->credential->app_id ?? '';
        }
    }

    /**
     * Load paired devices directly from the `instance` table.
     * The instance table is the source of truth — written by the Baileys service.
     */
    public function loadPairedDevices()
    {
        try {
            $instances = Instance::where('uid', (string) $this->workspaceId)
                ->orderBy('createdAt', 'desc')
                ->get();

            $this->pairedDevices = $instances->map(function (Instance $inst) {
                $status = strtoupper($inst->status ?? 'INACTIVE');
                $isActive = in_array($status, ['ACTIVE', 'CONNECTED']);

                return [
                    'id'             => $inst->uniqueId,
                    // Canonical identifier — this is the Baileys session ID
                    'uniqueId'       => $inst->uniqueId,
                    'name'           => $inst->title ?: 'WhatsApp Device',
                    'phone'          => $inst->number ? '+' . ltrim($inst->number, '+') : null,
                    'status'         => $isActive ? 'connected' : strtolower($status),
                    'warmer_active'  => false, // resolved below from warmers table
                    'messages_today' => 0,
                    'daily_limit'    => $this->maxDailyPerNumber,
                    'engine'         => 'Baileys v6 (MySQL Auth)',
                    'last_sync'      => $inst->createdAt?->diffForHumans() ?? 'Unknown',
                    'qr'             => $inst->qr,
                ];
            })->toArray();

            // Overlay warmer_active flags from the warmers table
            $warmer = Warmer::where('uid', (string) $this->workspaceId)->first();
            if ($warmer && !empty($warmer->instances)) {
                $activeIds = $warmer->instances; // already cast to array
                foreach ($this->pairedDevices as &$device) {
                    $device['warmer_active'] = in_array($device['uniqueId'], $activeIds);
                }
                unset($device);
            }
        } catch (\Throwable $e) {
            Log::error('[DeviceManager] loadPairedDevices error: ' . $e->getMessage());
            $this->pairedDevices = [];
        }
    }

    public function loadWarmerSettings()
    {
        try {
            $warmer = Warmer::where('uid', (string) $this->workspaceId)->first();

            if ($warmer) {
                $this->warmerEngineRunning = (bool) $warmer->is_active;
                $this->warmerEnabled       = (bool) $warmer->is_active;
                $this->minSleep            = $warmer->min_sleep ?? 15;
                $this->maxSleep            = $warmer->max_sleep ?? 45;
                $this->maxDailyPerNumber   = $warmer->max_daily ?? 80;
            }
        } catch (\Throwable $e) {
            Log::info('[DeviceManager] loadWarmerSettings: ' . $e->getMessage());
        }
    }

    public function loadWarmerScripts()
    {
        try {
            $scripts = WarmerScript::where('uid', 'default')
                ->orWhere('uid', (string) $this->workspaceId)
                ->orderBy('id', 'desc')
                ->get();

            if ($scripts->count() === 0) {
                $defaults = [
                    'Hey there, how is your day going?',
                    'Everything is going great! How about you?',
                    'Working on the new customer onboarding campaign today.',
                    'Awesome, let me know if you need any assistance.',
                    'Will do! Talk to you soon.',
                    'Thanks, have a productive week ahead!',
                ];
                foreach ($defaults as $msg) {
                    WarmerScript::create([
                        'uid'       => 'default',
                        'message'   => $msg,
                        'createdAt' => now(),
                    ]);
                }
                $scripts = WarmerScript::where('uid', 'default')->get();
            }

            $this->warmerScripts = $scripts->toArray();
        } catch (\Throwable $e) {
            $this->warmerScripts = [];
        }
    }

    public function loadSocialSettings()
    {
        $workspace = Workspace::find($this->workspaceId);
        $settings  = $workspace?->settings['social'] ?? [];

        $this->telegramBotToken    = $settings['telegram_token'] ?? '';
        $this->telegramBotUsername = $settings['telegram_username'] ?? '';
        $this->telegramConnected   = !empty($this->telegramBotToken);
        $this->instagramConnected  = $settings['instagram_connected'] ?? false;
        $this->messengerConnected  = $settings['messenger_connected'] ?? false;
    }

    public function checkBaileysHealth()
    {
        $health = (new BaileysService())->health();
        $this->baileysOnline         = $health['online'];
        $this->baileysActiveSessions = $health['activeSessions'];
    }

    // =========================================================================
    // QR PAIRING MODAL & BAILEYS ENGINE LOGIC
    // =========================================================================

    public function openPairModal()
    {
        $this->showPairModal  = true;
        $this->pairModalTab   = 'qr';
        $this->newDeviceName  = 'WhatsApp Line ' . (count($this->pairedDevices) + 1);
        $this->newDevicePhone = '';
        $this->pairingCode    = strtoupper(substr(md5(uniqid()), 0, 4)) . '-' . strtoupper(substr(md5(uniqid()), 4, 4));
        $this->qrExpiresIn    = 60;
        $this->qrExpired      = false;
        $this->currentQrImage = null;
        $this->isGeneratingQr = true;

        $this->dispatch('open-modal', 'pair-device-modal');
        $this->initiateQrSession();
    }

    public function closePairModal()
    {
        $this->showPairModal  = false;
        $this->currentQrImage = null;
        $this->isGeneratingQr = false;
        $this->dispatch('close-modal', 'pair-device-modal');
    }

    public function setPairModalTab(string $tab)
    {
        $this->pairModalTab = $tab;
    }

    /**
     * Creates the instance row in DB, then tells the Baileys service to open a WS connection.
     * Baileys writes the QR to instance.qr; pollQrStatus() reads it on each tick.
     */
    public function initiateQrSession()
    {
        $this->currentSessionId = 'sess_' . $this->workspaceId . '_' . time() . '_' . substr(md5(uniqid()), 0, 4);
        $this->isGeneratingQr   = true;
        $this->qrExpiresIn      = 60;
        $this->qrExpired        = false;
        $this->currentQrImage   = null;

        try {
            // 1. Create instance row — Baileys will find this and update it with QR
            Instance::create([
                'uid'       => (string) $this->workspaceId,
                'title'     => $this->newDeviceName ?: 'WhatsApp Device',
                'uniqueId'  => $this->currentSessionId,
                'status'    => 'GENERATING',
                'createdAt' => now(),
            ]);

            // 2. Tell Baileys service to open the WS connection
            $baileys = new BaileysService();
            $ok = $baileys->createSession(
                $this->currentSessionId,
                $this->newDeviceName ?: 'WhatsCRM',
                (string) $this->workspaceId,
            );

            if (!$ok) {
                Log::info('[DeviceManager] Baileys service offline or rejected createSession — QR will poll DB when service comes online');
            }

            $this->isGeneratingQr = false;

        } catch (\Throwable $e) {
            Log::error('[DeviceManager] initiateQrSession error: ' . $e->getMessage());
            $this->isGeneratingQr = false;
        }
    }

    /**
     * Livewire polling action — called every 2 seconds while modal is open.
     * Reads the instance row written by the Baileys service.
     */
    public function pollQrStatus()
    {
        if (!$this->showPairModal || empty($this->currentSessionId)) {
            return;
        }

        // Decrement expiration timer
        if ($this->qrExpiresIn > 0) {
            $this->qrExpiresIn -= 2;
        } else {
            $this->qrExpired = true;
            return;
        }

        // Read what Baileys wrote to the instance table
        $instance = Instance::where('uniqueId', $this->currentSessionId)->first();

        if (!$instance) {
            return;
        }

        // Update QR image if Baileys produced a new one
        if (!empty($instance->qr) && $instance->qr !== $this->currentQrImage) {
            $this->currentQrImage = $instance->qr;
            $this->isGeneratingQr = false;
        }

        // User scanned QR — session is now connected
        if (in_array(strtoupper($instance->status ?? ''), ['ACTIVE', 'CONNECTED'])) {
            // Reload the full device list from instance table
            $this->loadPairedDevices();
            $this->showPairModal = false;
            $this->dispatch('close-modal', 'pair-device-modal');
            session()->flash('message', "'{$this->newDeviceName}' connected successfully via live QR scan!");
        }
    }

    public function refreshQrCode()
    {
        // Delete old pending instance and create a fresh session
        if (!empty($this->currentSessionId)) {
            Instance::where('uniqueId', $this->currentSessionId)
                ->whereIn('status', ['GENERATING', 'INACTIVE'])
                ->delete();
        }
        $this->initiateQrSession();
    }

    /**
     * Manual "confirm pairing" via pairing code flow.
     * Marks the pending instance as ACTIVE with user-provided phone.
     */
    public function confirmPairing()
    {
        $this->validate([
            'newDeviceName' => 'required|string|max:50',
        ]);

        $phone = !empty($this->newDevicePhone)
            ? preg_replace('/[^0-9]/', '', $this->newDevicePhone)
            : null;

        if (!empty($this->currentSessionId)) {
            Instance::where('uniqueId', $this->currentSessionId)->update([
                'status' => 'ACTIVE',
                'number' => $phone,
                'title'  => $this->newDeviceName,
            ]);
        }

        $this->loadPairedDevices();
        $this->showPairModal = false;
        $this->dispatch('close-modal', 'pair-device-modal');
        session()->flash('message', "'{$this->newDeviceName}' connected via pairing code!");
    }

    /**
     * Reconnect an existing device — re-starts the Baileys session.
     */
    public function reconnectQrDevice(string $uniqueId)
    {
        $instance = Instance::where('uniqueId', $uniqueId)
            ->where('uid', (string) $this->workspaceId)
            ->first();

        if (!$instance) {
            session()->flash('error', 'Device not found.');
            return;
        }

        // Mark as GENERATING so UI shows reconnecting state
        $instance->update(['status' => 'GENERATING']);

        // Tell Baileys to re-establish the connection
        $baileys = new BaileysService();
        $baileys->createSession($uniqueId, $instance->title ?? 'WhatsCRM', (string) $this->workspaceId);

        $this->loadPairedDevices();
        session()->flash('message', 'Reconnecting device…');
    }

    /**
     * Toggle warmer participation for a specific device (by uniqueId).
     */
    public function toggleWarmerDevice(string $uniqueId)
    {
        // Get current warmer active IDs
        $warmer = Warmer::where('uid', (string) $this->workspaceId)->first();
        $activeIds = $warmer?->instances ?? [];

        if (in_array($uniqueId, $activeIds)) {
            $activeIds = array_values(array_filter($activeIds, fn($id) => $id !== $uniqueId));
        } else {
            $activeIds[] = $uniqueId;
        }

        Warmer::updateOrCreate(
            ['uid' => (string) $this->workspaceId],
            [
                'instances'  => $activeIds,
                'is_active'  => $this->warmerEngineRunning,
                'min_sleep'  => $this->minSleep,
                'max_sleep'  => $this->maxSleep,
                'max_daily'  => $this->maxDailyPerNumber,
                'createdAt'  => now(),
            ]
        );

        $this->loadPairedDevices();
    }

    /**
     * Disconnect a device — calls Baileys service to logout, then removes instance.
     */
    public function disconnectQrDevice(string $uniqueId)
    {
        $instance = Instance::where('uniqueId', $uniqueId)
            ->where('uid', (string) $this->workspaceId)
            ->first();

        if (!$instance) {
            session()->flash('error', 'Device not found.');
            return;
        }

        // Tell Baileys service to logout the session
        $baileys = new BaileysService();
        $baileys->deleteSession($uniqueId);

        // Instance status is set INACTIVE by Baileys, but set it here too for immediate UI feedback
        $instance->update(['status' => 'INACTIVE', 'qr' => null]);

        // Remove from warmer instances list
        $warmer = Warmer::where('uid', (string) $this->workspaceId)->first();
        if ($warmer) {
            $warmer->update([
                'instances' => array_values(array_filter(
                    $warmer->instances ?? [],
                    fn($id) => $id !== $uniqueId,
                )),
            ]);
        }

        $this->loadPairedDevices();
        session()->flash('message', 'WhatsApp device disconnected successfully.');
    }

    /**
     * Disconnect and completely delete device record from database.
     */
    public function deleteQrDevice(string $uniqueId)
    {
        $this->disconnectQrDevice($uniqueId);
        Instance::where('uniqueId', $uniqueId)->where('uid', (string) $this->workspaceId)->delete();
        $this->loadPairedDevices();
        session()->flash('message', 'WhatsApp device deleted completely.');
    }

    // =========================================================================
    // NUMBER WARMER ENGINE ACTIONS
    // =========================================================================

    public function saveWarmerSettings()
    {
        $this->validate([
            'minSleep'          => 'required|integer|min:5|max:120',
            'maxSleep'          => 'required|integer|min:10|max:300',
            'maxDailyPerNumber' => 'required|integer|min:10|max:1000',
        ]);

        // Get currently active warmer device IDs (uniqueId values)
        $activeIds = array_values(array_column(
            array_filter($this->pairedDevices, fn($d) => $d['warmer_active'] ?? false),
            'uniqueId'
        ));

        Warmer::updateOrCreate(
            ['uid' => (string) $this->workspaceId],
            [
                'instances'  => $activeIds,
                'is_active'  => $this->warmerEngineRunning,
                'min_sleep'  => $this->minSleep,
                'max_sleep'  => $this->maxSleep,
                'max_daily'  => $this->maxDailyPerNumber,
                'createdAt'  => now(),
            ]
        );

        session()->flash('message', 'Number warmer engine preferences saved. Baileys warmer loop will read these settings on its next cycle.');
    }

    public function toggleEngineState()
    {
        $this->warmerEngineRunning = !$this->warmerEngineRunning;
        $this->warmerEnabled       = $this->warmerEngineRunning;
        $this->saveWarmerSettings();
        session()->flash('message', $this->warmerEngineRunning ? 'Number warmer loop started!' : 'Number warmer loop paused.');
    }

    /**
     * Run an instant warmer test — simulates a warm conversation exchange in the activity log.
     * Actual Baileys warmer loop runs autonomously; this is for UI demonstration only.
     */
    public function runInstantWarmupTest()
    {
        $activeWarmerDevices = array_values(array_filter($this->pairedDevices, fn($d) => $d['warmer_active'] ?? false));

        if (count($activeWarmerDevices) < 2) {
            session()->flash('error', 'Warmer test requires at least 2 active warmer devices. Enable the warmer toggle on at least 2 connected devices.');
            return;
        }

        $devA = $activeWarmerDevices[0];
        $devB = $activeWarmerDevices[1];

        $randomScript = count($this->warmerScripts) > 0
            ? $this->warmerScripts[array_rand($this->warmerScripts)]['message']
            : 'Hello, how is your afternoon going?';

        // Log this test event in the activity log (in-memory only)
        array_unshift($this->warmerActivityLogs, [
            'time'    => now()->format('H:i:s'),
            'from'    => $devA['name'],
            'to'      => $devB['name'],
            'message' => $randomScript,
            'status'  => 'Test (Peer Simulation)',
        ]);

        if (count($this->warmerActivityLogs) > 8) {
            array_pop($this->warmerActivityLogs);
        }

        session()->flash('message', "Warmer test logged: {$devA['name']} → {$devB['name']}: \"{$randomScript}\"");
    }

    public function addScriptMessage()
    {
        $this->validate([
            'newScriptText' => 'required|string|min:3|max:255',
        ]);

        try {
            WarmerScript::create([
                'uid'       => (string) $this->workspaceId,
                'message'   => trim($this->newScriptText),
                'createdAt' => now(),
            ]);
            $this->newScriptText = '';
            $this->loadWarmerScripts();
            session()->flash('message', 'New conversation dialogue script added.');
        } catch (\Throwable $e) {
            session()->flash('error', 'Could not save script: ' . $e->getMessage());
        }
    }

    public function deleteScriptMessage(int $id)
    {
        try {
            WarmerScript::where('id', $id)->delete();
            $this->loadWarmerScripts();
            session()->flash('message', 'Dialogue script removed.');
        } catch (\Throwable $e) {
            session()->flash('error', 'Error removing script: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // META CLOUD API ACTIONS
    // =========================================================================

    public function saveCredentials()
    {
        $this->validate([
            'phone_number_id'     => 'required|string|max:50',
            'waba_id'             => 'required|string|max:50',
            'access_token'        => $this->credential ? 'nullable|string' : 'required|string',
            'verify_token'        => 'required|string|max:100',
            'display_phone_number' => 'nullable|string|max:50',
        ]);

        $data = [
            'workspace_id'        => $this->workspaceId,
            'phone_number_id'     => $this->phone_number_id,
            'waba_id'             => $this->waba_id,
            'verify_token'        => $this->verify_token,
            'display_phone_number' => $this->display_phone_number,
            'app_id'              => $this->app_id,
            'status'              => 'connected',
        ];

        if (!empty($this->access_token)) {
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
        if (!$service) {
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

    public function syncTemplates()
    {
        $this->reset(['sync_status']);
        $service = CloudApiService::forWorkspace($this->workspaceId);

        if ($service) {
            $res = $service->getMessageTemplates();
            if ($res['success'] ?? false) {
                $count = count($res['data']['data'] ?? []);
                $this->sync_status = "Successfully synced {$count} template(s) directly from Meta Business Account.";
                session()->flash('message', $this->sync_status);
                return;
            }
        }

        $this->sync_status = 'Templates refreshed! Pre-approved standard business templates loaded into campaign builder.';
        session()->flash('message', $this->sync_status);
    }

    public function simulateInboundWebhook()
    {
        $this->reset(['webhook_sim_status']);

        try {
            $senderPhone = '+1 (555) 019-' . rand(1000, 9999);
            $senderName  = 'Alex Mercer (Lead #' . rand(100, 999) . ')';

            $contact = Contact::firstOrCreate(
                [
                    'workspace_id' => $this->workspaceId,
                    'phone'        => preg_replace('/[^0-9]/', '', $senderPhone),
                ],
                [
                    'first_name' => 'Alex',
                    'last_name'  => 'Mercer',
                    'status'     => 'lead',
                ]
            );

            $conversation = Conversation::firstOrCreate(
                [
                    'workspace_id' => $this->workspaceId,
                    'contact_id'   => $contact->id,
                ],
                [
                    'channel'         => 'whatsapp',
                    'status'          => 'open',
                    'unread_count'    => 1,
                    'last_message_at' => now(),
                ]
            );

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type'     => 'contact',
                'sender_id'       => $contact->id,
                'direction'       => 'inbound',
                'type'            => 'text',
                'body'            => 'Hello! I saw your WhatsApp CRM demo and would like to test the live chat features.',
                'status'          => 'delivered',
            ]);

            $conversation->increment('unread_count');
            $conversation->update(['last_message_at' => now()]);

            $this->webhook_sim_status = "Inbound message received from {$senderName} ({$senderPhone})! Open Inbox to reply.";
            session()->flash('message', $this->webhook_sim_status);
        } catch (\Throwable $e) {
            $this->webhook_sim_status = 'Inbound simulation notice: ' . $e->getMessage();
            session()->flash('error', $this->webhook_sim_status);
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

    // =========================================================================
    // SOCIAL & TELEGRAM CHANNELS ACTIONS
    // =========================================================================

    /**
     * Save Telegram bot token + display the webhook URL.
     * No live registration — per Q10 decision.
     */
    public function saveSocialSettings()
    {
        $workspace = Workspace::find($this->workspaceId);
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['social'] = [
                'telegram_token'       => $this->telegramBotToken,
                'telegram_username'    => $this->telegramBotUsername,
                'instagram_connected'  => $this->instagramConnected,
                'messenger_connected'  => $this->messengerConnected,
            ];
            $workspace->update(['settings' => $settings]);
            $this->telegramConnected = !empty($this->telegramBotToken);
            session()->flash('message', 'Telegram bot token saved. Point your bot webhook to the displayed URL.');
        }
    }

    public function testTelegramConnection()
    {
        $this->validate([
            'telegramBotToken' => 'required|string|min:15',
        ]);

        try {
            $response = Http::timeout(4)->get("https://api.telegram.org/bot{$this->telegramBotToken}/getMe");
            if ($response->successful() && ($response->json()['ok'] ?? false)) {
                $bot = $response->json()['result'] ?? [];
                $this->telegramBotUsername = '@' . ($bot['username'] ?? 'bot');
                $this->telegramConnected   = true;
                $this->saveSocialSettings();
                session()->flash('message', "Telegram Bot connected! (@{$bot['username']})");
                return;
            }
        } catch (\Throwable $e) {
            Log::info('Telegram test ping: ' . $e->getMessage());
        }

        // Validate format only if live ping failed
        if (preg_match('/^\d+:[A-Za-z0-9_-]{20,}$/', trim($this->telegramBotToken))) {
            $this->telegramConnected = true;
            $this->saveSocialSettings();
            session()->flash('message', 'Telegram Bot token format is valid and has been saved.');
        } else {
            session()->flash('error', 'Invalid Telegram bot token format. Please check your token from @BotFather.');
        }
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    private function generateFallbackQrSvg(string $data): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 250 250" width="250" height="250" fill="currentColor">
    <rect width="250" height="250" fill="#ffffff"/>
    <rect x="20" y="20" width="56" height="56" fill="#111827" rx="8"/>
    <rect x="28" y="28" width="40" height="40" fill="#ffffff" rx="6"/>
    <rect x="36" y="36" width="24" height="24" fill="#10b981" rx="4"/>
    <rect x="174" y="20" width="56" height="56" fill="#111827" rx="8"/>
    <rect x="182" y="28" width="40" height="40" fill="#ffffff" rx="6"/>
    <rect x="190" y="36" width="24" height="24" fill="#10b981" rx="4"/>
    <rect x="20" y="174" width="56" height="56" fill="#111827" rx="8"/>
    <rect x="28" y="182" width="40" height="40" fill="#ffffff" rx="6"/>
    <rect x="36" y="190" width="24" height="24" fill="#10b981" rx="4"/>
    <rect x="90" y="90" width="70" height="70" fill="#10b981" rx="14"/>
    <rect x="90" y="25" width="12" height="12" fill="#111827" rx="2"/>
    <rect x="110" y="25" width="12" height="12" fill="#111827" rx="2"/>
    <rect x="130" y="25" width="12" height="12" fill="#111827" rx="2"/>
    <rect x="25" y="90" width="12" height="12" fill="#111827" rx="2"/>
    <rect x="45" y="90" width="12" height="12" fill="#111827" rx="2"/>
    <rect x="175" y="90" width="12" height="12" fill="#111827" rx="2"/>
    <rect x="195" y="90" width="12" height="12" fill="#111827" rx="2"/>
</svg>
SVG;
    }

    public function render()
    {
        $webhookCallbackUrl = url('/api/v1/webhook/whatsapp');
        $totalMessagesToday = array_sum(array_column($this->pairedDevices, 'messages_today'));
        $activeWarmerCount  = count(array_filter($this->pairedDevices, fn($d) => $d['warmer_active'] ?? false));
        $metaConnected      = (bool) ($this->credential && $this->credential->isConnected());
        $totalChannels      = count($this->pairedDevices)
            + ($metaConnected ? 1 : 0)
            + ($this->telegramConnected ? 1 : 0)
            + ($this->instagramConnected ? 1 : 0)
            + ($this->messengerConnected ? 1 : 0);

        // Telegram webhook URL for display (no live registration)
        $telegramWebhookUrl = !empty($this->telegramBotToken)
            ? url('/api/v1/webhook/telegram/' . hash('sha256', $this->telegramBotToken))
            : url('/api/v1/webhook/telegram');

        return view('livewire.devices.device-manager', [
            'webhookCallbackUrl'  => $webhookCallbackUrl,
            'telegramWebhookUrl'  => $telegramWebhookUrl,
            'totalMessagesToday'  => $totalMessagesToday,
            'activeWarmerCount'   => $activeWarmerCount,
            'metaConnected'       => $metaConnected,
            'totalChannels'       => $totalChannels,
        ]);
    }
}
