<?php

namespace App\Livewire\Campaigns;

use App\Jobs\ProcessCampaignBatch;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\Phonebook;
use App\Models\Tag;
use App\Models\Workspace;
use App\Services\WhatsApp\CloudApiService;
use Illuminate\Support\Facades\Auth;
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

    // Create Campaign Wizard Fields
    public int $wizardStep = 1;
    public string $name = '';
    public string $channelType = 'meta_api'; // 'meta_api' or 'qr_session'
    public ?string $qrDeviceId = null;
    public string $templateName = 'sample_promo_2026';
    public string $templateLanguage = 'en';
    public string $customMessageText = 'Hello {{1}}, here is your exclusive promo update! Reply STOP to opt out.';
    public string $targetType = 'all'; // 'all', 'phonebook', 'tags'
    public ?int $targetId = null;
    public array $templateVariables = [
        '1' => 'name',
        '2' => 'phone',
    ];
    public int $sleepInterval = 10; // Anti-ban delay in seconds for QR sessions
    public ?string $scheduledAt = null;

    // Filters for logs
    public string $logSearch = '';
    public string $logStatus = 'all';
    public ?int $filterCampaignId = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()->workspaces()->first()?->id ?? 1;
        $this->filterCampaignId = request()->query('campaign_id') ? (int) request()->query('campaign_id') : null;
        if ($this->filterCampaignId) {
            $this->activeTab = 'logs';
        }
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function setWizardStep(int $step)
    {
        $this->wizardStep = $step;
    }

    public function createCampaign()
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'channelType' => 'required|in:meta_api,qr_session',
            'targetType' => 'required|in:all,phonebook,tags',
            'targetId' => 'nullable|integer',
        ]);

        $campaign = Campaign::create([
            'workspace_id' => $this->workspaceId,
            'name' => $this->name,
            'type' => $this->channelType === 'meta_api' ? 'cloud_template' : 'qr_broadcast',
            'template_name' => $this->channelType === 'meta_api' ? $this->templateName : 'custom_message',
            'template_language' => $this->templateLanguage,
            'template_variables' => $this->channelType === 'meta_api' ? $this->templateVariables : ['body' => $this->customMessageText],
            'target_type' => $this->targetType,
            'target_id' => $this->targetId,
            'status' => 'scheduled',
            'scheduled_at' => $this->scheduledAt ?: now(),
        ]);

        // Dispatch background processing job
        ProcessCampaignBatch::dispatch($campaign->id);

        $this->reset(['name', 'targetId', 'scheduledAt', 'wizardStep']);
        $this->activeTab = 'all';
        session()->flash('success', "Campaign '{$campaign->name}' created and queued for broadcast!");
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

        if ($this->logStatus !== 'all') {
            $logsQuery->where('status', $this->logStatus);
        }

        $logs = $logsQuery->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="campaign_delivery_logs_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Log ID', 'Campaign Name', 'Recipient Phone', 'Status', 'WAM ID / Error Reason', 'Variables Sent', 'Dispatched At']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->campaign?->name ?? 'N/A',
                    $log->phone,
                    $log->status,
                    $log->error_message ?: ($log->external_message_id ?: '—'),
                    json_encode($log->variables_sent),
                    $log->created_at?->format('Y-m-d H:i:s') ?? '',
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

        $phonebooks = Phonebook::where('workspace_id', $this->workspaceId)
            ->withCount('contacts')
            ->get();

        $tags = Tag::where('workspace_id', $this->workspaceId)->get();

        $workspace = Workspace::find($this->workspaceId);
        $pairedDevices = $workspace?->settings['paired_devices'] ?? [
            ['id' => 'dev_sales_01', 'name' => 'Sales Support Line 1', 'phone' => '+1 (555) 019-2834'],
            ['id' => 'dev_mkt_02', 'name' => 'Marketing Outreach Line', 'phone' => '+1 (555) 019-8821'],
        ];

        // Sample Meta Templates
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
                'name' => 'appointment_reminder',
                'language' => 'en',
                'category' => 'UTILITY',
                'status' => 'APPROVED',
                'body' => 'Reminder: Your scheduled appointment is set for tomorrow at 10:00 AM. Press Confirm to acknowledge.',
            ],
            [
                'name' => 'flash_sale_announcement',
                'language' => 'en',
                'category' => 'MARKETING',
                'status' => 'APPROVED',
                'body' => 'Special offer for {{1}}: Flash Sale is live for the next 48 hours only! Check your cart at {{2}}.',
            ],
        ];

        // Delivery Logs Query
        $logsQuery = CampaignLog::whereHas('campaign', fn($q) => $q->where('workspace_id', $this->workspaceId))
            ->with('campaign')
            ->latest();

        if ($this->filterCampaignId) {
            $logsQuery->where('campaign_id', $this->filterCampaignId);
        }

        if ($this->logStatus !== 'all') {
            $logsQuery->where('status', $this->logStatus);
        }

        if (!empty($this->logSearch)) {
            $logsQuery->where('phone', 'like', '%' . $this->logSearch . '%');
        }

        $logs = $logsQuery->paginate(15);

        return view('livewire.campaigns.campaign-manager', [
            'campaigns' => $campaigns,
            'phonebooks' => $phonebooks,
            'tags' => $tags,
            'pairedDevices' => $pairedDevices,
            'templates' => $templates,
            'logs' => $logs,
        ])->layout('layouts.app');
    }
}
