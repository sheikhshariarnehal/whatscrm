<div class="p-4 sm:p-6 lg:p-8 space-y-6" wire:poll.5s>
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Broadcast Campaigns</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Schedule and dispatch high-throughput template broadcasts via Meta WhatsApp Cloud API.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button 
                wire:click="setTab('create')" 
                class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-medium text-sm shadow-sm transition-all flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Campaign</span>
            </button>
        </div>
    </div>

    <!-- Sub-Navbar Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800">
        <button 
            wire:click="setTab('all')" 
            class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'all' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>All Campaigns ({{ $campaigns->total() }})</span>
        </button>
        <button 
            wire:click="setTab('create')" 
            class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'create' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span>Campaign Wizard</span>
        </button>
        <button 
            wire:click="setTab('templates')" 
            class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'templates' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Message Templates</span>
        </button>
        <button 
            wire:click="setTab('logs')" 
            class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'logs' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Delivery Logs</span>
        </button>
    </div>

    <!-- Tab 1: All Campaigns -->
    @if($activeTab === 'all')
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
            @if($campaigns->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No campaigns created yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Launch your first WhatsApp broadcast to reach hundreds or thousands of contacts instantly.
                    </p>
                    <button wire:click="setTab('create')" class="mt-4 px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90">
                        Create Broadcast
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/40 text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-4">Campaign Name</th>
                                <th class="px-6 py-4">Template</th>
                                <th class="px-6 py-4">Target Audience</th>
                                <th class="px-6 py-4">Progress / Sent</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($campaigns as $camp)
                                @php
                                    $pct = $camp->total_recipients > 0 ? round(($camp->sent_count / $camp->total_recipients) * 100) : 0;
                                @endphp
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $camp->name }}</div>
                                        <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Official Meta Cloud API</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                            {{ $camp->template_name }} ({{ $camp->template_language }})
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-600 dark:text-gray-300 capitalize">
                                        {{ $camp->target_type }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="w-48">
                                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                                <span>{{ $camp->sent_count }} / {{ $camp->total_recipients }} sent</span>
                                                <span class="font-bold">{{ $pct }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-primary h-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($camp->status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Completed
                                            </span>
                                        @elseif($camp->status === 'processing')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/50 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Sending...
                                            </span>
                                        @elseif($camp->status === 'paused')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Paused
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                                {{ ucfirst($camp->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $camp->created_at->format('M d, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($camp->status === 'processing')
                                                <button wire:click="pauseCampaign({{ $camp->id }})" title="Pause Broadcast" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                            @elseif($camp->status === 'paused')
                                                <button wire:click="resumeCampaign({{ $camp->id }})" title="Resume Broadcast" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                            @endif
                                            <button wire:click="viewLogs({{ $camp->id }})" title="View Delivery Logs" class="px-2.5 py-1 rounded-lg text-xs font-medium text-primary hover:bg-primary/10 border border-primary/20">
                                                Logs
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Tab 2: Create Campaign Wizard -->
    @if($activeTab === 'create')
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm font-black">1</span>
                <span>Configure WhatsApp Broadcast</span>
            </h2>

            <form wire:submit.prevent="createCampaign" class="space-y-6">
                <!-- Campaign Name -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Campaign Title</label>
                    <input 
                        type="text" 
                        wire:model="name" 
                        placeholder="e.g. Black Friday VIP Announcement 2026" 
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                    @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Target Audience -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Target Audience</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'all' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                            <input type="radio" wire:model.live="targetType" value="all" class="text-primary focus:ring-primary">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">All Contacts</div>
                                <div class="text-[11px] text-gray-500">Workspace directory</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'phonebook' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                            <input type="radio" wire:model.live="targetType" value="phonebook" class="text-primary focus:ring-primary">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Phonebook Group</div>
                                <div class="text-[11px] text-gray-500">Specific list</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'tags' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                            <input type="radio" wire:model.live="targetType" value="tags" class="text-primary focus:ring-primary">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Tag Segment</div>
                                <div class="text-[11px] text-gray-500">Filtered by tag</div>
                            </div>
                        </label>
                    </div>

                    @if($targetType === 'phonebook')
                        <select wire:model="targetId" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">Select a Phonebook Group...</option>
                            @foreach($phonebooks as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->name }} ({{ $pb->contacts_count ?? $pb->contacts()->count() }} contacts)</option>
                            @endforeach
                        </select>
                    @elseif($targetType === 'tags')
                        <select wire:model="targetId" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">Select a Tag Segment...</option>
                            @foreach($tags as $tg)
                                <option value="{{ $tg->id }}">{{ $tg->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Template Selection -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Meta Cloud Template</label>
                    <select wire:model="templateName" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @foreach($templates as $tmpl)
                            <option value="{{ $tmpl['name'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }} - {{ $tmpl['language'] }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dynamic Variable Mapping -->
                <div class="p-4 rounded-xl bg-gray-50/70 dark:bg-gray-800/40 border border-gray-200/80 dark:border-gray-800 space-y-3">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Dynamic Variable Interpolation</h3>
                    <p class="text-xs text-gray-500">Variables like <code class="text-primary font-mono font-bold">&#123;&#123;1&#125;&#125;</code> inside the approved template are dynamically replaced per recipient.</p>
                    
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 mb-1">Parameter &#123;&#123;1&#125;&#125;</label>
                            <select wire:model="templateVariables.1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs">
                                <option value="name">Contact Full Name</option>
                                <option value="first_name">Contact First Name</option>
                                <option value="phone">Phone Number</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 mb-1">Parameter &#123;&#123;2&#125;&#125;</label>
                            <select wire:model="templateVariables.2" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs">
                                <option value="phone">Phone Number</option>
                                <option value="name">Contact Full Name</option>
                                <option value="custom.promo">Custom Field: Promo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dispatch Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <button type="button" wire:click="setTab('all')" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span>Dispatch Broadcast</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Tab 3: Message Templates Explorer -->
    @if($activeTab === 'templates')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($templates as $tmpl)
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-5 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-primary/10 text-primary">
                                {{ $tmpl['name'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                {{ $tmpl['status'] }}
                            </span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-sans mb-3">
                            {{ $tmpl['body'] }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500">
                        <span>Category: <strong>{{ $tmpl['category'] }}</strong></span>
                        <span>Language: <strong>{{ strtoupper($tmpl['language']) }}</strong></span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Tab 4: Granular Delivery Logs -->
    @if($activeTab === 'logs')
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden space-y-4 p-6">
            <!-- Filter toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 flex-1 max-w-sm">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="logSearch" 
                        placeholder="Search phone number..." 
                        class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                </div>
                <div class="flex items-center gap-2">
                    <select wire:model.live="logStatus" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white">
                        <option value="all">All Delivery Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="sent">Sent</option>
                        <option value="delivered">Delivered</option>
                        <option value="read">Read</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50/50 dark:bg-gray-800/40 uppercase font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-4 py-3">Campaign</th>
                            <th class="px-4 py-3">Recipient Phone</th>
                            <th class="px-4 py-3">Variables</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">WAM ID / Reason</th>
                            <th class="px-4 py-3">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $log->campaign->name ?? ('Campaign #' . $log->campaign_id) }}
                                </td>
                                <td class="px-4 py-3 font-mono font-medium text-gray-700 dark:text-gray-300">
                                    {{ $log->phone }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ json_encode($log->variables_sent) }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->status === 'read')
                                        <span class="inline-flex items-center gap-1 font-bold text-sky-600 dark:text-sky-400">
                                            ✓✓ Read
                                        </span>
                                    @elseif($log->status === 'delivered')
                                        <span class="inline-flex items-center gap-1 font-bold text-gray-600 dark:text-gray-300">
                                            ✓✓ Delivered
                                        </span>
                                    @elseif($log->status === 'sent')
                                        <span class="inline-flex items-center gap-1 font-medium text-gray-500">
                                            ✓ Sent
                                        </span>
                                    @elseif($log->status === 'failed')
                                        <span class="inline-flex items-center gap-1 font-bold text-rose-500">
                                            ✕ Failed
                                        </span>
                                    @else
                                        <span class="text-gray-400">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-[11px] text-gray-500">
                                    {{ $log->error_message ?: ($log->external_message_id ?: '—') }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $log->created_at->format('M d, H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">No logs found matching your filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
    @endif
</div>
