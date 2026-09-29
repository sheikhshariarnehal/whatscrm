<?php

namespace App\Livewire\Dashboard;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Instance;
use App\Models\Message;
use App\Models\MetaCredential;
use App\Models\Phonebook;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class LiveStats extends Component
{
    public ?int $workspaceId = null;
    public string $timeRange = '7_days'; // 'today', '7_days', '30_days', 'this_month', 'all_time'

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? 1;
    }

    public function setTimeRange(string $range)
    {
        if (in_array($range, ['today', '7_days', '30_days', 'this_month', 'all_time'])) {
            $this->timeRange = $range;
        }
    }

    public function render()
    {
        // 1. Calculate Date Range Boundaries
        $now = Carbon::now();
        $startDate = match ($this->timeRange) {
            'today' => $now->copy()->startOfDay(),
            '7_days' => $now->copy()->subDays(6)->startOfDay(),
            '30_days' => $now->copy()->subDays(29)->startOfDay(),
            'this_month' => $now->copy()->startOfMonth(),
            'all_time' => null,
            default => $now->copy()->subDays(6)->startOfDay(),
        };

        // 2. Contact Metrics
        $totalContacts = Contact::count();
        $newContactsInRange = $startDate 
            ? Contact::where('created_at', '>=', $startDate)->count() 
            : $totalContacts;
        $totalPhonebooks = Phonebook::count();

        // 3. Conversation & Inbox Metrics
        $activeConversations = Conversation::where('status', 'open')->count();
        $unassignedConversations = Conversation::where('status', 'open')
            ->whereNull('assigned_member_id')
            ->count();
        $totalUnreadMessages = (int) Conversation::sum('unread_count');
        $totalConversations = Conversation::count();

        // 4. Messaging Metrics in Selected Time Range
        $msgBaseQuery = Message::query();
        if ($startDate) {
            $msgBaseQuery->where('created_at', '>=', $startDate);
        }

        $totalMessagesSent = (clone $msgBaseQuery)->where('direction', 'outbound')->count();
        $totalMessagesReceived = (clone $msgBaseQuery)->where('direction', 'inbound')->count();
        
        $deliveredCount = (clone $msgBaseQuery)
            ->where('direction', 'outbound')
            ->whereIn('status', ['delivered', 'read'])
            ->count();
            
        $readCount = (clone $msgBaseQuery)
            ->where('direction', 'outbound')
            ->where('status', 'read')
            ->count();
            
        $failedCount = (clone $msgBaseQuery)
            ->where('direction', 'outbound')
            ->where('status', 'failed')
            ->count();

        $deliveryRate = $totalMessagesSent > 0 
            ? round(($deliveredCount / $totalMessagesSent) * 100, 1) 
            : 100.0;
            
        $readRate = $totalMessagesSent > 0 
            ? round(($readCount / $totalMessagesSent) * 100, 1) 
            : 0.0;

        // 5. CRM Pipeline & Deals
        $totalPipelineValue = (float) Conversation::where('deal_value', '>', 0)->sum('deal_value');
        $wonDealsCount = Conversation::where('kanban_stage', 'won')->count();
        $totalDealsCount = Conversation::whereNotNull('kanban_stage')
            ->where('kanban_stage', '!=', '')
            ->count();

        $pipelineStages = [
            'new_lead' => Conversation::where('kanban_stage', 'new_lead')->count(),
            'contacted' => Conversation::where('kanban_stage', 'contacted')->count(),
            'qualified' => Conversation::where('kanban_stage', 'qualified')->count(),
            'proposal' => Conversation::where('kanban_stage', 'proposal')->count(),
            'won' => $wonDealsCount,
        ];

        // 6. Multi-Channel & Infrastructure Health
        $metaAccount = MetaCredential::first();
        $isMetaConnected = $metaAccount && $metaAccount->isConnected();
        
        $totalQrInstances = 0;
        $activeQrInstances = 0;
        try {
            $totalQrInstances = Instance::count();
            $activeQrInstances = Instance::whereIn('status', ['ACTIVE', 'CONNECTED'])->count();
        } catch (\Throwable $e) {
            // Fallback gracefully if table not yet migrated
        }

        // 7. Active / Recent Broadcast Campaigns Spotlight
        $recentCampaigns = [];
        try {
            $recentCampaigns = Campaign::orderBy('created_at', 'desc')->take(2)->get();
        } catch (\Throwable $e) {
            // Fallback gracefully if campaigns empty
        }

        // 8. Recent WhatsApp Conversations (Preserved section, eager-loaded)
        $recentConversations = Conversation::with(['contact', 'tags', 'assignedMember'])
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        // 9. Daily Message Traffic Series Data for 7-Day Chart
        $daysCount = match ($this->timeRange) {
            'today' => 1,
            '30_days' => 14, // Display 14 sample points for 30d view
            'this_month' => 14,
            default => 7,
        };

        $chartDays = [];
        $chartInbound = [];
        $chartOutbound = [];
        $maxChartVal = 10;

        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $dayLabel = $daysCount === 1 ? 'Today' : $date->format('M d');
            $inbound = Message::where('direction', 'inbound')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();
            $outbound = Message::where('direction', 'outbound')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $chartDays[] = $dayLabel;
            $chartInbound[] = $inbound;
            $chartOutbound[] = $outbound;
        }

        $maxChartVal = max(5, max($chartInbound ?: [0]), max($chartOutbound ?: [0]));

        return view('livewire.dashboard.live-stats', [
            'totalContacts' => $totalContacts,
            'newContactsInRange' => $newContactsInRange,
            'totalPhonebooks' => $totalPhonebooks,
            'activeConversations' => $activeConversations,
            'unassignedConversations' => $unassignedConversations,
            'totalUnreadMessages' => $totalUnreadMessages,
            'totalConversations' => $totalConversations,
            'totalMessagesSent' => $totalMessagesSent,
            'totalMessagesReceived' => $totalMessagesReceived,
            'deliveryRate' => $deliveryRate,
            'readRate' => $readRate,
            'failedCount' => $failedCount,
            'totalPipelineValue' => $totalPipelineValue,
            'wonDealsCount' => $wonDealsCount,
            'totalDealsCount' => $totalDealsCount,
            'pipelineStages' => $pipelineStages,
            'metaAccount' => $metaAccount,
            'isMetaConnected' => $isMetaConnected,
            'totalQrInstances' => $totalQrInstances,
            'activeQrInstances' => $activeQrInstances,
            'recentCampaigns' => $recentCampaigns,
            'recentConversations' => $recentConversations,
            'chartDays' => $chartDays,
            'chartInbound' => $chartInbound,
            'chartOutbound' => $chartOutbound,
            'maxChartVal' => $maxChartVal,
        ]);
    }

    #[On('echo:workspace.{workspaceId},NewMessageReceived')]
    #[On('echo:workspace.{workspaceId},ConversationUpdated')]
    #[On('echo:workspace.{workspaceId},CampaignUpdated')]
    public function refreshStats()
    {
        // Automatically triggers live re-render when Echo events arrive
    }
}

