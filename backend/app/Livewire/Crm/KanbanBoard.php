<?php

namespace App\Livewire\Crm;

use App\Events\ConversationUpdated;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Tag;
use App\Models\WorkspaceMember;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class KanbanBoard extends Component
{
    public int $workspaceId;
    public string $activeView = 'kanban'; // 'kanban', 'table', 'settings', 'analytics'
    public string $selectedPipeline = 'sales_2026';
    public string $search = '';
    public string $priorityFilter = 'all';
    public string $agentFilter = 'all';

    // New / Edit Deal Modal state
    public bool $showDealModal = false;
    public ?int $editingDealId = null;
    public string $dealName = '';
    public string $dealCompany = '';
    public string $dealPhone = '';
    public float $dealValue = 1500.00;
    public string $dealStage = 'lead';
    public string $dealPriority = 'high';
    public ?int $dealAssignedMemberId = null;
    public ?string $dealExpectedCloseAt = null;

    // New Stage Modal state
    public bool $showStageModal = false;
    public string $newStageTitle = '';
    public string $newStageColor = '#3b82f6';

    // Default System Stages
    public array $defaultStages = [
        [
            'key' => 'lead',
            'title' => 'New Leads',
            'color' => '#3b82f6', // Blue
            'bg_light' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400',
        ],
        [
            'key' => 'contacted',
            'title' => 'Contacted',
            'color' => '#8b5cf6', // Purple
            'bg_light' => 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400',
        ],
        [
            'key' => 'proposal',
            'title' => 'Proposal & Quote',
            'color' => '#f59e0b', // Amber
            'bg_light' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400',
        ],
        [
            'key' => 'negotiation',
            'title' => 'Negotiation',
            'color' => '#ec4899', // Pink
            'bg_light' => 'bg-pink-50 dark:bg-pink-950/40 text-pink-600 dark:text-pink-400',
        ],
        [
            'key' => 'won',
            'title' => 'Won / Closed',
            'color' => '#10b981', // Emerald
            'bg_light' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400',
        ],
    ];

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()->workspaces()->first()?->id ?? 1;
        $this->dealExpectedCloseAt = now()->addDays(14)->format('Y-m-d');
        $this->ensureSeedDeals();
    }

    private function ensureSeedDeals()
    {
        // If conversations have 0 deal_value, populate some realistic values for demonstration
        $conversations = Conversation::where('workspace_id', $this->workspaceId)->get();
        $sampleValues = [2500, 4000, 8000, 6000, 12000, 3500, 1500, 9500];
        $samplePriorities = ['high', 'medium', 'urgent', 'low', 'high'];
        $sampleStages = ['lead', 'contacted', 'proposal', 'negotiation', 'won'];
        $sampleCompanies = ['Acme Corp', 'Apex Logistics', 'Global Tech Inc', 'Bright Retail', 'Vertex Media'];

        $i = 0;
        foreach ($conversations as $conv) {
            if ($conv->deal_value <= 0) {
                $conv->update([
                    'deal_value' => $sampleValues[$i % count($sampleValues)],
                    'priority' => $samplePriorities[$i % count($samplePriorities)],
                    'kanban_stage' => $conv->kanban_stage ?? $sampleStages[$i % count($sampleStages)],
                    'company' => $conv->company ?? $sampleCompanies[$i % count($sampleCompanies)],
                    'expected_close_at' => now()->addDays(rand(5, 30)),
                ]);
                $i++;
            }
        }
    }

    public function setView(string $view)
    {
        $this->activeView = $view;
    }

    public function updateDealStage(int $conversationId, string $targetStage)
    {
        $conversation = Conversation::where('workspace_id', $this->workspaceId)->find($conversationId);

        if (! $conversation) {
            return;
        }

        $conversation->update([
            'kanban_stage' => $targetStage,
        ]);

        event(new ConversationUpdated($conversation));
    }

    public function openNewDealModal(?string $stage = 'lead')
    {
        $this->resetDealForm();
        $this->dealStage = $stage;
        $this->showDealModal = true;
    }

    public function openEditDealModal(int $id)
    {
        $deal = Conversation::where('workspace_id', $this->workspaceId)->findOrFail($id);
        $this->editingDealId = $deal->id;
        $this->dealName = $deal->sender_name ?? '';
        $this->dealCompany = $deal->company ?? '';
        $this->dealPhone = $deal->sender_mobile ?? '';
        $this->dealValue = (float) $deal->deal_value;
        $this->dealStage = $deal->kanban_stage ?? 'lead';
        $this->dealPriority = $deal->priority ?? 'medium';
        $this->dealAssignedMemberId = $deal->assigned_member_id;
        $this->dealExpectedCloseAt = $deal->expected_close_at ? $deal->expected_close_at->format('Y-m-d') : null;
        $this->showDealModal = true;
    }

    public function saveDeal()
    {
        $this->validate([
            'dealName' => 'required|string|max:100',
            'dealPhone' => 'required|string|max:50',
            'dealValue' => 'required|numeric|min:0',
            'dealStage' => 'required|string',
            'dealPriority' => 'required|string',
        ]);

        if ($this->editingDealId) {
            $deal = Conversation::where('workspace_id', $this->workspaceId)->findOrFail($this->editingDealId);
            $deal->update([
                'sender_name' => $this->dealName,
                'company' => $this->dealCompany,
                'sender_mobile' => $this->dealPhone,
                'deal_value' => $this->dealValue,
                'kanban_stage' => $this->dealStage,
                'priority' => $this->dealPriority,
                'assigned_member_id' => $this->dealAssignedMemberId ?: null,
                'expected_close_at' => $this->dealExpectedCloseAt,
            ]);
            event(new ConversationUpdated($deal));
        } else {
            // Find or create Contact
            $contact = Contact::firstOrCreate(
                ['workspace_id' => $this->workspaceId, 'phone_number' => $this->dealPhone],
                ['first_name' => $this->dealName, 'email' => strtolower(str_replace(' ', '', $this->dealName)) . '@example.com']
            );

            $deal = Conversation::create([
                'workspace_id' => $this->workspaceId,
                'contact_id' => $contact->id,
                'chat_id' => $this->dealPhone . '@c.us',
                'channel' => 'whatsapp_cloud',
                'sender_name' => $this->dealName,
                'company' => $this->dealCompany,
                'sender_mobile' => $this->dealPhone,
                'last_message' => "Deal created for \${$this->dealValue} in {$this->dealStage} stage.",
                'last_message_at' => now(),
                'deal_value' => $this->dealValue,
                'kanban_stage' => $this->dealStage,
                'priority' => $this->dealPriority,
                'assigned_member_id' => $this->dealAssignedMemberId ?: null,
                'expected_close_at' => $this->dealExpectedCloseAt,
                'status' => 'open',
            ]);

            event(new ConversationUpdated($deal));
        }

        $this->showDealModal = false;
        $this->resetDealForm();
    }

    public function deleteDeal(int $id)
    {
        $deal = Conversation::where('workspace_id', $this->workspaceId)->find($id);
        if ($deal) {
            $deal->delete();
        }
    }

    private function resetDealForm()
    {
        $this->editingDealId = null;
        $this->dealName = '';
        $this->dealCompany = '';
        $this->dealPhone = '';
        $this->dealValue = 2500.00;
        $this->dealStage = 'lead';
        $this->dealPriority = 'high';
        $this->dealAssignedMemberId = null;
        $this->dealExpectedCloseAt = now()->addDays(14)->format('Y-m-d');
    }

    #[On('echo:workspace.{workspaceId},ConversationUpdated')]
    #[On('echo:workspace.{workspaceId},NewMessageReceived')]
    public function onRealtimeUpdate()
    {
        // Re-renders reactive Kanban state on WebSocket broadcast
    }

    public function render()
    {
        $teamMembers = WorkspaceMember::where('workspace_id', $this->workspaceId)->with('user')->get();

        $query = Conversation::where('workspace_id', $this->workspaceId)
            ->with(['contact', 'tags', 'assignedMember.user']);

        // Search
        if (! empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('sender_name', 'like', $search)
                  ->orWhere('sender_mobile', 'like', $search)
                  ->orWhere('company', 'like', $search)
                  ->orWhere('last_message', 'like', $search);
            });
        }

        // Priority filter
        if ($this->priorityFilter !== 'all') {
            $query->where('priority', $this->priorityFilter);
        }

        // Agent filter
        if ($this->agentFilter !== 'all') {
            if ($this->agentFilter === 'unassigned') {
                $query->whereNull('assigned_member_id');
            } else {
                $query->where('assigned_member_id', $this->agentFilter);
            }
        }

        $conversations = $query->orderBy('updated_at', 'desc')->get();

        // Calculate stage groups and aggregates
        $stageGroups = [];
        $totalPipelineValue = 0;
        $totalWonValue = 0;
        $stageTotals = [];

        foreach ($this->defaultStages as $stage) {
            $stageKey = $stage['key'];
            $items = $conversations->filter(fn ($c) => ($c->kanban_stage ?? 'lead') === $stageKey);
            $stageSum = $items->sum('deal_value');

            $stageGroups[$stageKey] = $items;
            $stageTotals[$stageKey] = $stageSum;
            $totalPipelineValue += $stageSum;

            if ($stageKey === 'won') {
                $totalWonValue = $stageSum;
            }
        }

        // Analytics KPIs
        $dealCount = $conversations->count();
        $averageDealSize = $dealCount > 0 ? round($totalPipelineValue / $dealCount, 2) : 0;
        $wonCount = ($stageGroups['won'] ?? collect())->count();
        $winRate = $dealCount > 0 ? round(($wonCount / $dealCount) * 100, 1) : 0;

        return view('livewire.crm.kanban-board', [
            'teamMembers' => $teamMembers,
            'conversations' => $conversations,
            'stageGroups' => $stageGroups,
            'stageTotals' => $stageTotals,
            'totalPipelineValue' => $totalPipelineValue,
            'totalWonValue' => $totalWonValue,
            'averageDealSize' => $averageDealSize,
            'winRate' => $winRate,
            'dealCount' => $dealCount,
        ]);
    }
}
