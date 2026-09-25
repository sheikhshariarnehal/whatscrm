<?php

namespace App\Livewire\Automations;

use App\Models\ChatbotRule;
use App\Models\Flow;
use App\Models\QuickReply;
use App\Models\Workspace;
use Livewire\Component;
use Livewire\WithPagination;

class AutomationHub extends Component
{
    use WithPagination;

    public string $activeTab = 'flows'; // 'flows', 'rules', 'quick_replies', 'ai'

    // Flow Creation / Editing
    public bool $showFlowModal = false;
    public string $flowName = '';
    public string $flowTriggerKeywords = '';
    public string $flowWelcomeMessage = '';

    // Chatbot Rule Creation / Editing
    public bool $showRuleModal = false;
    public string $ruleKeywords = '';
    public string $ruleMatchType = 'contains';
    public string $ruleReplyText = '';
    public int $rulePriority = 0;

    // Quick Reply Creation
    public bool $showQuickReplyModal = false;
    public string $qrShortcut = '';
    public string $qrMessage = '';
    public string $qrCategory = 'General';

    // AI Assistant Configuration
    public string $aiProvider = 'gemini';
    public string $aiModel = 'gemini-2.5-flash';
    public string $aiSystemPrompt = 'You are an intelligent customer support representative for our company on WhatsApp. Answer queries concisely and politely.';
    public string $aiKnowledgeBase = 'Business hours: 9 AM to 6 PM Mon-Fri. Delivery within 24-48 hours nationwide.';
    public float $aiTemperature = 0.7;

    public function mount()
    {
        $workspace = auth()->user()->currentWorkspace();
        if ($workspace && isset($workspace->settings['ai'])) {
            $ai = $workspace->settings['ai'];
            $this->aiProvider = $ai['provider'] ?? 'gemini';
            $this->aiModel = $ai['model'] ?? 'gemini-2.5-flash';
            $this->aiSystemPrompt = $ai['system_prompt'] ?? $this->aiSystemPrompt;
            $this->aiKnowledgeBase = $ai['knowledge_base'] ?? $this->aiKnowledgeBase;
            $this->aiTemperature = (float) ($ai['temperature'] ?? 0.7);
        }
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // --- FLOWS ---
    public function openNewFlowModal()
    {
        $this->reset(['flowName', 'flowTriggerKeywords', 'flowWelcomeMessage']);
        $this->showFlowModal = true;
    }

    public function saveFlow()
    {
        $this->validate([
            'flowName' => 'required|string|max:100',
            'flowTriggerKeywords' => 'required|string',
            'flowWelcomeMessage' => 'required|string',
        ]);

        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);

        Flow::create([
            'workspace_id' => $workspaceId,
            'name' => $this->flowName,
            'trigger_type' => 'keyword',
            'trigger_keywords' => $this->flowTriggerKeywords,
            'flow_data' => [
                'welcome_message' => $this->flowWelcomeMessage,
                'nodes' => [
                    ['id' => '1', 'type' => 'trigger', 'title' => 'Keyword Match', 'data' => ['keywords' => $this->flowTriggerKeywords]],
                    ['id' => '2', 'type' => 'message', 'title' => 'Send Greeting', 'data' => ['body' => $this->flowWelcomeMessage]],
                    ['id' => '3', 'type' => 'action', 'title' => 'Route to Agent', 'data' => ['action' => 'assign']],
                ],
            ],
            'is_active' => true,
        ]);

        $this->showFlowModal = false;
        session()->flash('success', 'Visual chat flow created successfully!');
    }

    public function toggleFlow(int $id)
    {
        $flow = Flow::find($id);
        if ($flow) {
            $flow->update(['is_active' => !$flow->is_active]);
        }
    }

    public function deleteFlow(int $id)
    {
        $flow = Flow::find($id);
        if ($flow) {
            $flow->delete();
            session()->flash('info', 'Flow deleted.');
        }
    }

    // --- CHATBOT RULES ---
    public function openNewRuleModal()
    {
        $this->reset(['ruleKeywords', 'ruleReplyText', 'rulePriority']);
        $this->ruleMatchType = 'contains';
        $this->showRuleModal = true;
    }

    public function saveRule()
    {
        $this->validate([
            'ruleKeywords' => 'required|string|max:255',
            'ruleMatchType' => 'required|in:exact,contains,starts_with',
            'ruleReplyText' => 'required|string',
        ]);

        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);

        ChatbotRule::create([
            'workspace_id' => $workspaceId,
            'keywords' => $this->ruleKeywords,
            'match_type' => $this->ruleMatchType,
            'reply_type' => 'text',
            'reply_content' => ['text' => $this->ruleReplyText],
            'priority' => $this->rulePriority,
            'is_active' => true,
        ]);

        $this->showRuleModal = false;
        session()->flash('success', 'Chatbot rule saved!');
    }

    public function toggleRule(int $id)
    {
        $rule = ChatbotRule::find($id);
        if ($rule) {
            $rule->update(['is_active' => !$rule->is_active]);
        }
    }

    public function deleteRule(int $id)
    {
        $rule = ChatbotRule::find($id);
        if ($rule) {
            $rule->delete();
            session()->flash('info', 'Chatbot rule deleted.');
        }
    }

    // --- QUICK REPLIES ---
    public function openNewQuickReplyModal()
    {
        $this->reset(['qrShortcut', 'qrMessage']);
        $this->qrCategory = 'General';
        $this->showQuickReplyModal = true;
    }

    public function saveQuickReply()
    {
        $this->validate([
            'qrShortcut' => 'required|string|max:50',
            'qrMessage' => 'required|string',
        ]);

        $shortcut = str_starts_with($this->qrShortcut, '/') ? $this->qrShortcut : '/' . $this->qrShortcut;
        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);

        QuickReply::create([
            'workspace_id' => $workspaceId,
            'shortcut' => $shortcut,
            'message' => $this->qrMessage,
            'category' => $this->qrCategory,
        ]);

        $this->showQuickReplyModal = false;
        session()->flash('success', 'Quick reply added!');
    }

    public function deleteQuickReply(int $id)
    {
        $qr = QuickReply::find($id);
        if ($qr) {
            $qr->delete();
            session()->flash('info', 'Quick reply deleted.');
        }
    }

    // --- AI ASSISTANT ---
    public function saveAiSettings()
    {
        $workspace = auth()->user()->currentWorkspace();
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['ai'] = [
                'provider' => $this->aiProvider,
                'model' => $this->aiModel,
                'system_prompt' => $this->aiSystemPrompt,
                'knowledge_base' => $this->aiKnowledgeBase,
                'temperature' => $this->aiTemperature,
            ];
            $workspace->update(['settings' => $settings]);
            session()->flash('success', 'AI Assistant configuration updated!');
        }
    }

    public function render()
    {
        $flows = Flow::latest()->paginate(10);
        $rules = ChatbotRule::orderByDesc('priority')->latest()->paginate(15);
        $quickReplies = QuickReply::latest()->paginate(15);

        return view('livewire.automations.automation-hub', [
            'flows' => $flows,
            'rules' => $rules,
            'quickReplies' => $quickReplies,
        ])->layout('layouts.app');
    }
}
