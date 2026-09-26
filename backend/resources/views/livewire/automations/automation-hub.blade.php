<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Automations & Workflows</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Configure smart 24/7 keyword bots, visual conversational flows, and AI assistant intelligence.
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($activeTab === 'flows')
                <button wire:click="openNewFlowModal" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-medium text-sm shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Create Visual Flow</span>
                </button>
            @elseif($activeTab === 'rules')
                <button wire:click="openNewRuleModal" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-medium text-sm shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>New Chatbot Rule</span>
                </button>
            @elseif($activeTab === 'quick_replies')
                <button wire:click="openNewQuickReplyModal" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-medium text-sm shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>New Quick Reply</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-sm border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <x-tabs>
        <x-tab-item wire:click="setTab('flows')" :active="$activeTab === 'flows'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Visual Flows ({{ $flows->total() }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('rules')" :active="$activeTab === 'rules'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <span>Keyword Chatbots ({{ $rules->total() }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('quick_replies')" :active="$activeTab === 'quick_replies'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            <span>Quick Replies ({{ $quickReplies->total() }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('ai')" :active="$activeTab === 'ai'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            <span>AI Assistant (LLM)</span>
        </x-tab-item>
    </x-tabs>

    <!-- TAB 1: VISUAL FLOWS -->
    @if($activeTab === 'flows')
        @if($flows->isEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-12 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">No visual flows configured</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                    Design automated interactive trees with triggers, questions, dynamic buttons, and agent handover.
                </p>
                <button wire:click="openNewFlowModal" class="mt-4 px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90">
                    Create Visual Flow
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($flows as $flow)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-6 shadow-sm flex flex-col justify-between hover:border-primary/40 transition-all">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $flow->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ $flow->name }}</h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="toggleFlow({{ $flow->id }})" class="text-xs px-2.5 py-1 rounded-lg border {{ $flow->is_active ? 'border-emerald-200 text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40' : 'border-gray-200 text-gray-500 bg-gray-50 dark:bg-gray-800' }}">
                                        {{ $flow->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                    <button wire:click="deleteFlow({{ $flow->id }})" wire:confirm="Are you sure you want to delete this flow?" class="p-1 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Flow Trigger Badge -->
                            <div class="mb-4">
                                <span class="text-xs text-gray-500">Trigger Keywords:</span>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    @foreach(explode(',', $flow->trigger_keywords) as $kw)
                                        <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-mono text-xs">
                                            {{ trim($kw) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Visual Nodes Step Preview -->
                            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 text-xs text-gray-600 dark:text-gray-300 space-y-2">
                                <div class="flex items-center gap-2 font-mono text-[11px] text-primary font-bold">
                                    <span>⚡ Trigger</span>
                                    <span>➔</span>
                                    <span>💬 Welcome Reply</span>
                                    <span>➔</span>
                                    <span>👥 Route to Agent</span>
                                </div>
                                <div class="text-[11px] text-gray-500 italic">
                                    "{{ Str::limit($flow->flow_data['welcome_message'] ?? 'Initial greeting', 80) }}"
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500">
                            <span>Executions: <strong>{{ number_format($flow->execution_count) }} times</strong></span>
                            <span>Updated {{ $flow->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $flows->links() }}
            </div>
        @endif
    @endif

    <!-- TAB 2: KEYWORD CHATBOTS -->
    @if($activeTab === 'rules')
        <x-card gutterless class="overflow-hidden">
            @if($rules->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No keyword chatbot rules</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Automate replies to common queries like pricing, catalog, business hours, and support.
                    </p>
                    <x-button wire:click="openNewRuleModal" variant="solid" size="sm" class="mt-4">
                        Add Chatbot Rule
                    </x-button>
                </div>
            @else
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Trigger Keywords</th>
                            <th>Match Type</th>
                            <th>Automated Response</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rules as $rule)
                            <tr>
                                <td>
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach(explode(',', $rule->keywords) as $kw)
                                            <x-tag color="primary" class="font-mono font-semibold">
                                                {{ trim($kw) }}
                                            </x-tag>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <x-tag color="gray" class="font-mono capitalize">
                                        {{ $rule->match_type }}
                                    </x-tag>
                                </td>
                                <td class="text-xs text-gray-700 dark:text-gray-300 max-w-md">
                                    {{ Str::limit($rule->reply_content['text'] ?? '', 100) }}
                                </td>
                                <td class="text-xs font-mono text-gray-500">
                                    {{ $rule->priority }}
                                </td>
                                <td>
                                    <button wire:click="toggleRule({{ $rule->id }})" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $rule->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $rule->is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </td>
                                <td class="text-right">
                                    <button wire:click="deleteRule({{ $rule->id }})" wire:confirm="Delete this chatbot rule?" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $rules->links() }}
                </div>
            @endif
        </x-card>
    @endif

    <!-- TAB 3: QUICK REPLIES -->
    @if($activeTab === 'quick_replies')
        <x-card gutterless class="overflow-hidden">
            @if($quickReplies->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No quick replies created</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Speed up human agent replies by creating shortcuts like <code class="text-primary font-bold">/pricing</code> or <code class="text-primary font-bold">/welcome</code>.
                    </p>
                    <x-button wire:click="openNewQuickReplyModal" variant="solid" size="sm" class="mt-4">
                        Add Quick Reply
                    </x-button>
                </div>
            @else
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Shortcut</th>
                            <th>Category</th>
                            <th>Canned Message Content</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quickReplies as $qr)
                            <tr>
                                <td class="font-mono font-bold text-primary">
                                    {{ $qr->shortcut }}
                                </td>
                                <td>
                                    <x-tag color="gray">
                                        {{ $qr->category ?: 'General' }}
                                    </x-tag>
                                </td>
                                <td class="text-xs text-gray-700 dark:text-gray-300 max-w-lg">
                                    {{ $qr->message }}
                                </td>
                                <td class="text-right">
                                    <button wire:click="deleteQuickReply({{ $qr->id }})" wire:confirm="Delete this quick reply?" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $quickReplies->links() }}
                </div>
            @endif
        </x-card>
    @endif

    <!-- TAB 4: AI ASSISTANT -->
    @if($activeTab === 'ai')
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">AI Agent Intelligence Configuration</h2>
                    <p class="text-xs text-gray-500">Configure LLM-powered dynamic customer assistance on WhatsApp.</p>
                </div>
            </div>

            <form wire:submit.prevent="saveAiSettings" class="space-y-6">
                <!-- AI Provider -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">AI Model Provider</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $aiProvider === 'gemini' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                            <input type="radio" wire:model.live="aiProvider" value="gemini" class="text-primary focus:ring-primary">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Google Gemini</div>
                                <div class="text-[11px] text-gray-500">Gemini 2.5 Flash</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $aiProvider === 'openai' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                            <input type="radio" wire:model.live="aiProvider" value="openai" class="text-primary focus:ring-primary">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">OpenAI</div>
                                <div class="text-[11px] text-gray-500">GPT-4o Mini</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $aiProvider === 'deepseek' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                            <input type="radio" wire:model.live="aiProvider" value="deepseek" class="text-primary focus:ring-primary">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">DeepSeek</div>
                                <div class="text-[11px] text-gray-500">DeepSeek-V3</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- System Persona Prompt -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Agent Persona & Instructions</label>
                    <textarea 
                        wire:model="aiSystemPrompt" 
                        rows="3" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    ></textarea>
                </div>

                <!-- Knowledge Base Facts -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Knowledge Base & Business Facts</label>
                    <textarea 
                        wire:model="aiKnowledgeBase" 
                        rows="4" 
                        placeholder="Provide details about products, pricing, operating hours, refund policies..."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    ></textarea>
                </div>

                <!-- Temperature Slider -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Creativity / Temperature</label>
                        <span class="text-xs font-mono font-bold text-primary">{{ $aiTemperature }}</span>
                    </div>
                    <input type="range" wire:model.live="aiTemperature" min="0" max="1" step="0.1" class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-primary">
                    <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                        <span>Precise (0.0)</span>
                        <span>Balanced (0.7)</span>
                        <span>Creative (1.0)</span>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition-all">
                        Save AI Agent Config
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- MODAL: NEW VISUAL FLOW -->
    @if($showFlowModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Create Visual Chat Flow</h3>
                    <button wire:click="$set('showFlowModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Flow Name</label>
                        <input type="text" wire:model="flowName" placeholder="e.g. Lead Qualification Tree" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        @error('flowName') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Trigger Keywords (comma separated)</label>
                        <input type="text" wire:model="flowTriggerKeywords" placeholder="quote, estimate, price quote, inquiry" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        @error('flowTriggerKeywords') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">First Interactive Message</label>
                        <textarea wire:model="flowWelcomeMessage" rows="3" placeholder="Hi! Welcome to our automated advisor. What service are you interested in today?" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"></textarea>
                        @error('flowWelcomeMessage') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showFlowModal', false)" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveFlow" class="px-4 py-2 rounded-lg bg-primary text-white font-medium">Save Flow</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: NEW CHATBOT RULE -->
    @if($showRuleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">New Keyword Chatbot Rule</h3>
                    <button wire:click="$set('showRuleModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Trigger Keywords (comma separated)</label>
                        <input type="text" wire:model="ruleKeywords" placeholder="e.g. price, pricing, catalog, hours" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        @error('ruleKeywords') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Match Type</label>
                            <x-select wire:model="ruleMatchType">
                                <option value="contains">Contains Keyword</option>
                                <option value="exact">Exact Match</option>
                                <option value="starts_with">Starts With</option>
                            </x-select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Priority Order</label>
                            <input type="number" wire:model="rulePriority" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Automated Reply Text</label>
                        <textarea wire:model="ruleReplyText" rows="4" placeholder="Hello {{name}}! Our plans start at $29/mo. View details at https://whatscrm.com/pricing" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"></textarea>
                        <span class="text-[11px] text-gray-400 mt-1 block">Supports dynamic tags: <code class="font-mono text-primary">&#123;&#123;name&#125;&#125;</code>, <code class="font-mono text-primary">&#123;&#123;phone&#125;&#125;</code></span>
                        @error('ruleReplyText') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showRuleModal', false)" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveRule" class="px-4 py-2 rounded-lg bg-primary text-white font-medium">Save Chatbot Rule</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: NEW QUICK REPLY -->
    @if($showQuickReplyModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">New Quick Reply</h3>
                    <button wire:click="$set('showQuickReplyModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Shortcut (e.g. /pricing)</label>
                            <input type="text" wire:model="qrShortcut" placeholder="/pricing" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                            @error('qrShortcut') <span class="text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Category</label>
                            <input type="text" wire:model="qrCategory" placeholder="Sales, Support, FAQ" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Canned Message</label>
                        <textarea wire:model="qrMessage" rows="4" placeholder="Type canned reply..." class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"></textarea>
                        @error('qrMessage') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showQuickReplyModal', false)" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveQuickReply" class="px-4 py-2 rounded-lg bg-primary text-white font-medium">Add Shortcut</button>
                </div>
            </div>
        </div>
    @endif
</div>
