<div class="p-4 sm:p-6 lg:p-8 space-y-6" wire:poll.5s>
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Broadcast Campaigns</h1>
                <x-tag color="primary" class="font-bold">{{ $campaigns->total() }} Campaigns</x-tag>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Schedule and dispatch high-throughput template broadcasts via Meta WhatsApp Cloud API.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-button wire:click="setTab('create')" variant="solid" size="sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Campaign</span>
            </x-button>
        </div>
    </div>

    <!-- Sub-Navbar Tabs as Segments -->
    <x-segment>
        <x-segment-item wire:click="setTab('all')" :active="$activeTab === 'all'">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>All Campaigns ({{ $campaigns->total() }})</span>
            </span>
        </x-segment-item>
        <x-segment-item wire:click="setTab('create')" :active="$activeTab === 'create'">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Campaign Wizard</span>
            </span>
        </x-segment-item>
        <x-segment-item wire:click="setTab('templates')" :active="$activeTab === 'templates'">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Message Templates</span>
            </span>
        </x-segment-item>
        <x-segment-item wire:click="setTab('logs')" :active="$activeTab === 'logs'">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Delivery Logs</span>
            </span>
        </x-segment-item>
    </x-segment>

    <!-- Tab 1: All Campaigns -->
    @if($activeTab === 'all')
        <x-card gutterless class="overflow-hidden">
            @if($campaigns->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary-subtle text-primary flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No campaigns created yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Launch your first WhatsApp broadcast to reach hundreds or thousands of contacts instantly.
                    </p>
                    <x-button wire:click="setTab('create')" variant="solid" size="sm" class="mt-4">
                        Create Broadcast
                    </x-button>
                </div>
            @else
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Campaign Name</th>
                            <th>Template</th>
                            <th>Target Audience</th>
                            <th>Progress / Sent</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $camp)
                            @php
                                $pct = $camp->total_recipients > 0 ? round(($camp->sent_count / $camp->total_recipients) * 100) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $camp->name }}</div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Official Meta Cloud API</span>
                                    </div>
                                </td>
                                <td>
                                    <x-tag color="gray" class="font-mono">
                                        {{ $camp->template_name }} ({{ $camp->template_language }})
                                    </x-tag>
                                </td>
                                <td class="text-xs font-medium text-gray-600 dark:text-gray-300 capitalize">
                                    {{ $camp->target_type }}
                                </td>
                                <td>
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
                                <td>
                                    @if($camp->status === 'completed')
                                        <x-tag color="emerald" class="font-semibold">
                                            Completed
                                        </x-tag>
                                    @elseif($camp->status === 'processing')
                                        <x-tag color="primary" class="font-semibold animate-pulse">
                                            Sending...
                                        </x-tag>
                                    @elseif($camp->status === 'paused')
                                        <x-tag color="amber" class="font-semibold">
                                            Paused
                                        </x-tag>
                                    @else
                                        <x-tag color="gray">
                                            {{ ucfirst($camp->status) }}
                                        </x-tag>
                                    @endif
                                </td>
                                <td class="text-xs text-gray-500">
                                    {{ $camp->created_at->format('M d, H:i') }}
                                </td>
                                <td class="text-right">
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
                                        <x-button wire:click="viewLogs({{ $camp->id }})" variant="default" size="xs">
                                            Logs
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </x-card>
    @endif

    <!-- Tab 2: Create Campaign Wizard -->
    @if($activeTab === 'create')
        <div class="max-w-3xl mx-auto">
            <x-card bodyClass="p-6 sm:p-8 space-y-6">
                <!-- Wizard Progress Steps -->
                <x-steps class="pb-6 border-b border-gray-100 dark:border-gray-800">
                    <x-step-item :step="1" status="complete" title="Target Audience" description="Select contacts list" />
                    <x-step-item :step="2" status="in_progress" title="Template Mapping" description="Dynamic parameters" />
                    <x-step-item :step="3" status="pending" :isLast="true" title="Dispatch" description="Meta Cloud API" />
                </x-steps>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-primary-subtle text-primary flex items-center justify-center text-sm font-black">1</span>
                    <span>Configure WhatsApp Broadcast</span>
                </h2>

                <form wire:submit.prevent="createCampaign" class="space-y-6">
                    <!-- Campaign Name -->
                    <x-form-item label="Campaign Title" :required="true" :error="$errors->first('name')">
                        <x-input wire:model="name" placeholder="e.g. Black Friday VIP Announcement 2026" prefix-icon="broadcast" :invalid="$errors->has('name')" />
                    </x-form-item>

                    <!-- Target Audience -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Target Audience</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'all' ? 'border-primary bg-primary-subtle ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                                <input type="radio" wire:model.live="targetType" value="all" class="text-primary focus:ring-primary">
                                <div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white">All Contacts</div>
                                    <div class="text-[11px] text-gray-500">Workspace directory</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'phonebook' ? 'border-primary bg-primary-subtle ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                                <input type="radio" wire:model.live="targetType" value="phonebook" class="text-primary focus:ring-primary">
                                <div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white">Phonebook Group</div>
                                    <div class="text-[11px] text-gray-500">Specific list</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'tags' ? 'border-primary bg-primary-subtle ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                                <input type="radio" wire:model.live="targetType" value="tags" class="text-primary focus:ring-primary">
                                <div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white">Tag Segment</div>
                                    <div class="text-[11px] text-gray-500">Filtered by tag</div>
                                </div>
                            </label>
                        </div>

                        @if($targetType === 'phonebook')
                            <x-select wire:model="targetId" placeholder="Select a Phonebook Group...">
                                @foreach($phonebooks as $pb)
                                    <option value="{{ $pb->id }}">{{ $pb->name }} ({{ $pb->contacts_count ?? $pb->contacts()->count() }} contacts)</option>
                                @endforeach
                            </x-select>
                        @elseif($targetType === 'tags')
                            <x-select wire:model="targetId" placeholder="Select a Tag Segment...">
                                @foreach($tags as $tg)
                                    <option value="{{ $tg->id }}">{{ $tg->name }}</option>
                                @endforeach
                            </x-select>
                        @endif
                    </div>

                    <!-- Template Selection -->
                    <x-form-item label="Meta Cloud Template">
                        <x-select wire:model="templateName">
                            @foreach($templates as $tmpl)
                                <option value="{{ $tmpl['name'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }} - {{ $tmpl['language'] }})</option>
                            @endforeach
                        </x-select>
                    </x-form-item>

                    <!-- Dynamic Variable Mapping -->
                    <div class="p-4 rounded-xl bg-gray-50/70 dark:bg-gray-800/40 border border-gray-200/80 dark:border-gray-800 space-y-3">
                        <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Dynamic Variable Interpolation</h3>
                        <p class="text-xs text-gray-500">Variables like <code class="text-primary font-mono font-bold">&#123;&#123;1&#125;&#125;</code> inside the approved template are dynamically replaced per recipient.</p>
                        
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Parameter &#123;&#123;1&#125;&#125;</label>
                                <x-select wire:model="templateVariables.1" size="sm">
                                    <option value="name">Contact Full Name</option>
                                    <option value="first_name">Contact First Name</option>
                                    <option value="phone">Phone Number</option>
                                </x-select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Parameter &#123;&#123;2&#125;&#125;</label>
                                <x-select wire:model="templateVariables.2" size="sm">
                                    <option value="phone">Phone Number</option>
                                    <option value="name">Contact Full Name</option>
                                    <option value="custom.promo">Custom Field: Promo</option>
                                </x-select>
                            </div>
                        </div>
                    </div>

                    <!-- Dispatch Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <x-button type="button" wire:click="setTab('all')" variant="default" size="md">
                            Cancel
                        </x-button>
                        <x-button type="submit" variant="solid" size="md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Dispatch Broadcast</span>
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    @endif

    <!-- Tab 3: Message Templates Explorer -->
    @if($activeTab === 'templates')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($templates as $tmpl)
                <x-card bodyClass="p-5 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <x-tag color="primary" class="font-mono font-bold">
                                {{ $tmpl['name'] }}
                            </x-tag>
                            <x-tag color="emerald" class="font-bold">
                                {{ $tmpl['status'] }}
                            </x-tag>
                        </div>
                        <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-sans mb-3">
                            {{ $tmpl['body'] }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500">
                        <span>Category: <strong>{{ $tmpl['category'] }}</strong></span>
                        <span>Language: <strong>{{ strtoupper($tmpl['language']) }}</strong></span>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif

    <!-- Tab 4: Granular Delivery Logs -->
    @if($activeTab === 'logs')
        <x-card gutterless class="overflow-hidden space-y-4 p-6">
            <!-- Filter toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 flex-1 max-w-sm">
                    <x-input 
                        type="text" 
                        wire:model.live.debounce.300ms="logSearch" 
                        placeholder="Search phone number..." 
                        prefix-icon="chat"
                        size="sm"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <x-select wire:model.live="logStatus" size="sm">
                        <option value="all">All Delivery Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="sent">Sent</option>
                        <option value="delivered">Delivered</option>
                        <option value="read">Read</option>
                        <option value="failed">Failed</option>
                    </x-select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Recipient Phone</th>
                            <th>Variables</th>
                            <th>Status</th>
                            <th>WAM ID / Reason</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="font-semibold text-gray-900 dark:text-white">
                                    {{ $log->campaign->name ?? ('Campaign #' . $log->campaign_id) }}
                                </td>
                                <td class="font-mono font-medium text-gray-700 dark:text-gray-300">
                                    {{ $log->phone }}
                                </td>
                                <td class="text-gray-500 font-mono text-[11px]">
                                    {{ json_encode($log->variables_sent) }}
                                </td>
                                <td>
                                    @if($log->status === 'read')
                                        <x-tag color="primary" class="font-bold">
                                            ✓✓ Read
                                        </x-tag>
                                    @elseif($log->status === 'delivered')
                                        <x-tag color="gray" class="font-bold">
                                            ✓✓ Delivered
                                        </x-tag>
                                    @elseif($log->status === 'sent')
                                        <x-tag color="gray">
                                            ✓ Sent
                                        </x-tag>
                                    @elseif($log->status === 'failed')
                                        <x-tag color="rose" class="font-bold">
                                            ✕ Failed
                                        </x-tag>
                                    @else
                                        <x-tag color="gray">Pending</x-tag>
                                    @endif
                                </td>
                                <td class="font-mono text-[11px] text-gray-500">
                                    {{ $log->error_message ?: ($log->external_message_id ?: '—') }}
                                </td>
                                <td class="text-xs text-gray-500">
                                    {{ $log->created_at->format('M d, H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">No logs found matching your filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </div>
            <div>
                {{ $logs->links() }}
            </div>
        </x-card>
    @endif
</div>
