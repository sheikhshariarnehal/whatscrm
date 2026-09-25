<div class="h-[calc(100vh-4rem)] flex flex-col bg-gray-50/60 dark:bg-gray-950/40 overflow-hidden"
     x-data="{ 
        draggedDealId: null,
        handleDragStart(e, id) {
            this.draggedDealId = id;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', id);
        },
        handleDrop(e, targetStage) {
            e.preventDefault();
            const id = this.draggedDealId || e.dataTransfer.getData('text/plain');
            if (id) {
                $wire.updateDealStage(parseInt(id), targetStage);
            }
            this.draggedDealId = null;
        }
     }">

    <!-- ========================================== -->
    <!-- TOP TOOLBAR & PIPELINE SELECTOR            -->
    <!-- ========================================== -->
    <div class="p-4 sm:px-6 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex flex-col lg:flex-row lg:items-center justify-between gap-4 shrink-0 z-10">
        
        <!-- Title & Pipeline Switcher -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-primary/20 to-indigo-200 dark:from-primary/30 dark:to-indigo-900 flex items-center justify-center text-primary shadow-2xs shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white tracking-tight">CRM Pipeline & Deals</h1>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">
                        Live Sync
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Track and advance WhatsApp sales leads across deal stages</p>
            </div>
        </div>

        <!-- Right Action Controls -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <!-- Search -->
            <div class="relative w-full sm:w-56">
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Filter deals, name, company..." 
                       class="w-full pl-8 pr-3 py-1.5 rounded-xl text-xs bg-gray-100 dark:bg-gray-800 border-none text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-primary/40 focus:bg-white dark:focus:bg-gray-900 transition-all">
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Priority Filter -->
            <select wire:model.live="priorityFilter"
                    class="rounded-xl text-xs border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 py-1.5 px-2.5 font-semibold focus:ring-2 focus:ring-primary/20">
                <option value="all">All Priorities</option>
                <option value="urgent">⚡ Urgent</option>
                <option value="high">🔥 High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>

            <!-- Agent Filter -->
            <select wire:model.live="agentFilter"
                    class="rounded-xl text-xs border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 py-1.5 px-2.5 font-semibold focus:ring-2 focus:ring-primary/20">
                <option value="all">All Agents</option>
                <option value="unassigned">👤 Unassigned</option>
                @foreach ($teamMembers as $member)
                    <option value="{{ $member->id }}">{{ $member->user?->name ?? 'Agent' }}</option>
                @endforeach
            </select>

            <!-- + New Deal Button -->
            <button type="button"
                    wire:click="openNewDealModal"
                    class="px-3.5 py-1.5 rounded-xl bg-primary hover:bg-primary-deep text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm shadow-primary/25 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>New Deal</span>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SUB-NAVBAR TABS (Kanban, Table, Analytics) -->
    <!-- ========================================== -->
    <div class="h-12 px-4 sm:px-6 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between shrink-0 z-10">
        <!-- View Switcher Pills -->
        <div class="flex items-center gap-1">
            <!-- Kanban Board Tab -->
            <button type="button"
                    wire:click="setView('kanban')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $activeView === 'kanban' ? 'bg-primary text-white shadow-2xs font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                <span>Kanban Board</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeView === 'kanban' ? 'bg-white/25 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                    {{ $dealCount }}
                </span>
            </button>

            <!-- All Leads Table Tab -->
            <button type="button"
                    wire:click="setView('table')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $activeView === 'table' ? 'bg-primary text-white shadow-2xs font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span>All Leads Table</span>
            </button>

            <!-- Pipeline Analytics Tab -->
            <button type="button"
                    wire:click="setView('analytics')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $activeView === 'analytics' ? 'bg-primary text-white shadow-2xs font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Pipeline Analytics</span>
            </button>

            <!-- Stage Settings Tab -->
            <button type="button"
                    wire:click="setView('settings')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $activeView === 'settings' ? 'bg-primary text-white shadow-2xs font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Stage Settings</span>
            </button>
        </div>

        <!-- Pipeline Value Snapshot -->
        <div class="hidden md:flex items-center gap-3 text-xs">
            <span class="text-gray-500 dark:text-gray-400">Total Pipeline: <strong class="text-gray-900 dark:text-white font-bold">${{ number_format($totalPipelineValue, 2) }}</strong></span>
            <span class="text-gray-300 dark:text-gray-700">|</span>
            <span class="text-gray-500 dark:text-gray-400">Won Revenue: <strong class="text-emerald-600 dark:text-emerald-400 font-bold">${{ number_format($totalWonValue, 2) }}</strong></span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VIEW 1: KANBAN BOARD (DRAG & DROP)         -->
    <!-- ========================================== -->
    @if ($activeView === 'kanban')
        <div class="flex-1 overflow-x-auto p-4 sm:p-6 flex gap-4 sm:gap-5 items-start">
            
            @foreach ($defaultStages as $stage)
                @php 
                    $stageKey = $stage['key'];
                    $deals = $stageGroups[$stageKey] ?? collect();
                    $stageSum = $stageTotals[$stageKey] ?? 0;
                @endphp

                <!-- Column Container -->
                <div class="w-80 sm:w-88 shrink-0 flex flex-col max-h-full rounded-2xl bg-gray-100/70 dark:bg-gray-900/70 border border-gray-200/80 dark:border-gray-800 shadow-2xs overflow-hidden transition-all"
                     @dragover.prevent
                     @dragenter.prevent
                     @drop="handleDrop($event, '{{ $stageKey }}')">
                    
                    <!-- Stage Header -->
                    <div class="p-3.5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $stage['color'] }}"></span>
                            <div>
                                <h2 class="font-bold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-wider">{{ $stage['title'] }}</h2>
                                <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                    ${{ number_format($stageSum, 0) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $stage['bg_light'] }}">
                                {{ $deals->count() }}
                            </span>
                            <button type="button"
                                    wire:click="openNewDealModal('{{ $stageKey }}')"
                                    class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-200/60 dark:hover:bg-gray-800 transition-colors"
                                    title="Add deal to this stage">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Scrollable Cards Stack -->
                    <div class="flex-1 overflow-y-auto p-3 space-y-3 min-h-[150px]">
                        @forelse ($deals as $conv)
                            <!-- Deal Card -->
                            <div draggable="true"
                                 @dragstart="handleDragStart($event, {{ $conv->id }})"
                                 class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-2xs hover:shadow-md transition-all space-y-2.5 cursor-grab active:cursor-grabbing group">
                                
                                <!-- Card Header: Contact / Company & Priority -->
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white truncate">
                                                {{ $conv->sender_name ?? $conv->sender_mobile }}
                                            </span>
                                            
                                            <!-- Channel Badge -->
                                            @if (in_array($conv->channel, ['whatsapp', 'whatsapp_cloud']))
                                                <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0" title="WhatsApp">
                                                    <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.086.087-.175.18-.075.352.1.173.444.733.953 1.186.656.585 1.21.766 1.383.852.173.086.275.072.376-.044.101-.115.433-.505.548-.678.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                                                </span>
                                            @elseif ($conv->channel === 'instagram')
                                                <span class="w-3.5 h-3.5 rounded-full bg-pink-500 text-white flex items-center justify-center shrink-0" title="Instagram">
                                                    <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/></svg>
                                                </span>
                                            @endif
                                        </div>
                                        @if ($conv->company)
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $conv->company }}</p>
                                        @endif
                                    </div>

                                    <!-- Priority Pill -->
                                    @if ($conv->priority === 'urgent')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 shrink-0">
                                            ⚡ Urgent
                                        </span>
                                    @elseif ($conv->priority === 'high')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 shrink-0">
                                            🔥 High
                                        </span>
                                    @elseif ($conv->priority === 'low')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 shrink-0">
                                            Low
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 shrink-0">
                                            Medium
                                        </span>
                                    @endif
                                </div>

                                <!-- Deal Value & Phone -->
                                <div class="flex items-center justify-between text-xs pt-1">
                                    <span class="font-bold text-sm text-emerald-600 dark:text-emerald-400">
                                        ${{ number_format($conv->deal_value, 2) }}
                                    </span>
                                    <span class="text-[11px] text-gray-400 font-mono">
                                        {{ $conv->sender_mobile }}
                                    </span>
                                </div>

                                <!-- Agent & Tags -->
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-300">
                                        <div class="w-5 h-5 rounded-full bg-primary/20 text-primary flex items-center justify-center text-[10px] font-bold">
                                            {{ substr($conv->assignedMember?->user?->name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="text-[11px] truncate max-w-[100px]">
                                            {{ $conv->assignedMember?->user?->name ?? 'Unassigned' }}
                                        </span>
                                    </div>

                                    @if ($conv->expected_close_at)
                                        <span class="text-[10px] text-gray-400">
                                            Closes {{ $conv->expected_close_at->format('M d') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Card Action Footer -->
                                <div class="pt-2 border-t border-gray-100 dark:border-gray-700/80 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <!-- Launch WhatsApp Inbox Chat -->
                                        <a href="{{ route('inbox') }}" 
                                           class="text-primary hover:text-primary-deep font-semibold text-[11px] flex items-center gap-1 transition-colors"
                                           title="Chat in Live Inbox">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            <span>Chat</span>
                                        </a>

                                        <!-- Edit Deal Button -->
                                        <button type="button"
                                                wire:click="openEditDealModal({{ $conv->id }})"
                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-[11px] font-medium transition-colors">
                                            Edit
                                        </button>
                                    </div>

                                    <!-- Quick Move Dropdown -->
                                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-[11px] font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all shadow-2xs">
                                            <span>Move...</span>
                                            <svg class="w-3 h-3 text-gray-400 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>

                                        <div x-show="open" 
                                             x-transition
                                             class="absolute right-0 bottom-full mb-1.5 w-44 rounded-2xl bg-white dark:bg-gray-900 p-1.5 shadow-2xl border border-gray-100 dark:border-gray-800 z-30 max-h-56 overflow-y-auto"
                                             style="display: none;">
                                            @foreach ($defaultStages as $s)
                                                @if ($s['key'] !== $stageKey)
                                                    <button type="button" 
                                                            wire:click="updateDealStage({{ $conv->id }}, '{{ $s['key'] }}')" 
                                                            @click="open = false" 
                                                            class="w-full text-left px-2.5 py-1.5 text-xs font-semibold rounded-xl flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $s['color'] }}"></span>
                                                        <span class="truncate">{{ $s['title'] }}</span>
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-gray-400 space-y-1">
                                <p>No deals in {{ strtolower($stage['title']) }}</p>
                                <button type="button"
                                        wire:click="openNewDealModal('{{ $stageKey }}')"
                                        class="text-primary hover:underline font-semibold text-[11px]">
                                    + Add First Deal
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ========================================== -->
    <!-- VIEW 2: ALL LEADS TABLE                    -->
    <!-- ========================================== -->
    @if ($activeView === 'table')
        <div class="flex-1 overflow-y-auto p-4 sm:p-6">
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xs overflow-hidden">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800 bg-gray-50/70 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider text-[11px]">
                            <th class="p-4">Lead / Contact</th>
                            <th class="p-4">Company</th>
                            <th class="p-4">Channel</th>
                            <th class="p-4">Stage</th>
                            <th class="p-4">Deal Value</th>
                            <th class="p-4">Priority</th>
                            <th class="p-4">Assigned Agent</th>
                            <th class="p-4">Expected Close</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($conversations as $conv)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $conv->sender_name ?? 'Lead' }}</div>
                                    <div class="text-gray-400 font-mono text-[11px]">{{ $conv->sender_mobile }}</div>
                                </td>
                                <td class="p-4 text-gray-600 dark:text-gray-300 font-medium">
                                    {{ $conv->company ?? '—' }}
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">
                                        WhatsApp
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-primary/10 text-primary">
                                        {{ ucfirst($conv->kanban_stage ?? 'lead') }}
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-emerald-600 dark:text-emerald-400">
                                    ${{ number_format($conv->deal_value, 2) }}
                                </td>
                                <td class="p-4">
                                    @if ($conv->priority === 'urgent')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400">⚡ Urgent</span>
                                    @elseif ($conv->priority === 'high')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400">🔥 High</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Medium</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-700 dark:text-gray-300">
                                    {{ $conv->assignedMember?->user?->name ?? 'Unassigned' }}
                                </td>
                                <td class="p-4 text-gray-400">
                                    {{ $conv->expected_close_at ? $conv->expected_close_at->format('M d, Y') : '—' }}
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <a href="{{ route('inbox') }}" class="text-primary hover:underline font-semibold">Chat</a>
                                    <button wire:click="openEditDealModal({{ $conv->id }})" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-semibold">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-gray-400">No leads match your filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- VIEW 3: PIPELINE ANALYTICS                 -->
    <!-- ========================================== -->
    @if ($activeView === 'analytics')
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">
            <!-- 4 Top KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xs space-y-1">
                    <span class="text-xs font-semibold text-gray-400">Total Pipeline Value</span>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalPipelineValue, 2) }}</p>
                    <span class="text-[11px] text-emerald-500 font-medium">↑ +14.8% vs last month</span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xs space-y-1">
                    <span class="text-xs font-semibold text-gray-400">Closed Won Revenue</span>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($totalWonValue, 2) }}</p>
                    <span class="text-[11px] text-emerald-500 font-medium">↑ 4 active closed deals</span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xs space-y-1">
                    <span class="text-xs font-semibold text-gray-400">Average Deal Size</span>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($averageDealSize, 2) }}</p>
                    <span class="text-[11px] text-gray-400 font-medium">Across {{ $dealCount }} active leads</span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xs space-y-1">
                    <span class="text-xs font-semibold text-gray-400">Pipeline Win Rate</span>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $winRate }}%</p>
                    <span class="text-[11px] text-emerald-500 font-medium">↑ Top quartile performance</span>
                </div>
            </div>

            <!-- Visual Conversion Funnel -->
            <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xs space-y-4">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white">Pipeline Conversion Funnel</h3>
                
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span>1. New Leads (100%)</span>
                            <span>{{ ($stageGroups['lead'] ?? collect())->count() }} Deals (${{ number_format($stageTotals['lead'] ?? 0, 0) }})</span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: 100%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span>2. Contacted (78%)</span>
                            <span>{{ ($stageGroups['contacted'] ?? collect())->count() }} Deals (${{ number_format($stageTotals['contacted'] ?? 0, 0) }})</span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: 78%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span>3. Proposal & Quote (55%)</span>
                            <span>{{ ($stageGroups['proposal'] ?? collect())->count() }} Deals (${{ number_format($stageTotals['proposal'] ?? 0, 0) }})</span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: 55%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span>4. Negotiation (35%)</span>
                            <span>{{ ($stageGroups['negotiation'] ?? collect())->count() }} Deals (${{ number_format($stageTotals['negotiation'] ?? 0, 0) }})</span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full bg-pink-500 rounded-full" style="width: 35%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span>5. Won / Closed (24%)</span>
                            <span>{{ ($stageGroups['won'] ?? collect())->count() }} Deals (${{ number_format($stageTotals['won'] ?? 0, 0) }})</span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: 24%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- VIEW 4: STAGE SETTINGS                     -->
    <!-- ========================================== -->
    @if ($activeView === 'settings')
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 max-w-4xl space-y-6">
            <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xs space-y-4">
                <div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Pipeline Stages & Automations</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Configure default stages, color codes, and automated WhatsApp trigger workflows.</p>
                </div>

                <div class="space-y-3 divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($defaultStages as $s)
                        <div class="pt-3 first:pt-0 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="w-4 h-4 rounded-full" style="background-color: {{ $s['color'] }}"></span>
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-white">{{ $s['title'] }}</h4>
                                    <p class="text-xs text-gray-400">Stage Key: <code class="font-mono">{{ $s['key'] }}</code></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">
                                    ⚡ Automated Sync
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- NEW / EDIT DEAL MODAL                      -->
    <!-- ========================================== -->
    <div x-show="$wire.showDealModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.outside="$wire.set('showDealModal', false)"
             class="w-full max-w-lg rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">
                        {{ $editingDealId ? 'Edit CRM Deal' : 'Create New CRM Deal' }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Add or update lead details in the sales pipeline.</p>
                </div>
                <button type="button" @click="$wire.set('showDealModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form wire:submit.prevent="saveDeal" class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Contact Name *</label>
                        <input wire:model="dealName" 
                               type="text" 
                               placeholder="Jane Doe"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary/20">
                        @error('dealName') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Company / Org</label>
                        <input wire:model="dealCompany" 
                               type="text" 
                               placeholder="Acme Corp"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">WhatsApp / Phone *</label>
                        <input wire:model="dealPhone" 
                               type="text" 
                               placeholder="+1 555-0199"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary/20">
                        @error('dealPhone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Deal Value ($) *</label>
                        <input wire:model="dealValue" 
                               type="number" 
                               step="0.01" 
                               placeholder="2500"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary/20 font-semibold text-emerald-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pipeline Stage *</label>
                        <select wire:model="dealStage"
                                class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold focus:ring-2 focus:ring-primary/20">
                            @foreach ($defaultStages as $s)
                                <option value="{{ $s['key'] }}">{{ $s['title'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                        <select wire:model="dealPriority"
                                class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold focus:ring-2 focus:ring-primary/20">
                            <option value="urgent">⚡ Urgent</option>
                            <option value="high">🔥 High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Assigned Agent</label>
                        <select wire:model="dealAssignedMemberId"
                                class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold focus:ring-2 focus:ring-primary/20">
                            <option value="">👤 Unassigned</option>
                            @foreach ($teamMembers as $m)
                                <option value="{{ $m->id }}">{{ $m->user?->name ?? 'Agent' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Expected Close Date</label>
                        <input wire:model="dealExpectedCloseAt"
                               type="date"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    @if ($editingDealId)
                        <button type="button"
                                wire:click="deleteDeal({{ $editingDealId }})"
                                @click="$wire.set('showDealModal', false)"
                                class="px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                            Delete Deal
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="$wire.set('showDealModal', false)" 
                                class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-deep text-white text-xs font-semibold shadow-sm shadow-primary/25 transition-all">
                            {{ $editingDealId ? 'Update Deal' : 'Create Deal' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
