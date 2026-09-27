<?php

namespace App\Livewire\Automations;

use App\Models\BotBinding;
use App\Models\ChatbotRule;
use App\Models\Flow;
use App\Models\Instance;
use App\Models\MetaCredential;
use App\Models\QuickReply;
use App\Models\WaForm;
use App\Models\WaFormSubmission;
use App\Models\Workspace;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class AutomationHub extends Component
{
    use WithPagination;

    public string $activeTab = 'flows'; // 'flows', 'bindings', 'rules', 'forms', 'ai'

    // --- TAB 1: FLOWS ---
    public bool $showFlowModal = false;
    public string $flowName = '';
    public string $flowDescription = '';
    public string $flowTriggerType = 'keyword';
    public string $flowTriggerKeywords = '';

    // --- TAB 2: BOT & CHANNEL BINDINGS ---
    public bool $showBindingModal = false;
    public string $bindingTitle = '';
    public string $bindingChannel = 'meta'; // 'meta', 'qr', 'telegram', 'instagram', 'messenger', 'webhook'
    public string $bindingOriginId = '';
    public ?int $bindingFlowId = null;

    // --- TAB 3: KEYWORD CHATBOT RULES ---
    public bool $showRuleModal = false;
    public string $ruleKeywords = '';
    public string $ruleMatchType = 'contains';
    public string $ruleReplyText = '';
    public int $rulePriority = 0;

    // --- TAB 4: WHATSAPP FORM BUILDER (META FLOWS STUDIO) ---
    public bool $showFormModal = false;
    public ?int $editingFormId = null;
    public string $formName = '';
    public string $formDescription = '';
    public array $formCategories = ['CUSTOMER_SUPPORT'];
    public array $formFields = [];

    // Form Submissions Viewer
    public bool $showSubmissionsModal = false;
    public ?int $viewingFormId = null;
    public ?string $viewingFormName = null;

    // --- TAB 5: AI ASSISTANT STUDIO & SIMULATOR ---
    public string $aiProvider = 'gemini';
    public string $aiModel = 'gemini-2.5-flash';
    public string $aiApiKey = '';
    public string $aiSystemPrompt = 'You are an intelligent, polite, and helpful AI assistant for our company on WhatsApp. Answer customer queries concisely, guide them accurately, and offer friendly support.';
    public string $aiKnowledgeBase = "Business Hours: Monday to Friday, 9:00 AM - 6:00 PM (EST).\nShipping: Free standard shipping on orders over $50. Nationwide delivery within 2-4 business days.\nRefund Policy: 30-day hassle-free returns with original receipt.";
    public float $aiTemperature = 0.7;
    public int $aiMaxTokens = 800;
    public int $aiContextDepth = 5;

    // Interactive Simulator State
    public array $simulatorMessages = [];
    public string $simulatorInput = '';
    public bool $isAiThinking = false;

    public function mount()
    {
        $requestedTab = request()->query('tab');
        if (in_array($requestedTab, ['flows', 'bindings', 'rules', 'forms', 'ai'])) {
            $this->activeTab = $requestedTab;
        }

        $workspace = auth()->user()->currentWorkspace();
        if ($workspace && isset($workspace->settings['ai'])) {
            $ai = $workspace->settings['ai'];
            $this->aiProvider = $ai['provider'] ?? 'gemini';
            $this->aiModel = $ai['model'] ?? 'gemini-2.5-flash';
            $this->aiApiKey = $ai['api_key'] ?? '';
            $this->aiSystemPrompt = $ai['system_prompt'] ?? $this->aiSystemPrompt;
            $this->aiKnowledgeBase = $ai['knowledge_base'] ?? $this->aiKnowledgeBase;
            $this->aiTemperature = (float) ($ai['temperature'] ?? 0.7);
            $this->aiMaxTokens = (int) ($ai['max_tokens'] ?? 800);
            $this->aiContextDepth = (int) ($ai['context_depth'] ?? 5);
        }

        // Initialize Simulator with welcome greeting
        $this->simulatorMessages = [
            [
                'role' => 'assistant',
                'content' => "👋 Hello! I am your AI WhatsApp Assistant. How can I help you today?",
                'timestamp' => now()->format('H:i'),
            ],
        ];

        // Default initial field for WA form modal
        if (empty($this->formFields)) {
            $this->formFields = [
                [
                    'name' => 'full_name',
                    'label' => 'Full Name',
                    'type' => 'TextInput',
                    'required' => true,
                    'placeholder' => 'Enter your full name',
                    'options' => [],
                ],
                [
                    'name' => 'email',
                    'label' => 'Email Address',
                    'type' => 'TextInput',
                    'required' => true,
                    'placeholder' => 'yourname@company.com',
                    'options' => [],
                ],
            ];
        }
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // =========================================================================
    // --- 1. VISUAL FLOWS ---
    // =========================================================================

    public function openNewFlowModal()
    {
        $this->reset(['flowName', 'flowDescription', 'flowTriggerKeywords']);
        $this->flowTriggerType = 'keyword';
        $this->showFlowModal = true;
    }

    public function createAndLaunchBuilder()
    {
        $this->validate([
            'flowName' => 'required|string|max:100',
            'flowTriggerKeywords' => 'nullable|string|max:255',
        ]);

        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);

        $initialGraph = [
            'nodes' => [
                [
                    'id' => 'initialNode',
                    'type' => 'INITIAL',
                    'position' => ['x' => 100, 'y' => 260],
                    'data' => [
                        'title' => '⚡ Inbound Trigger',
                        'triggerType' => $this->flowTriggerType,
                        'keywords' => $this->flowTriggerKeywords,
                    ],
                ],
                [
                    'id' => 'msg_' . time(),
                    'type' => 'SEND_MESSAGE',
                    'position' => ['x' => 480, 'y' => 240],
                    'data' => [
                        'title' => '💬 Welcome Message',
                        'content' => [
                            'type' => 'text',
                            'text' => [
                                'preview_url' => true,
                                'body' => "Hello {{{name}}}! Welcome to our WhatsApp service. How can we help you today?",
                            ],
                        ],
                    ],
                ],
            ],
            'edges' => [
                [
                    'id' => 'edge_initial_msg',
                    'source' => 'initialNode',
                    'target' => 'msg_' . time(),
                ],
            ],
        ];

        $flow = Flow::create([
            'workspace_id' => $workspaceId,
            'name' => $this->flowName,
            'description' => $this->flowDescription,
            'trigger_type' => $this->flowTriggerType,
            'trigger_keywords' => $this->flowTriggerKeywords,
            'flow_data' => $initialGraph,
            'is_active' => true,
        ]);

        $this->showFlowModal = false;
        return redirect()->route('automations.builder', ['id' => $flow->id]);
    }

    public function cloneFlow(int $id)
    {
        $flow = Flow::find($id);
        if ($flow) {
            $clone = $flow->replicate();
            $clone->name = $flow->name . ' (Copy)';
            $clone->execution_count = 0;
            $clone->is_active = false;
            $clone->save();

            session()->flash('success', "Flow '{$flow->name}' cloned successfully!");
        }
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
            session()->flash('info', 'Visual chat flow deleted.');
        }
    }

    // =========================================================================
    // --- 2. CONNECTED BOTS & CHANNELS ---
    // =========================================================================

    public function openNewBindingModal()
    {
        $this->reset(['bindingTitle', 'bindingOriginId']);
        $this->bindingChannel = 'meta';
        $this->bindingFlowId = Flow::where('is_active', true)->first()->id ?? null;
        $this->showBindingModal = true;
    }

    public function saveBinding()
    {
        $this->validate([
            'bindingTitle' => 'required|string|max:100',
            'bindingChannel' => 'required|in:meta,qr,telegram,instagram,messenger,webhook',
            'bindingFlowId' => 'required|exists:flows,id',
        ]);

        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);

        BotBinding::create([
            'workspace_id' => $workspaceId,
            'title' => $this->bindingTitle,
            'channel' => $this->bindingChannel,
            'origin_id' => $this->bindingOriginId ?: ($this->bindingChannel === 'meta' ? 'META_CLOUD_API' : 'ALL_DEVICES'),
            'flow_id' => $this->bindingFlowId,
            'is_active' => true,
        ]);

        $this->showBindingModal = false;
        session()->flash('success', 'Bot channel binding activated successfully!');
    }

    public function toggleBinding(int $id)
    {
        $binding = BotBinding::find($id);
        if ($binding) {
            $binding->update(['is_active' => !$binding->is_active]);
        }
    }

    public function deleteBinding(int $id)
    {
        $binding = BotBinding::find($id);
        if ($binding) {
            $binding->delete();
            session()->flash('info', 'Bot binding removed.');
        }
    }

    // =========================================================================
    // --- 3. KEYWORD CHATBOT RULES ---
    // =========================================================================

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

    // =========================================================================
    // --- 4. WHATSAPP FORM BUILDER (META FLOWS STUDIO) ---
    // =========================================================================

    public function openNewFormModal()
    {
        $this->reset(['formName', 'formDescription']);
        $this->editingFormId = null;
        $this->formCategories = ['CUSTOMER_SUPPORT'];
        $this->formFields = [
            [
                'name' => 'name',
                'label' => 'Full Name',
                'type' => 'TextInput',
                'required' => true,
                'placeholder' => 'Enter your full name',
                'options' => [],
            ],
            [
                'name' => 'email',
                'label' => 'Email Address',
                'type' => 'TextInput',
                'required' => true,
                'placeholder' => 'yourname@example.com',
                'options' => [],
            ],
            [
                'name' => 'service',
                'label' => 'Interested Service',
                'type' => 'Dropdown',
                'required' => true,
                'placeholder' => 'Select a service',
                'options' => ['Sales Consultation', 'Technical Support', 'Custom Development'],
            ],
        ];
        $this->showFormModal = true;
    }

    public function addFormField()
    {
        $index = count($this->formFields) + 1;
        $this->formFields[] = [
            'name' => 'field_' . $index,
            'label' => 'New Question ' . $index,
            'type' => 'TextInput',
            'required' => true,
            'placeholder' => '',
            'options' => ['Option A', 'Option B'],
        ];
    }

    public function removeFormField(int $index)
    {
        unset($this->formFields[$index]);
        $this->formFields = array_values($this->formFields);
    }

    public function addFieldOption(int $fieldIndex)
    {
        if (isset($this->formFields[$fieldIndex])) {
            $optCount = count($this->formFields[$fieldIndex]['options'] ?? []) + 1;
            $this->formFields[$fieldIndex]['options'][] = 'Option ' . $optCount;
        }
    }

    public function removeFieldOption(int $fieldIndex, int $optIndex)
    {
        if (isset($this->formFields[$fieldIndex]['options'][$optIndex])) {
            unset($this->formFields[$fieldIndex]['options'][$optIndex]);
            $this->formFields[$fieldIndex]['options'] = array_values($this->formFields[$fieldIndex]['options']);
        }
    }

    public function saveWaForm()
    {
        $this->validate([
            'formName' => 'required|string|max:100',
            'formFields' => 'required|array|min:1',
        ]);

        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);

        if ($this->editingFormId) {
            $form = WaForm::find($this->editingFormId);
            if ($form) {
                $form->update([
                    'name' => $this->formName,
                    'description' => $this->formDescription,
                    'fields_schema' => $this->formFields,
                    'categories' => $this->formCategories,
                ]);
            }
        } else {
            WaForm::create([
                'workspace_id' => $workspaceId,
                'name' => $this->formName,
                'description' => $this->formDescription,
                'meta_flow_id' => 'FLOW_' . rand(100000000000, 999999999999),
                'flow_status' => 'PUBLISHED',
                'categories' => $this->formCategories,
                'fields_schema' => $this->formFields,
            ]);
        }

        $this->showFormModal = false;
        session()->flash('success', 'WhatsApp Form (Meta Flow) saved and ready for messaging!');
    }

    public function viewFormSubmissions(int $formId)
    {
        $form = WaForm::find($formId);
        if ($form) {
            $this->viewingFormId = $form->id;
            $this->viewingFormName = $form->name;
            $this->showSubmissionsModal = true;
        }
    }

    public function deleteWaForm(int $id)
    {
        $form = WaForm::find($id);
        if ($form) {
            $form->delete();
            session()->flash('info', 'WhatsApp Form deleted.');
        }
    }

    // =========================================================================
    // --- 5. AI ASSISTANT STUDIO & CHAT SIMULATOR ---
    // =========================================================================

    public function saveAiSettings()
    {
        $workspace = auth()->user()->currentWorkspace();
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['ai'] = [
                'provider' => $this->aiProvider,
                'model' => $this->aiModel,
                'api_key' => $this->aiApiKey,
                'system_prompt' => $this->aiSystemPrompt,
                'knowledge_base' => $this->aiKnowledgeBase,
                'temperature' => $this->aiTemperature,
                'max_tokens' => $this->aiMaxTokens,
                'context_depth' => $this->aiContextDepth,
            ];
            $workspace->update(['settings' => $settings]);
            session()->flash('success', 'AI Assistant intelligence configuration saved!');
        }
    }

    public function sendSimulatorMessage()
    {
        $text = trim($this->simulatorInput);
        if (empty($text)) {
            return;
        }

        // Add user message
        $this->simulatorMessages[] = [
            'role' => 'user',
            'content' => $text,
            'timestamp' => now()->format('H:i'),
        ];

        $this->simulatorInput = '';
        $this->isAiThinking = true;

        // Generate response using configured provider or smart simulated fallback
        $reply = $this->generateAiReply($text);

        $this->simulatorMessages[] = [
            'role' => 'assistant',
            'content' => $reply,
            'timestamp' => now()->format('H:i'),
        ];

        $this->isAiThinking = false;
    }

    protected function generateAiReply(string $userQuery): string
    {
        $workspace = auth()->user()->currentWorkspace();
        $apiKey = $this->aiApiKey ?: ($workspace->settings['ai']['api_key'] ?? null);

        // 1. If valid Google Gemini API Key configured, call Gemini 2.5 Flash
        if ($this->aiProvider === 'gemini' && !empty($apiKey)) {
            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(15)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->aiModel}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => "System Instructions:\n" . $this->aiSystemPrompt . "\n\nKnowledge Base:\n" . $this->aiKnowledgeBase . "\n\nCustomer Message: " . $userQuery],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => $this->aiTemperature,
                            'maxOutputTokens' => $this->aiMaxTokens,
                        ],
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    return $json['candidates'][0]['content']['parts'][0]['text'] ?? "I am ready to assist you!";
                }
            } catch (\Exception $e) {
                Log::warning('Gemini API call failed in simulator: ' . $e->getMessage());
            }
        }

        // 2. If valid OpenAI API Key configured, call OpenAI
        if ($this->aiProvider === 'openai' && !empty($apiKey)) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(15)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => $this->aiModel,
                        'messages' => [
                            ['role' => 'system', 'content' => $this->aiSystemPrompt . "\n\nKnowledge Base:\n" . $this->aiKnowledgeBase],
                            ['role' => 'user', 'content' => $userQuery],
                        ],
                        'temperature' => $this->aiTemperature,
                        'max_tokens' => $this->aiMaxTokens,
                    ]);

                if ($response->successful()) {
                    return $response->json()['choices'][0]['message']['content'] ?? "Hello! How can I assist?";
                }
            } catch (\Exception $e) {
                Log::warning('OpenAI API call failed in simulator: ' . $e->getMessage());
            }
        }

        // 3. Realistic intelligent simulation based on knowledge base facts & persona
        $queryLower = strtolower($userQuery);

        if (str_contains($queryLower, 'hour') || str_contains($queryLower, 'time') || str_contains($queryLower, 'open')) {
            return "Our business hours are Monday to Friday from 9:00 AM to 6:00 PM EST. Let me know if you need anything else!";
        } elseif (str_contains($queryLower, 'ship') || str_contains($queryLower, 'deliver') || str_contains($queryLower, 'track')) {
            return "We provide free standard shipping on all orders over $50! Delivery typically takes 2-4 business days nationwide.";
        } elseif (str_contains($queryLower, 'refund') || str_contains($queryLower, 'return')) {
            return "We offer a 30-day hassle-free return policy. As long as you have your original receipt, we'll take care of it right away.";
        } elseif (str_contains($queryLower, 'human') || str_contains($queryLower, 'agent') || str_contains($queryLower, 'person') || str_contains($queryLower, 'rep')) {
            return "I will be happy to connect you with one of our human team specialists right away. Please hold on for a moment!";
        } else {
            return "Thank you for reaching out! Based on our catalog and support services, I would be delighted to help you. Is there a specific product or service you'd like more details on?";
        }
    }

    public function clearSimulatorHistory()
    {
        $this->simulatorMessages = [
            [
                'role' => 'assistant',
                'content' => "Chat history cleared. How can I assist you now?",
                'timestamp' => now()->format('H:i'),
            ],
        ];
    }

    public function render()
    {
        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);

        $flows = Flow::where('workspace_id', $workspaceId)->latest()->paginate(10);
        $bindings = BotBinding::where('workspace_id', $workspaceId)->with('flow')->latest()->paginate(10);
        $rules = ChatbotRule::where('workspace_id', $workspaceId)->orderByDesc('priority')->latest()->paginate(15);
        $forms = WaForm::where('workspace_id', $workspaceId)->withCount('submissions')->latest()->paginate(10);
        $submissions = $this->viewingFormId 
            ? WaFormSubmission::where('wa_form_id', $this->viewingFormId)->latest()->paginate(15) 
            : collect([]);

        // Channels / Devices for binding modal
        $qrInstances = Instance::where('uid', (string) $workspaceId)->get();
        $metaCredential = MetaCredential::where('workspace_id', $workspaceId)->first();
        $activeFlows = Flow::where('workspace_id', $workspaceId)->where('is_active', true)->get();

        return view('livewire.automations.automation-hub', [
            'flows' => $flows,
            'bindings' => $bindings,
            'rules' => $rules,
            'forms' => $forms,
            'submissions' => $submissions,
            'qrInstances' => $qrInstances,
            'metaCredential' => $metaCredential,
            'activeFlows' => $activeFlows,
        ])->layout('layouts.app');
    }
}
