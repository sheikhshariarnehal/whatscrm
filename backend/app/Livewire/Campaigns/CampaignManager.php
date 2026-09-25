<?php

namespace App\Livewire\Campaigns;

use App\Jobs\ProcessCampaignBatch;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\Phonebook;
use App\Models\Tag;
use App\Services\WhatsApp\CloudApiService;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignManager extends Component
{
    use WithPagination;

    public string $activeTab = 'all'; // 'all', 'create', 'templates', 'logs'
    public ?int $selectedCampaignId = null;

    // Create Campaign Wizard Fields
    public string $name = '';
    public string $templateName = 'sample_promo_2026';
    public string $templateLanguage = 'en';
    public string $targetType = 'all'; // 'all', 'phonebook', 'tags'
    public ?int $targetId = null;
    public array $templateVariables = [
        '1' => 'name',
        '2' => 'phone',
    ];
    public ?string $scheduledAt = null;

    // Filters for logs
    public string $logSearch = '';
    public string $logStatus = 'all';
    public ?int $filterCampaignId = null;

    public function mount()
    {
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

    public function createCampaign()
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'templateName' => 'required|string|max:100',
            'targetType' => 'required|in:all,phonebook,tags',
            'targetId' => 'nullable|integer',
        ]);

        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);

        $campaign = Campaign::create([
            'workspace_id' => $workspaceId,
            'name' => $this->name,
            'type' => 'cloud_template',
            'template_name' => $this->templateName,
            'template_language' => $this->templateLanguage,
            'template_variables' => $this->templateVariables,
            'target_type' => $this->targetType,
            'target_id' => $this->targetId,
            'status' => 'scheduled',
            'scheduled_at' => $this->scheduledAt ?: now(),
        ]);

        // Dispatch background processing job
        ProcessCampaignBatch::dispatch($campaign->id);

        $this->reset(['name', 'targetId', 'scheduledAt']);
        $this->activeTab = 'all';
        session()->flash('success', 'Campaign created and queued for broadcast!');
    }

    public function pauseCampaign(int $campaignId)
    {
        $campaign = Campaign::find($campaignId);
        if ($campaign && $campaign->status === 'processing') {
            $campaign->update(['status' => 'paused']);
            session()->flash('info', 'Campaign paused.');
        }
    }

    public function resumeCampaign(int $campaignId)
    {
        $campaign = Campaign::find($campaignId);
        if ($campaign && $campaign->status === 'paused') {
            $campaign->update(['status' => 'processing']);
            ProcessCampaignBatch::dispatch($campaign->id);
            session()->flash('success', 'Campaign resumed.');
        }
    }

    public function viewLogs(int $campaignId)
    {
        $this->filterCampaignId = $campaignId;
        $this->activeTab = 'logs';
        $this->resetPage();
    }

    public function render()
    {
        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);

        $campaigns = Campaign::latest()->paginate(10);
        $phonebooks = Phonebook::all();
        $tags = Tag::all();

        // Sample Meta Templates (or fetched via CloudApiService if credentials connected)
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
        ];

        // Delivery Logs Query
        $logsQuery = CampaignLog::with('campaign')
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
            'templates' => $templates,
            'logs' => $logs,
        ])->layout('layouts.app');
    }
}
