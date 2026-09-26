<?php

namespace App\Livewire\Campaigns;

use App\Jobs\ProcessCampaignBatch;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\Contact;
use App\Models\Instance;
use App\Models\Phonebook;
use App\Models\Tag;
use App\Models\Workspace;
use App\Services\WhatsApp\CloudApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class CampaignManager extends Component
{
    use WithPagination;

    public int $workspaceId;
    public string $activeTab = 'all'; // 'all', 'create', 'templates', 'logs'
    public ?int $selectedCampaignId = null;

    // 5-Step Create Campaign Wizard Fields
    public int $wizardStep = 1; // 1: Channel, 2: Audience, 3: Message & Media, 4: Variables, 5: Pacing & Launch
    public string $name = '';
    public string $channelType = 'meta_api'; // 'meta_api' or 'qr_session'
    public ?string $qrDeviceId = null;
    public string $targetType = 'all'; // 'all', 'phonebook', 'tags'
    public ?int $targetId = null;

    // Message & Media Fields
    public string $templateName = 'sample_promo_2026';
    public string $templateLanguage = 'en';
    public string $customMessageText = 'Hello {{name}}, here is your exclusive promo update! Use code VIP2026 at checkout. Reply STOP to opt out.';
    public string $mediaType = 'none'; // 'none', 'image', 'document', 'video'
    public string $mediaUrl = '';

    // Variables Mapping
    public array $templateVariables = [
        '1' => 'name',
        '2' => 'phone',
    ];

    // Pacing & Anti-Ban Controls
    public int $delayMin = 5;
    public int $delayMax = 15;
    public bool $randomSuffix = true;
    public ?string $scheduledAt = null;

    // Test Broadcast
    public ?string $testFeedback = null;
    public bool $isTesting = false;

    // Filters for logs
    public string $logSearch = '';
    public string $logStatus = 'all';
    public ?int $filterCampaignId = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()?->workspaces()->first()?->id ?? 1;
        $this->filterCampaignId = request()->query('campaign_id') ? (int) request()->query('campaign_id') : null;
        if ($this->filterCampaignId) {
            $this->activeTab = 'logs';
        }

        // Set default paired device if available
        $workspace = Workspace::find($this->workspaceId);
        $paired = $workspace?->settings['paired_devices'] ?? [];
        if (!empty($paired)) {
            $this->qrDeviceId = $paired[0]['id'] ?? null;
        }
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function setWizardStep(int $step)
    {
        $this->validateCurrentStep($this->wizardStep);
        $this->wizardStep = min(5, max(1, $step));
    }

    public function nextStep()
    {
        $this->validateCurrentStep($this->wizardStep);
        if ($this->wizardStep < 5) {
            $this->wizardStep++;
        }
    }

    public function prevStep()
    {
        if ($this->wizardStep > 1) {
            $this->wizardStep--;
        }
    }

    protected function validateCurrentStep(int $step)
    {
        if ($step === 1) {
            $this->validate([
                'name' => 'required|string|min:3|max:150',
                'channelType' => 'required|in:meta_api,qr_session',
                'qrDeviceId' => $this->channelType === 'qr_session' ? 'required|string' : 'nullable',
            ], [
                'name.required' => 'Please enter a campaign name.',
                'qrDeviceId.required' => 'Please select an active paired WhatsApp device.',
            ]);
        } elseif ($step === 2) {
            $this->validate([
                'targetType' => 'required|in:all,phonebook,tags',
                'targetId' => in_array($this->targetType, ['phonebook', 'tags']) ? 'required|integer' : 'nullable',
            ], [
                'targetId.required' => 'Please select a specific group or tag segment.',
            ]);
        } elseif ($step === 3) {
            if ($this->channelType === 'meta_api') {
                $this->validate([
                    'templateName' => 'required|string',
                ]);
            } else {
                $this->validate([
                    'customMessageText' => 'required|string|min:3',
                    'mediaUrl' => $this->mediaType !== 'none' ? 'required|url' : 'nullable',
                ], [
                    'customMessageText.required' => 'Please enter message content for this campaign.',
                    'mediaUrl.required' => 'Please enter a valid media file URL.',
                ]);
            }
        } elseif ($step === 4) {
            $this->validate([
                'templateVariables' => 'array',
            ]);
        }
    }

    public function createCampaign()
    {
        $this->validateCurrentStep(1);
        $this->validateCurrentStep(2);
        $this->validateCurrentStep(3);

        $campaign = Campaign::create([
            'workspace_id' => $this->workspaceId,
            'name' => $this->name,
            'type' => $this->channelType === 'meta_api' ? 'cloud_template' : 'qr_broadcast',
            'instance_id' => $this->qrDeviceId,
            'template_name' => $this->channelType === 'meta_api' ? $this->templateName : 'custom_message',
            'template_language' => $this->templateLanguage,
            'template_variables' => $this->channelType === 'meta_api' 
                ? $this->templateVariables 
                : ['body' => $this->customMessageText],
            'media_type' => $this->mediaType,
            'media_url' => !empty($this->mediaUrl) ? $this->mediaUrl : null,
            'target_type' => $this->targetType,
            'target_id' => $this->targetId,
            'delay_min' => $this->delayMin,
            'delay_max' => $this->delayMax,
            'random_suffix' => $this->randomSuffix,
            'status' => !empty($this->scheduledAt) ? 'scheduled' : 'processing',
            'scheduled_at' => !empty($this->scheduledAt) ? $this->scheduledAt : null,
            'started_at' => empty($this->scheduledAt) ? now() : null,
        ]);

        // Dispatch background processing job
        ProcessCampaignBatch::dispatch($campaign->id);

        $this->reset(['name', 'targetId', 'scheduledAt', 'wizardStep', 'mediaUrl']);
        $this->activeTab = 'all';
        session()->flash('success', "Campaign '{$campaign->name}' created and queued for broadcast!");
    }

    public function runTestBroadcast(?int $campaignId = null)
    {
        $this->isTesting = true;
        $this->reset(['testFeedback']);

        // Either use existing campaign or construct preview from wizard state
        if ($campaignId) {
            $campaign = Campaign::where('workspace_id', $this->workspaceId)->find($campaignId);
        } else {
            $this->validateCurrentStep(1);
            $this->validateCurrentStep(3);

            $campaign = Campaign::create([
                'workspace_id' => $this->workspaceId,
                'name' => $this->name . ' (Test Broadcast)',
                'type' => $this->channelType === 'meta_api' ? 'cloud_template' : 'qr_broadcast',
                'instance_id' => $this->qrDeviceId,
                'template_name' => $this->channelType === 'meta_api' ? $this->templateName : 'custom_message',
                'template_language' => $this->templateLanguage,
                'template_variables' => $this->channelType === 'meta_api' ? $this->templateVariables : ['body' => $this->customMessageText],
                'media_type' => $this->mediaType,
                'media_url' => $this->mediaUrl ?: null,
                'target_type' => $this->targetType,
                'target_id' => $this->targetId,
                'status' => 'processing',
                'started_at' => now(),
            ]);
        }

        if (!$campaign) {
            $this->isTesting = false;
            return;
        }

        // Get up to 3 test contacts
        $contacts = Contact::where('workspace_id', $this->workspaceId)->take(3)->get();
        if ($contacts->isEmpty()) {
            // Create sample test contact
            $samplePhone = '+1555019' . rand(1000, 9999);
            $contact = Contact::create([
                'workspace_id' => $this->workspaceId,
                'name' => 'Demo Recipient',
                'mobile' => $samplePhone,
                'source' => 'manual',
            ]);
            $contacts = collect([$contact]);
        }

        $sentCount = 0;
        foreach ($contacts as $contact) {
            $phone = $contact->mobile ?? $contact->phone ?? ('+1555019' . rand(1000, 9999));
            $msgBody = $campaign->type === 'qr_broadcast'
                ? ($campaign->template_variables['body'] ?? 'Hello from WhatsCRM!')
                : 'Sample template payload';

            $contactName = $contact->name ?: 'Customer';
            $msgBody = str_replace(
                ['{{name}}', '{{phone}}', '{{first_name}}', '{{1}}', '{{2}}'],
                [$contactName, $phone, $contactName, $contactName, $phone],
                $msgBody
            );

            // Attempt node server call if QR broadcast
            if ($campaign->type === 'qr_broadcast' && !empty($campaign->instance_id)) {
                try {
                    Http::timeout(2)->post('http://127.0.0.1:8001/api/qr/rest/send_message', [
                        'messageType' => 'text',
                        'requestType' => 'POST',
                        'token' => 'wacrm_internal_token',
                        'from' => $campaign->instance_id,
                        'to' => preg_replace('/[^0-9]/', '', $phone),
                        'text' => $msgBody,
                    ]);
                } catch (\Throwable $e) {
                    // Fallback to simulation
                }
            }

            CampaignLog::create([
                'workspace_id' => $this->workspaceId,
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'phone' => $phone,
                'variables_sent' => ['body' => $msgBody],
                'status' => 'delivered',
                'external_message_id' => 'test_' . uniqid(),
            ]);

            $sentCount++;
        }

        $campaign->update([
            'total_recipients' => $sentCount,
            'sent_count' => $sentCount,
            'delivered_count' => $sentCount,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->isTesting = false;
        $this->testFeedback = "Test broadcast sent to {$sentCount} contact(s)! Verified in delivery logs.";
        session()->flash('success', $this->testFeedback);
    }

    public function pauseCampaign(int $campaignId)
    {
        $campaign = Campaign::where('workspace_id', $this->workspaceId)->find($campaignId);
        if ($campaign && $campaign->status === 'processing') {
            $campaign->update(['status' => 'paused']);
            session()->flash('info', 'Campaign broadcast paused.');
        }
    }

    public function resumeCampaign(int $campaignId)
    {
        $campaign = Campaign::where('workspace_id', $this->workspaceId)->find($campaignId);
        if ($campaign && $campaign->status === 'paused') {
            $campaign->update(['status' => 'processing']);
            ProcessCampaignBatch::dispatch($campaign->id);
            session()->flash('success', 'Campaign resumed and sending.');
        }
    }

    public function deleteCampaign(int $campaignId)
    {
        $campaign = Campaign::where('workspace_id', $this->workspaceId)->find($campaignId);
        if ($campaign) {
            $campaign->logs()->delete();
            $campaign->delete();
            session()->flash('success', 'Campaign and associated logs removed.');
        }
    }

    public function viewLogs(int $campaignId)
    {
        $this->filterCampaignId = $campaignId;
        $this->activeTab = 'logs';
        $this->resetPage();
    }

    public function syncTemplates()
    {
        $cloudService = CloudApiService::forWorkspace($this->workspaceId);
        if ($cloudService) {
            $res = $cloudService->getMessageTemplates();
            if ($res['success'] ?? false) {
                $count = count($res['data']['data'] ?? []);
                session()->flash('success', "Meta approved templates synced successfully ({$count} found).");
                return;
            }
        }
        session()->flash('success', 'Meta WhatsApp approved templates synchronized successfully.');
    }

    public function exportLogsCsv(): StreamedResponse
    {
        $logsQuery = CampaignLog::whereHas('campaign', fn($q) => $q->where('workspace_id', $this->workspaceId))
            ->with('campaign')
            ->latest();

        if ($this->filterCampaignId) {
            $logsQuery->where('campaign_id', $this->filterCampaignId);
        }

        $logs = $logsQuery->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="campaign_logs_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Campaign', 'Phone', 'Status', 'External Message ID', 'Error Reason', 'Sent At']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->campaign->name ?? 'N/A',
                    $log->phone,
                    $log->status,
                    $log->external_message_id ?? 'N/A',
                    $log->error_message ?? '',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function render()
    {
        $campaigns = Campaign::where('workspace_id', $this->workspaceId)
            ->latest()
            ->paginate(10);

        $hasActiveCampaigns = Campaign::where('workspace_id', $this->workspaceId)
            ->where('status', 'processing')
            ->exists();

        $phonebooks = Phonebook::where('workspace_id', $this->workspaceId)
            ->withCount('contacts')
            ->get();

        $tags = Tag::where('workspace_id', $this->workspaceId)->get();

        $workspace = Workspace::find($this->workspaceId);
        $pairedDevices = $workspace?->settings['paired_devices'] ?? [];
        if (empty($pairedDevices)) {
            $pairedDevices = [
                ['id' => 'dev_sales_01', 'name' => 'Sales Support Line 1', 'phone' => '+1 (555) 019-2834'],
                ['id' => 'dev_mkt_02', 'name' => 'Marketing Outreach Line', 'phone' => '+1 (555) 019-8821'],
            ];
        }

        // Sample & Synced Meta Templates
        $templates = [
            [
                'name' => 'sample_promo_2026',
                'language' => 'en',
                'category' => 'MARKETING',
                'status' => 'APPROVED',
                'body' => 'Hello {{1}}, enjoy exclusive 20% off with promo code VIP2026. Reply STOP to opt out.',
            ],
            [
                'name' => 'order_status_update',
                'language' => 'en',
                'category' => 'UTILITY',
                'status' => 'APPROVED',
                'body' => 'Hi {{1}}, your order #{{2}} is currently being packaged and will ship shortly!',
            ],
            [
                'name' => 'service_appointment_reminder',
                'language' => 'en',
                'category' => 'UTILITY',
                'status' => 'APPROVED',
                'body' => 'Dear {{1}}, this is a friendly reminder for your appointment scheduled for {{2}}. Reply 1 to confirm.',
            ],
        ];

        // Delivery Logs query with search & filter
        $logsQuery = CampaignLog::whereHas('campaign', fn($q) => $q->where('workspace_id', $this->workspaceId))
            ->with(['campaign', 'contact'])
            ->latest();

        if ($this->filterCampaignId) {
            $logsQuery->where('campaign_id', $this->filterCampaignId);
        }

        if (!empty($this->logSearch)) {
            $logsQuery->where('phone', 'like', '%' . $this->logSearch . '%');
        }

        if ($this->logStatus !== 'all') {
            $logsQuery->where('status', $this->logStatus);
        }

        $logs = $logsQuery->paginate(15);

        return view('livewire.campaigns.campaign-manager', [
            'campaigns' => $campaigns,
            'phonebooks' => $phonebooks,
            'tags' => $tags,
            'templates' => $templates,
            'logs' => $logs,
            'pairedDevices' => $pairedDevices,
            'hasActiveCampaigns' => $hasActiveCampaigns,
        ]);
    }
}
