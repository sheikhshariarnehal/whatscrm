<div class="p-2 sm:p-2.5 md:p-3 bg-[#f0f2f5] dark:bg-[#0c1317] min-h-[calc(100vh-4rem)] flex flex-col font-sans">
    <!-- Page Header & Action Controls -->
    <div class="px-2 pt-1 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#111b21] dark:text-[#e9edef] tracking-tight">Automations</h1>
        </div>
        <div class="flex items-center gap-3">
            @if($activeTab === 'flows')
                <button wire:click="openNewFlowModal" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-xs shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                    <x-ph-icon name="plus-circle" weight="bold" class="text-base" />
                    <span>Create Visual Flow</span>
                </button>
            @elseif($activeTab === 'bindings')
                <button wire:click="openNewBindingModal" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-xs shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                    <x-ph-icon name="link" weight="bold" class="text-base" />
                    <span>Connect Bot to Channel</span>
                </button>
            @elseif($activeTab === 'rules')
                <button wire:click="openNewRuleModal" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-xs shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                    <x-ph-icon name="plus-circle" weight="bold" class="text-base" />
                    <span>New Chatbot Rule</span>
                </button>
            @elseif($activeTab === 'forms')
                <button wire:click="openNewFormModal" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-xs shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                    <x-ph-icon name="list-checks" weight="bold" class="text-base" />
                    <span>Create WhatsApp Form</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="mb-3 mx-2 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-medium border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2.5 shadow-2xs">
            <x-ph-icon name="check-circle" weight="fill" class="text-lg text-emerald-500" />
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="mb-3 mx-2 p-3.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-xs font-medium border border-blue-200 dark:border-blue-800/50 flex items-center gap-2.5 shadow-2xs">
            <x-ph-icon name="info" weight="fill" class="text-lg text-blue-500" />
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- Folder Tabs Row (Flush with Content Card) -->
    <div class="-mb-px relative z-10">
        <x-tabs>
            <x-tab-item wire:click="setTab('flows')" :active="$activeTab === 'flows'">
                <x-ph-icon name="tree-structure" weight="duotone" class="text-base shrink-0" />
                <span>Visual Flows ({{ $flows->total() }})</span>
            </x-tab-item>
            <x-tab-item wire:click="setTab('bindings')" :active="$activeTab === 'bindings'">
                <x-ph-icon name="share-network" weight="duotone" class="text-base shrink-0" />
                <span>Connected Bots & Channels ({{ $bindings->total() }})</span>
            </x-tab-item>
            <x-tab-item wire:click="setTab('rules')" :active="$activeTab === 'rules'">
                <x-ph-icon name="chat-dots" weight="duotone" class="text-base shrink-0" />
                <span>Keyword Chatbots ({{ $rules->total() }})</span>
            </x-tab-item>
            <x-tab-item wire:click="setTab('forms')" :active="$activeTab === 'forms'">
                <x-ph-icon name="textbox" weight="duotone" class="text-base shrink-0" />
                <span>WhatsApp Form Studio ({{ $forms->total() }})</span>
            </x-tab-item>
            <x-tab-item wire:click="setTab('ai')" :active="$activeTab === 'ai'">
                <x-ph-icon name="sparkle" weight="fill" class="text-base text-amber-500 shrink-0" />
                <span>AI Assistant & Simulator</span>
            </x-tab-item>
        </x-tabs>
    </div>

    <!-- Main Floating Card Container -->
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity duration-150" class="flex-1 flex flex-col rounded-2xl rounded-tl-none border border-[#d1d7db] dark:border-[#222e35] shadow-xs bg-white dark:bg-[#111b21] overflow-hidden">

        <div class="p-6 flex-1 overflow-y-auto bg-white dark:bg-[#111b21]">
            <!-- ========================================================================= -->
            <!-- TAB 1: VISUAL FLOWS                                                       -->
            <!-- ========================================================================= -->
            @if($activeTab === 'flows')
                <div wire:key="tab-panel-automations-flows" class="tab-pane">
        @if($flows->isEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-12 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                    <x-ph-icon name="tree-structure" weight="duotone" class="text-3xl" />
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">No visual chat flows created yet</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-md mx-auto">
                    Design automated interactive trees with triggers, questions, media, conditions, and live agent handover.
                </p>
                <button wire:click="openNewFlowModal" class="mt-5 px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary/90 shadow-sm inline-flex items-center gap-2">
                    <x-ph-icon name="plus" weight="bold" class="text-sm" />
                    <span>Create Your First Visual Flow</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($flows as $flow)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-6 shadow-sm flex flex-col justify-between hover:border-primary/40 transition-all group">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-3 h-3 rounded-full {{ $flow->is_active ? 'bg-emerald-500 ring-4 ring-emerald-500/20' : 'bg-gray-400' }}"></span>
                                    <div>
                                        <h3 class="font-bold text-gray-900 dark:text-white text-base group-hover:text-primary transition-colors">{{ $flow->name }}</h3>
                                        <p class="text-[11px] text-gray-400 line-clamp-1 mt-0.5">{{ $flow->description ?: 'Conversational automation tree' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button wire:click="toggleFlow({{ $flow->id }})" class="text-[11px] font-semibold px-2.5 py-1 rounded-lg border transition-all {{ $flow->is_active ? 'border-emerald-200 text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 dark:border-emerald-800/50' : 'border-gray-200 text-gray-500 bg-gray-50 dark:bg-gray-800' }}">
                                        {{ $flow->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                    <button wire:click="cloneFlow({{ $flow->id }})" title="Clone Flow" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <x-ph-icon name="copy" weight="bold" class="text-sm" />
                                    </button>
                                    <button wire:click="deleteFlow({{ $flow->id }})" wire:confirm="Are you sure you want to delete this visual flow?" title="Delete Flow" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                        <x-ph-icon name="trash" weight="bold" class="text-sm" />
                                    </button>
                                </div>
                            </div>

                            <!-- Trigger Keywords Badge -->
                            <div class="mb-4">
                                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Trigger Mechanism:</span>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-primary-subtle text-primary font-mono text-[11px] font-bold">
                                        {{ strtoupper($flow->trigger_type) }}
                                    </span>
                                    @if($flow->trigger_keywords)
                                        @foreach(explode(',', $flow->trigger_keywords) as $kw)
                                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-mono text-[11px]">
                                                "{{ trim($kw) }}"
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Visual Flow Node Summary -->
                            @php
                                $nodeCount = count($flow->flow_data['nodes'] ?? []);
                                $edgeCount = count($flow->flow_data['edges'] ?? []);
                            @endphp
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 text-xs text-gray-600 dark:text-gray-300 space-y-2">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-gray-500">Flow Canvas Nodes:</span>
                                    <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $nodeCount }} Nodes / {{ $edgeCount }} Edges</span>
                                </div>
                                <div class="flex items-center gap-2 font-mono text-[11px] text-primary font-bold overflow-hidden text-ellipsis whitespace-nowrap">
                                    <span>⚡ Trigger</span>
                                    <span>➔</span>
                                    <span>💬 Send Msg</span>
                                    <span>➔</span>
                                    <span>🔀 Logic</span>
                                    <span>➔</span>
                                    <span>👥 Handover</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer & Launch Builder Button -->
                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500">
                            <div>
                                <span class="text-gray-400">Fired:</span>
                                <strong class="text-gray-700 dark:text-gray-300">{{ number_format($flow->execution_count) }} times</strong>
                            </div>
                            <a href="{{ route('automations.builder', ['id' => $flow->id]) }}" 
                               class="px-3.5 py-1.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-xs shadow-xs transition-all flex items-center gap-1.5">
                                <span>Open Canvas Builder</span>
                                <x-ph-icon name="arrow-up-right" weight="bold" class="text-xs" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $flows->links() }}
            </div>
        @endif
                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- TAB 2: CONNECTED BOTS & CHANNELS                                          -->
            <!-- ========================================================================= -->
            @if($activeTab === 'bindings')
                <div wire:key="tab-panel-automations-bindings" class="tab-pane">
                    <x-card gutterless class="overflow-hidden">
            @if($bindings->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                        <x-ph-icon name="share-network" weight="duotone" class="text-3xl" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No active bot bindings</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Bind your visual flows to specific WhatsApp QR instances, Meta Cloud API phone numbers, or inbound webhooks.
                    </p>
                    <button wire:click="openNewBindingModal" class="mt-4 px-4 py-2 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary/90">
                        Connect Bot to Channel
                    </button>
                </div>
            @else
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Bot Title</th>
                            <th>Channel Platform</th>
                            <th>Device / Origin ID</th>
                            <th>Attached Visual Flow</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bindings as $binding)
                            <tr>
                                <td class="font-bold text-gray-900 dark:text-white text-xs">
                                    {{ $binding->title }}
                                </td>
                                <td>
                                    @if($binding->channel === 'meta')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                            <x-ph-icon name="whatsapp-logo" weight="fill" class="text-sm" />
                                            <span>Meta Cloud API</span>
                                        </span>
                                    @elseif($binding->channel === 'qr')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200 dark:border-blue-800/50">
                                            <x-ph-icon name="qr-code" weight="bold" class="text-sm" />
                                            <span>WhatsApp QR Instance</span>
                                        </span>
                                    @elseif($binding->channel === 'webhook')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50">
                                            <x-ph-icon name="webhooks-logo" weight="bold" class="text-sm" />
                                            <span>Inbound Webhook</span>
                                        </span>
                                    @elseif($binding->channel === 'telegram')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-400 border border-sky-200 dark:border-sky-800/50">
                                            <x-ph-icon name="telegram-logo" weight="fill" class="text-sm" />
                                            <span>Telegram Bot</span>
                                        </span>
                                    @elseif($binding->channel === 'instagram')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50">
                                            <x-ph-icon name="instagram-logo" weight="fill" class="text-sm" />
                                            <span>Instagram DM</span>
                                        </span>
                                    @else
                                        <x-tag color="gray">{{ strtoupper($binding->channel) }}</x-tag>
                                    @endif
                                </td>
                                <td class="font-mono text-xs text-gray-500">
                                    {{ $binding->origin_id }}
                                </td>
                                <td>
                                    @if($binding->flow)
                                        <a href="{{ route('automations.builder', ['id' => $binding->flow->id]) }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                                            <span>{{ $binding->flow->name }}</span>
                                            <x-ph-icon name="arrow-up-right" weight="bold" class="text-xs" />
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No flow attached</span>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="toggleBinding({{ $binding->id }})" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $binding->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $binding->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $binding->is_active ? 'Listening' : 'Paused' }}
                                    </button>
                                </td>
                                <td class="text-right">
                                    <button wire:click="deleteBinding({{ $binding->id }})" wire:confirm="Remove this bot binding?" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                        <x-ph-icon name="trash" weight="bold" class="text-sm" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $bindings->links() }}
                </div>
            @endif
                    </x-card>
                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- TAB 3: KEYWORD CHATBOTS                                                   -->
            <!-- ========================================================================= -->
            @if($activeTab === 'rules')
                <div wire:key="tab-panel-automations-rules" class="tab-pane">
                    <x-card gutterless class="overflow-hidden">
            @if($rules->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                        <x-ph-icon name="chat-dots" weight="duotone" class="text-3xl" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No keyword chatbot rules</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
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
                                            <x-tag color="primary" class="font-mono font-semibold text-[11px]">
                                                {{ trim($kw) }}
                                            </x-tag>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <x-tag color="gray" class="font-mono capitalize text-[11px]">
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
                                    <button wire:click="toggleRule({{ $rule->id }})" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $rule->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $rule->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $rule->is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </td>
                                <td class="text-right">
                                    <button wire:click="deleteRule({{ $rule->id }})" wire:confirm="Delete this chatbot rule?" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                        <x-ph-icon name="trash" weight="bold" class="text-sm" />
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
                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- TAB 4: WHATSAPP FORM BUILDER (META FLOWS STUDIO)                          -->
            <!-- ========================================================================= -->
            @if($activeTab === 'forms')
                <div wire:key="tab-panel-automations-forms" class="tab-pane">
        @if($forms->isEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-12 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                    <x-ph-icon name="textbox" weight="duotone" class="text-3xl" />
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">No Meta WhatsApp Flows created</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-md mx-auto">
                    Design native in-chat WhatsApp forms with inputs, dropdowns, and date pickers for surveys and lead capture.
                </p>
                <button wire:click="openNewFormModal" class="mt-5 px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary/90 shadow-sm inline-flex items-center gap-2">
                    <x-ph-icon name="plus" weight="bold" class="text-sm" />
                    <span>Create Your First WhatsApp Form</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($forms as $form)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-6 shadow-sm flex flex-col justify-between hover:border-primary/40 transition-all">
                        <div>
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ $form->name }}</h3>
                                    <span class="font-mono text-[10px] text-gray-400">{{ $form->meta_flow_id }}</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $form->flow_status === 'PUBLISHED' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200' }}">
                                    {{ $form->flow_status }}
                                </span>
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">
                                {{ $form->description ?: 'Conversational WhatsApp Flow' }}
                            </p>

                            <!-- Field Chips -->
                            <div class="space-y-1.5 p-3 rounded-xl bg-gray-50/80 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 text-[11px]">
                                <div class="text-gray-400 uppercase font-semibold text-[10px]">Form Components ({{ count($form->fields_schema ?? []) }}):</div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($form->fields_schema ?? [] as $f)
                                        <span class="px-2 py-0.5 rounded bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-mono border border-gray-200/60 dark:border-gray-700">
                                            {{ $f['label'] ?? $f['name'] }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs">
                            <button wire:click="viewFormSubmissions({{ $form->id }})" class="font-semibold text-primary hover:underline flex items-center gap-1.5">
                                <x-ph-icon name="users" weight="bold" class="text-sm" />
                                <span>{{ $form->submissions_count }} Submissions</span>
                            </button>
                            <button wire:click="deleteWaForm({{ $form->id }})" wire:confirm="Delete this WhatsApp form?" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                <x-ph-icon name="trash" weight="bold" class="text-sm" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $forms->links() }}
            </div>
        @endif
                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- TAB 5: AI ASSISTANT STUDIO & SIMULATOR                                    -->
            <!-- ========================================================================= -->
            @if($activeTab === 'ai')
                <div wire:key="tab-panel-automations-ai" class="tab-pane">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: AI Intelligence Settings (7 cols) -->
            <div class="lg:col-span-7 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                        <x-ph-icon name="sparkle" weight="fill" class="text-xl" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">AI Agent Intelligence Configuration</h2>
                        <p class="text-xs text-gray-500">Configure LLM prompts, business facts, and API credentials.</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveAiSettings" class="space-y-6">
                    <!-- AI Provider Cards -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2.5">AI Engine Provider</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $aiProvider === 'gemini' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer transition-all">
                                <input type="radio" wire:model.live="aiProvider" value="gemini" class="text-primary focus:ring-primary">
                                <div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white">Google Gemini</div>
                                    <div class="text-[10px] text-gray-500 font-mono">gemini-2.5-flash</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $aiProvider === 'openai' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer transition-all">
                                <input type="radio" wire:model.live="aiProvider" value="openai" class="text-primary focus:ring-primary">
                                <div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white">OpenAI</div>
                                    <div class="text-[10px] text-gray-500 font-mono">gpt-4o-mini</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $aiProvider === 'deepseek' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer transition-all">
                                <input type="radio" wire:model.live="aiProvider" value="deepseek" class="text-primary focus:ring-primary">
                                <div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white">DeepSeek</div>
                                    <div class="text-[10px] text-gray-500 font-mono">deepseek-v3</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- API Key Input -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                            {{ ucfirst($aiProvider) }} API Key
                        </label>
                        <div class="relative">
                            <input type="password" wire:model="aiApiKey" placeholder="Enter {{ ucfirst($aiProvider) }} secret key..." 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1 block">Your API key is securely encrypted at rest per workspace.</span>
                    </div>

                    <!-- System Persona Prompt -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">System Persona & Tone</label>
                        <textarea wire:model="aiSystemPrompt" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                    </div>

                    <!-- Knowledge Base Facts -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Knowledge Base & Facts</label>
                        <textarea wire:model="aiKnowledgeBase" rows="5" placeholder="Operating hours, shipping policies, FAQ answers, pricing tiers..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                    </div>

                    <!-- Advanced Parameters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100 dark:border-gray-800">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 dark:text-gray-300 mb-1">Temperature (Creativity: {{ $aiTemperature }})</label>
                            <input type="range" wire:model.live="aiTemperature" min="0" max="1" step="0.05" class="w-full accent-primary">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 dark:text-gray-300 mb-1">Memory Depth: {{ $aiContextDepth }} messages</label>
                            <input type="range" wire:model.live="aiContextDepth" min="1" max="10" step="1" class="w-full accent-primary">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-xs shadow-sm transition-all flex items-center gap-2">
                            <x-ph-icon name="floppy-disk" weight="bold" class="text-sm" />
                            <span>Save Configuration</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column: In-Browser Live WhatsApp Chat Simulator (5 cols) -->
            <div class="lg:col-span-5 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm flex flex-col h-[650px] overflow-hidden">
                <!-- Simulator Header -->
                <div class="px-4 py-3.5 bg-emerald-600 text-white flex items-center justify-between shrink-0 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs">
                            <x-ph-icon name="whatsapp-logo" weight="fill" class="text-lg" />
                        </div>
                        <div>
                            <div class="font-bold text-xs leading-tight">AI Bot Live Simulator</div>
                            <div class="text-[10px] text-white/80 leading-tight">Active Persona: {{ ucfirst($aiProvider) }}</div>
                        </div>
                    </div>
                    <button wire:click="clearSimulatorHistory" title="Clear History" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all text-xs">
                        <x-ph-icon name="arrows-clockwise" weight="bold" class="text-xs" />
                    </button>
                </div>

                <!-- Chat Messages Scrollable Area -->
                <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#e5ddd5]/30 dark:bg-gray-950/60">
                    @foreach($simulatorMessages as $msg)
                        <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] rounded-2xl p-3 text-xs shadow-xs {{ $msg['role'] === 'user' ? 'bg-emerald-500 text-white rounded-tr-none' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-tl-none border border-gray-100 dark:border-gray-700' }}">
                                <p class="leading-relaxed whitespace-pre-wrap">{{ $msg['content'] }}</p>
                                <div class="text-[9px] mt-1 text-right {{ $msg['role'] === 'user' ? 'text-white/70' : 'text-gray-400' }}">
                                    {{ $msg['timestamp'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($isAiThinking)
                        <div class="flex justify-start">
                            <div class="rounded-2xl p-3 bg-white dark:bg-gray-800 text-gray-400 rounded-tl-none border border-gray-100 dark:border-gray-700 flex items-center gap-1.5 shadow-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-bounce"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-bounce [animation-delay:0.2s]"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-bounce [animation-delay:0.4s]"></span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Simulator Chat Input Bar -->
                <div class="p-3 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 shrink-0">
                    <form wire:submit.prevent="sendSimulatorMessage" class="flex items-center gap-2">
                        <input type="text" wire:model="simulatorInput" placeholder="Test with a query (e.g. 'What are your hours?')..." 
                               class="flex-1 px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <button type="submit" class="p-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-xs transition-all">
                            <x-ph-icon name="paper-plane-right" weight="fill" class="text-sm" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
                </div>
    @endif
    </div>
</div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: CREATE VISUAL FLOW                                               -->
    <!-- ========================================================================= -->
    @if($showFlowModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-800 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Create Visual Chat Flow</h3>
                    <button wire:click="$set('showFlowModal', false)" class="text-gray-400 hover:text-gray-600">
                        <x-ph-icon name="x" weight="bold" class="text-base" />
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Flow Name *</label>
                        <input type="text" wire:model="flowName" placeholder="e.g. Sales Qualification Bot" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs">
                        @error('flowName') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <input type="text" wire:model="flowDescription" placeholder="Optional flow description" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Trigger Keywords (comma separated)</label>
                        <input type="text" wire:model="flowTriggerKeywords" placeholder="e.g. pricing, quote, sales, demo" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs font-mono">
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showFlowModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button wire:click="createAndLaunchBuilder" class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary/90 flex items-center gap-1.5">
                        <span>Create & Launch Canvas</span>
                        <x-ph-icon name="arrow-up-right" weight="bold" class="text-xs" />
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 2: BOT & CHANNEL BINDING                                            -->
    <!-- ========================================================================= -->
    @if($showBindingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-800 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Connect Bot to Channel</h3>
                    <button wire:click="$set('showBindingModal', false)" class="text-gray-400 hover:text-gray-600">
                        <x-ph-icon name="x" weight="bold" class="text-base" />
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Bot Connection Title *</label>
                        <input type="text" wire:model="bindingTitle" placeholder="e.g. Primary Sales WhatsApp Bot" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs">
                        @error('bindingTitle') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Channel Platform *</label>
                        <select wire:model.live="bindingChannel" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs">
                            <option value="meta">WhatsApp Meta Cloud API</option>
                            <option value="qr">WhatsApp QR Session (Baileys)</option>
                            <option value="webhook">Inbound E-Commerce Webhook (Shopify/WooCommerce)</option>
                            <option value="telegram">Telegram Bot</option>
                            <option value="instagram">Instagram Direct Message</option>
                            <option value="messenger">Facebook Messenger</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Target Visual Flow *</label>
                        <select wire:model="bindingFlowId" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs">
                            <option value="">-- Select Active Visual Flow --</option>
                            @foreach($activeFlows as $f)
                                <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->trigger_type }})</option>
                            @endforeach
                        </select>
                        @error('bindingFlowId') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showBindingModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button wire:click="saveBinding" class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary/90">
                        Activate Connection
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 3: KEYWORD CHATBOT RULE                                             -->
    <!-- ========================================================================= -->
    @if($showRuleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-800 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">New Chatbot Rule</h3>
                    <button wire:click="$set('showRuleModal', false)" class="text-gray-400 hover:text-gray-600">
                        <x-ph-icon name="x" weight="bold" class="text-base" />
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Keywords (comma separated) *</label>
                        <input type="text" wire:model="ruleKeywords" placeholder="e.g. price, pricing, cost, quote" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs font-mono">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Match Type</label>
                            <select wire:model="ruleMatchType" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs">
                                <option value="contains">Contains Keyword</option>
                                <option value="exact">Exact Match</option>
                                <option value="starts_with">Starts With</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                            <input type="number" wire:model="rulePriority" min="0" max="100" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Automated Reply Message *</label>
                        <textarea wire:model="ruleReplyText" rows="4" placeholder="Hi {{name}}! Our starter plan is $29/mo..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showRuleModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button wire:click="saveRule" class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary/90">
                        Save Chatbot Rule
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 4: WHATSAPP FORM BUILDER (META FLOWS STUDIO DESIGNER)               -->
    <!-- ========================================================================= -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-4xl w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-800 space-y-6 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-primary/10 text-primary">
                            <x-ph-icon name="textbox" weight="duotone" class="text-xl" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white text-base">Meta WhatsApp Flows Studio</h3>
                            <p class="text-[11px] text-gray-400">Design dynamic WhatsApp forms published to Meta Cloud API v20.0</p>
                        </div>
                    </div>
                    <button wire:click="$set('showFormModal', false)" class="text-gray-400 hover:text-gray-600">
                        <x-ph-icon name="x" weight="bold" class="text-base" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto space-y-6 pr-1">
                    <!-- Form Title & Description -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Form Name *</label>
                            <input type="text" wire:model="formName" placeholder="e.g. Customer Intake Form" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Description / Subtitle</label>
                            <input type="text" wire:model="formDescription" placeholder="Shown to customer on header" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs">
                        </div>
                    </div>

                    <!-- Interactive Field Builder -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Form Questions & Fields</h4>
                            <button wire:click="addFormField" class="px-3 py-1.5 rounded-xl bg-primary-subtle text-primary font-semibold text-xs hover:bg-primary/20 flex items-center gap-1.5 transition-all">
                                <x-ph-icon name="plus" weight="bold" class="text-xs" />
                                <span>Add New Question</span>
                            </button>
                        </div>

                        <div class="space-y-3">
                            @foreach($formFields as $index => $field)
                                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-[10px]">{{ $index + 1 }}</span>
                                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $field['label'] ?: 'Question' }}</span>
                                        </div>
                                        <button wire:click="removeFormField({{ $index }})" class="text-gray-400 hover:text-rose-500">
                                            <x-ph-icon name="trash" weight="bold" class="text-sm" />
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-500 mb-1">Field Type</label>
                                            <select wire:model.live="formFields.{{ $index }}.type" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs">
                                                <option value="TextInput">Text Input</option>
                                                <option value="TextArea">Text Area</option>
                                                <option value="Dropdown">Dropdown Menu</option>
                                                <option value="RadioButtonsGroup">Radio Buttons</option>
                                                <option value="CheckboxGroup">Checkbox Group</option>
                                                <option value="DatePicker">Date Picker</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-500 mb-1">Question Label</label>
                                            <input type="text" wire:model="formFields.{{ $index }}.label" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-500 mb-1">Variable Identifier</label>
                                            <input type="text" wire:model="formFields.{{ $index }}.name" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-mono">
                                        </div>
                                    </div>

                                    <!-- If Dropdown or Radio, show options manager -->
                                    @if(in_array($field['type'], ['Dropdown', 'RadioButtonsGroup', 'CheckboxGroup']))
                                        <div class="pt-2 border-t border-gray-200/60 dark:border-gray-700">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[10px] font-semibold text-gray-400 uppercase">Answer Options</span>
                                                <button wire:click="addFieldOption({{ $index }})" class="text-[11px] text-primary font-bold hover:underline">+ Add Option</button>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($field['options'] ?? [] as $optIndex => $opt)
                                                    <div class="flex items-center gap-1 bg-white dark:bg-gray-800 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700">
                                                        <input type="text" wire:model="formFields.{{ $index }}.options.{{ $optIndex }}" class="text-xs border-0 bg-transparent p-0 focus:ring-0 w-28">
                                                        <button wire:click="removeFieldOption({{ $index }}, {{ $optIndex }})" class="text-gray-400 hover:text-rose-500">×</button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-gray-800 shrink-0">
                    <button wire:click="$set('showFormModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button wire:click="saveWaForm" class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary/90 flex items-center gap-1.5">
                        <x-ph-icon name="cloud-arrow-up" weight="bold" class="text-sm" />
                        <span>Publish Meta Flow</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 5: SUBMISSIONS VIEWER                                               -->
    <!-- ========================================================================= -->
    @if($showSubmissionsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-3xl w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-800 space-y-4 max-h-[85vh] flex flex-col">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 shrink-0">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-base">Submissions: {{ $viewingFormName }}</h3>
                        <p class="text-[11px] text-gray-400">Incoming customer responses captured via WhatsApp Flows</p>
                    </div>
                    <button wire:click="$set('showSubmissionsModal', false)" class="text-gray-400 hover:text-gray-600">
                        <x-ph-icon name="x" weight="bold" class="text-base" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto">
                    @if($submissions->isEmpty())
                        <div class="p-8 text-center text-gray-400 text-xs">
                            No submissions recorded yet for this WhatsApp Flow.
                        </div>
                    @else
                        <x-table hoverable>
                            <thead>
                                <tr>
                                    <th>Customer Phone</th>
                                    <th>Submission Responses</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $sub)
                                    <tr>
                                        <td class="font-mono font-bold text-xs text-primary">
                                            {{ $sub->from_phone }}
                                        </td>
                                        <td class="text-xs">
                                            <div class="space-y-1">
                                                @foreach($sub->submission_data ?? [] as $k => $v)
                                                    <div>
                                                        <span class="font-mono text-gray-400">{{ $k }}:</span>
                                                        <strong class="text-gray-800 dark:text-gray-200">{{ is_array($v) ? implode(', ', $v) : $v }}</strong>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-[11px] text-gray-400 whitespace-nowrap">
                                            {{ $sub->created_at->format('M d, H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-table>
                    @endif
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-800 shrink-0">
                    <button wire:click="$set('showSubmissionsModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
