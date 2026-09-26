<div class="p-4 sm:p-6 lg:p-8 space-y-6" @if($hasActiveCampaigns) wire:poll.3s @endif>
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Broadcast Campaigns & Mass Outreach</h1>
                <x-tag color="primary" class="font-bold">{{ $campaigns->total() }} Campaigns</x-tag>
                @if($hasActiveCampaigns)
                    <x-tag color="emerald" class="font-bold animate-pulse">Broadcast In Progress</x-tag>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Schedule and dispatch mass broadcasts via official Meta Cloud API or paired WhatsApp Web numbers.
            </p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <x-button wire:click="exportLogsCsv" variant="default" size="sm" title="Export campaign delivery logs">
                <svg class="w-4 h-4 mr-1 text-gray-500 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Logs (CSV)</span>
            </x-button>

            <x-button wire:click="setTab('create')" variant="solid" size="sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Campaign</span>
            </x-button>
        </div>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between gap-2 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-xs font-semibold border border-blue-200 dark:border-blue-800/50 flex items-center justify-between gap-2 shadow-xs">
            <span>{{ session('info') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-blue-600 hover:text-blue-800">&times;</button>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <x-tabs>
        <x-tab-item wire:click="setTab('all')" :active="$activeTab === 'all'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>All Campaigns ({{ $campaigns->total() }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('create')" :active="$activeTab === 'create'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span>Campaign Wizard</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('templates')" :active="$activeTab === 'templates'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Message Templates ({{ count($templates) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('logs')" :active="$activeTab === 'logs'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Delivery Logs</span>
        </x-tab-item>
    </x-tabs>

    <!-- ============================================================== -->
    <!-- TAB 1: ALL CAMPAIGNS                                           -->
    <!-- ============================================================== -->
    @if($activeTab === 'all')
        <x-card gutterless class="overflow-hidden">
            @if($campaigns->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No campaigns created yet</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                        Launch your first WhatsApp broadcast to reach hundreds or thousands of customers instantly.
                    </p>
                    <x-button wire:click="setTab('create')" variant="solid" size="sm" class="mt-2">
                        Create Broadcast
                    </x-button>
                </div>
            @else
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Campaign Name</th>
                            <th>Channel Protocol</th>
                            <th>Template / Format</th>
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
                                    <div class="font-bold text-gray-900 dark:text-white text-xs">{{ $camp->name }}</div>
                                    <div class="text-[11px] text-gray-400 font-mono mt-0.5">ID: #{{ $camp->id }}</div>
                                </td>
                                <td>
                                    @if($camp->type === 'qr_broadcast')
                                        <div class="flex items-center gap-1.5">
                                            <x-tag color="emerald" class="text-[10px] font-bold">QR Web Session</x-tag>
                                            @if($camp->instance_id)
                                                <span class="text-[10px] text-gray-400 font-mono">({{ $camp->instance_id }})</span>
                                            @endif
                                        </div>
                                    @else
                                        <x-tag color="primary" class="text-[10px] font-bold">Meta Cloud API</x-tag>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-1">
                                        <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                            {{ $camp->template_name }}
                                        </span>
                                        @if($camp->media_type && $camp->media_type !== 'none')
                                            <x-tag color="amber" class="text-[9px] uppercase">{{ $camp->media_type }}</x-tag>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-xs font-medium text-gray-600 dark:text-gray-300 capitalize">
                                    {{ $camp->target_type }}
                                </td>
                                <td>
                                    <div class="w-44">
                                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                            <span>{{ $camp->sent_count }} / {{ $camp->total_recipients }}</span>
                                            <span class="font-bold">{{ $pct }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-primary h-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($camp->status === 'completed')
                                        <x-tag color="emerald" class="font-bold text-[10px]">Completed</x-tag>
                                    @elseif($camp->status === 'processing')
                                        <x-tag color="primary" class="font-bold text-[10px] animate-pulse">Sending...</x-tag>
                                    @elseif($camp->status === 'paused')
                                        <x-tag color="amber" class="font-bold text-[10px]">Paused</x-tag>
                                    @elseif($camp->status === 'scheduled')
                                        <x-tag color="default" class="font-bold text-[10px]">Scheduled</x-tag>
                                    @else
                                        <x-tag color="default" class="text-[10px] capitalize">{{ $camp->status }}</x-tag>
                                    @endif
                                </td>
                                <td class="text-xs text-gray-500">
                                    {{ $camp->created_at->format('M d, H:i') }}
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($camp->status === 'processing')
                                            <button wire:click="pauseCampaign({{ $camp->id }})" title="Pause Broadcast" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </button>
                                        @elseif($camp->status === 'paused')
                                            <button wire:click="resumeCampaign({{ $camp->id }})" title="Resume Broadcast" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </button>
                                        @endif

                                        <button wire:click="runTestBroadcast({{ $camp->id }})" title="Run Instant 3-Contact Test" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-xs font-semibold">
                                            ⚡ Test
                                        </button>

                                        <x-button wire:click="viewLogs({{ $camp->id }})" variant="default" size="xs">
                                            Logs
                                        </x-button>

                                        <button wire:click="deleteCampaign({{ $camp->id }})" wire:confirm="Delete this campaign and all its delivery logs?" class="p-1.5 text-rose-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
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

    <!-- ============================================================== -->
    <!-- TAB 2: 5-STEP CREATE CAMPAIGN WIZARD                           -->
    <!-- ============================================================== -->
    @if($activeTab === 'create')
        <div class="space-y-6">
            <!-- 5-Step Interactive Wizard Header Breadcrumbs -->
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700 shadow-xs">
                <div class="grid grid-cols-5 gap-2 text-center text-xs">
                    <button type="button" wire:click="setWizardStep(1)" class="p-2.5 rounded-xl transition-all {{ $wizardStep === 1 ? 'bg-primary text-white font-bold shadow-xs' : ($wizardStep > 1 ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 font-semibold' : 'text-gray-400') }}">
                        <span class="block text-[10px] uppercase tracking-wider">Step 1</span>
                        <span class="truncate block">Channel & Title</span>
                    </button>
                    <button type="button" wire:click="setWizardStep(2)" class="p-2.5 rounded-xl transition-all {{ $wizardStep === 2 ? 'bg-primary text-white font-bold shadow-xs' : ($wizardStep > 2 ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 font-semibold' : 'text-gray-400') }}">
                        <span class="block text-[10px] uppercase tracking-wider">Step 2</span>
                        <span class="truncate block">Target Audience</span>
                    </button>
                    <button type="button" wire:click="setWizardStep(3)" class="p-2.5 rounded-xl transition-all {{ $wizardStep === 3 ? 'bg-primary text-white font-bold shadow-xs' : ($wizardStep > 3 ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 font-semibold' : 'text-gray-400') }}">
                        <span class="block text-[10px] uppercase tracking-wider">Step 3</span>
                        <span class="truncate block">Message & Media</span>
                    </button>
                    <button type="button" wire:click="setWizardStep(4)" class="p-2.5 rounded-xl transition-all {{ $wizardStep === 4 ? 'bg-primary text-white font-bold shadow-xs' : ($wizardStep > 4 ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 font-semibold' : 'text-gray-400') }}">
                        <span class="block text-[10px] uppercase tracking-wider">Step 4</span>
                        <span class="truncate block">Variables Mapping</span>
                    </button>
                    <button type="button" wire:click="setWizardStep(5)" class="p-2.5 rounded-xl transition-all {{ $wizardStep === 5 ? 'bg-primary text-white font-bold shadow-xs' : 'text-gray-400' }}">
                        <span class="block text-[10px] uppercase tracking-wider">Step 5</span>
                        <span class="truncate block">Pacing & Launch</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Step Content -->
                <div class="lg:col-span-2">
                    <x-card bodyClass="p-6 sm:p-8 space-y-6">
                        <!-- ==================== STEP 1: CHANNEL & TITLE ==================== -->
                        @if($wizardStep === 1)
                            <div class="space-y-5">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Step 1: Campaign Title & Delivery Channel</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Select between official Meta Cloud API or your paired WhatsApp Web lines.</p>
                                </div>

                                <x-form-item label="Campaign Title" :required="true" :error="$errors->first('name')">
                                    <x-input wire:model="name" placeholder="e.g. Autumn Flash Sale Announcement 2026" :invalid="$errors->has('name')" />
                                </x-form-item>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Delivery Channel</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <label class="flex items-start gap-3 p-4 rounded-xl border {{ $channelType === 'meta_api' ? 'border-primary bg-primary-subtle ring-2 ring-primary/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer transition-all">
                                            <input type="radio" wire:model.live="channelType" value="meta_api" class="text-primary focus:ring-primary mt-0.5">
                                            <div>
                                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                    <span>Official Meta Cloud API</span>
                                                    <x-tag color="primary" class="text-[9px]">Verified</x-tag>
                                                </div>
                                                <div class="text-[11px] text-gray-500 mt-0.5">High volume, 24-hour service window, zero ban risk</div>
                                            </div>
                                        </label>

                                        <label class="flex items-start gap-3 p-4 rounded-xl border {{ $channelType === 'qr_session' ? 'border-primary bg-primary-subtle ring-2 ring-primary/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer transition-all">
                                            <input type="radio" wire:model.live="channelType" value="qr_session" class="text-primary focus:ring-primary mt-0.5">
                                            <div>
                                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                    <span>Paired WhatsApp Web (QR)</span>
                                                    <x-tag color="emerald" class="text-[9px]">Free</x-tag>
                                                </div>
                                                <div class="text-[11px] text-gray-500 mt-0.5">Direct device sync, custom rich text, media, zero per-message fee</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                @if ($channelType === 'qr_session')
                                    <x-form-item label="Dispatch Line (Paired WhatsApp Device)" :required="true" :error="$errors->first('qrDeviceId')">
                                        <x-select wire:model="qrDeviceId">
                                            @foreach ($pairedDevices as $d)
                                                <option value="{{ $d['id'] }}">{{ $d['name'] }} ({{ $d['phone'] }})</option>
                                            @endforeach
                                        </x-select>
                                    </x-form-item>
                                @endif
                            </div>
                        @endif

                        <!-- ==================== STEP 2: AUDIENCE SELECTION ==================== -->
                        @if($wizardStep === 2)
                            <div class="space-y-5">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Step 2: Recipient Target Audience</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Choose which contacts will receive this broadcast campaign.</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'all' ? 'border-primary bg-primary-subtle ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                                        <input type="radio" wire:model.live="targetType" value="all" class="text-primary focus:ring-primary">
                                        <div>
                                            <div class="text-xs font-bold text-gray-900 dark:text-white">All Contacts</div>
                                            <div class="text-[11px] text-gray-500">Entire workspace</div>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'phonebook' ? 'border-primary bg-primary-subtle ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer">
                                        <input type="radio" wire:model.live="targetType" value="phonebook" class="text-primary focus:ring-primary">
                                        <div>
                                            <div class="text-xs font-bold text-gray-900 dark:text-white">Phonebook Group</div>
                                            <div class="text-[11px] text-gray-500">Segmented list</div>
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
                                    <x-form-item label="Select Phonebook Group" :required="true" :error="$errors->first('targetId')">
                                        <x-select wire:model="targetId">
                                            <option value="">Choose a Phonebook...</option>
                                            @foreach($phonebooks as $pb)
                                                <option value="{{ $pb->id }}">{{ $pb->name }} ({{ $pb->contacts_count }} contacts)</option>
                                            @endforeach
                                        </x-select>
                                    </x-form-item>
                                @elseif($targetType === 'tags')
                                    <x-form-item label="Select Tag Segment" :required="true" :error="$errors->first('targetId')">
                                        <x-select wire:model="targetId">
                                            <option value="">Choose a Tag...</option>
                                            @foreach($tags as $tg)
                                                <option value="{{ $tg->id }}">{{ $tg->name }}</option>
                                            @endforeach
                                        </x-select>
                                    </x-form-item>
                                @endif
                            </div>
                        @endif

                        <!-- ==================== STEP 3: MESSAGE & MEDIA CRAFTING ==================== -->
                        @if($wizardStep === 3)
                            <div class="space-y-5">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Step 3: Message & Media Content</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Compose your campaign payload with optional media attachment.</p>
                                </div>

                                @if ($channelType === 'meta_api')
                                    <x-form-item label="Approved Meta WhatsApp Template" :required="true">
                                        <x-select wire:model="templateName">
                                            @foreach($templates as $tmpl)
                                                <option value="{{ $tmpl['name'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }} - {{ strtoupper($tmpl['language']) }})</option>
                                            @endforeach
                                        </x-select>
                                    </x-form-item>
                                @else
                                    <x-form-item label="Custom Message Body" :required="true" :error="$errors->first('customMessageText')">
                                        <textarea wire:model.live="customMessageText" rows="5" class="input text-xs w-full font-sans" placeholder="Hello @{{name}}, here is your exclusive promo update! Use coupon VIP2026 at checkout."></textarea>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="text-[11px] text-gray-400">Insert tag:</span>
                                            <button type="button" @click="$wire.set('customMessageText', $wire.customMessageText + ' @{{name}}')" class="px-2 py-0.5 text-[10px] font-mono bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200">+ @{{name}}</button>
                                            <button type="button" @click="$wire.set('customMessageText', $wire.customMessageText + ' @{{phone}}')" class="px-2 py-0.5 text-[10px] font-mono bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200">+ @{{phone}}</button>
                                            <button type="button" @click="$wire.set('customMessageText', $wire.customMessageText + ' @{{first_name}}')" class="px-2 py-0.5 text-[10px] font-mono bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200">+ @{{first_name}}</button>
                                        </div>
                                    </x-form-item>

                                    <!-- Media Attachment Selector -->
                                    <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Media Attachment</label>
                                        <div class="grid grid-cols-4 gap-2">
                                            <label class="p-3 text-center rounded-xl border {{ $mediaType === 'none' ? 'border-primary bg-primary-subtle font-bold' : 'border-gray-200 dark:border-gray-700' }} cursor-pointer text-xs">
                                                <input type="radio" wire:model.live="mediaType" value="none" class="sr-only">
                                                <span>None (Text)</span>
                                            </label>
                                            <label class="p-3 text-center rounded-xl border {{ $mediaType === 'image' ? 'border-primary bg-primary-subtle font-bold' : 'border-gray-200 dark:border-gray-700' }} cursor-pointer text-xs">
                                                <input type="radio" wire:model.live="mediaType" value="image" class="sr-only">
                                                <span>🖼️ Image</span>
                                            </label>
                                            <label class="p-3 text-center rounded-xl border {{ $mediaType === 'document' ? 'border-primary bg-primary-subtle font-bold' : 'border-gray-200 dark:border-gray-700' }} cursor-pointer text-xs">
                                                <input type="radio" wire:model.live="mediaType" value="document" class="sr-only">
                                                <span>📄 Document</span>
                                            </label>
                                            <label class="p-3 text-center rounded-xl border {{ $mediaType === 'video' ? 'border-primary bg-primary-subtle font-bold' : 'border-gray-200 dark:border-gray-700' }} cursor-pointer text-xs">
                                                <input type="radio" wire:model.live="mediaType" value="video" class="sr-only">
                                                <span>🎬 Video</span>
                                            </label>
                                        </div>

                                        @if($mediaType !== 'none')
                                            <x-form-item label="Media File URL" :required="true" :error="$errors->first('mediaUrl')">
                                                <x-input wire:model.live="mediaUrl" placeholder="https://example.com/assets/promo-banner.jpg" class="font-mono text-xs" />
                                            </x-form-item>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- ==================== STEP 4: VARIABLE MAPPING ==================== -->
                        @if($wizardStep === 4)
                            <div class="space-y-5">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Step 4: Dynamic Variable Interpolation</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Map template parameters to contact attributes.</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-form-item label="Parameter @{{1}}">
                                        <x-select wire:model="templateVariables.1">
                                            <option value="name">Contact Full Name</option>
                                            <option value="first_name">Contact First Name</option>
                                            <option value="phone">Phone Number</option>
                                        </x-select>
                                    </x-form-item>

                                    <x-form-item label="Parameter @{{2}}">
                                        <x-select wire:model="templateVariables.2">
                                            <option value="phone">Phone Number</option>
                                            <option value="name">Contact Full Name</option>
                                            <option value="company_name">Company Name</option>
                                            <option value="order_id">Order Reference</option>
                                        </x-select>
                                    </x-form-item>
                                </div>
                            </div>
                        @endif

                        <!-- ==================== STEP 5: PACING & LAUNCH ==================== -->
                        @if($wizardStep === 5)
                            <div class="space-y-5">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Step 5: Anti-Ban Pacing & Schedule</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Control jitter delays and dispatch timing to protect number reputation.</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-form-item label="Min Delay (Seconds / Message)">
                                        <x-input type="number" wire:model="delayMin" min="2" max="60" />
                                    </x-form-item>

                                    <x-form-item label="Max Delay (Seconds / Message)">
                                        <x-input type="number" wire:model="delayMax" min="5" max="120" />
                                    </x-form-item>
                                </div>

                                <div class="p-4 rounded-xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-center justify-between gap-4">
                                    <div>
                                        <span class="font-bold text-xs text-gray-800 dark:text-gray-200 block">Anti-Ban Zero-Width Suffix</span>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400 block">Appends invisible Unicode variations to prevent hash duplicate spam triggers.</span>
                                    </div>
                                    <x-switcher wire:model="randomSuffix" />
                                </div>

                                <x-form-item label="Scheduled Launch Time (Leave empty for immediate)">
                                    <x-input type="datetime-local" wire:model="scheduledAt" />
                                </x-form-item>
                            </div>
                        @endif

                        <!-- Wizard Footer Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div>
                                @if($wizardStep > 1)
                                    <x-button type="button" wire:click="prevStep" variant="default" size="sm">
                                        &larr; Back
                                    </x-button>
                                @else
                                    <x-button type="button" wire:click="setTab('all')" variant="plain" size="sm">
                                        Cancel
                                    </x-button>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if($wizardStep === 5)
                                    <x-button type="button" wire:click="runTestBroadcast" variant="default" size="sm" class="text-emerald-600">
                                        ⚡ Run 3-Contact Test
                                    </x-button>

                                    <x-button type="button" wire:click="createCampaign" variant="solid" size="sm">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        <span>Launch Broadcast Campaign</span>
                                    </x-button>
                                @else
                                    <x-button type="button" wire:click="nextStep" variant="solid" size="sm">
                                        <span>Next Step &rarr;</span>
                                    </x-button>
                                @endif
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Right Column: Live Message Preview Card -->
                <div class="space-y-6">
                    <x-card bodyClass="p-6 space-y-4">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">WhatsApp Phone Preview</h3>
                        
                        <!-- Simulated WhatsApp Message Bubble -->
                        <div class="rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 p-4 border border-emerald-100 dark:border-emerald-900/30 space-y-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">WA</div>
                                <div>
                                    <span class="text-xs font-bold text-gray-900 dark:text-white block leading-none">Your Business</span>
                                    <span class="text-[10px] text-gray-400">Verified WhatsApp Channel</span>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-white dark:bg-gray-800 shadow-xs border border-gray-100 dark:border-gray-700 text-xs text-gray-800 dark:text-gray-200 leading-relaxed space-y-2">
                                @if($mediaType !== 'none' && !empty($mediaUrl))
                                    <div class="rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 p-2 text-center text-[11px] font-mono text-gray-500">
                                        📎 [Attached: {{ strtoupper($mediaType) }}]
                                    </div>
                                @endif

                                <div>
                                    @if ($channelType === 'meta_api')
                                        @php
                                            $selectedTmpl = collect($templates)->firstWhere('name', $templateName) ?? $templates[0];
                                        @endphp
                                        {{ $selectedTmpl['body'] }}
                                    @else
                                        {{ $customMessageText }}
                                    @endif
                                </div>
                                <div class="text-[10px] text-gray-400 text-right mt-1">10:45 AM • ✓✓</div>
                            </div>
                        </div>

                        <div class="space-y-2 text-xs text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex justify-between">
                                <span>Selected Channel:</span>
                                <strong class="text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $channelType) }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Recipients Target:</span>
                                <strong class="text-gray-900 dark:text-white capitalize">{{ $targetType }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Pacing Interval:</span>
                                <strong class="text-gray-900 dark:text-white font-mono">{{ $delayMin }}s – {{ $delayMax }}s jitter</strong>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 3: MESSAGE TEMPLATES EXPLORER                              -->
    <!-- ============================================================== -->
    @if($activeTab === 'templates')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Approved Meta WhatsApp Templates</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pre-approved message templates verified through Meta WhatsApp Business Manager.</p>
                </div>
                <x-button wire:click="syncTemplates" variant="solid" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Sync Meta Templates</span>
                </x-button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($templates as $tmpl)
                    <x-card bodyClass="p-6 flex flex-col justify-between h-full space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-xs text-gray-900 dark:text-white">{{ $tmpl['name'] }}</span>
                                    <x-tag color="primary" class="text-[9px] uppercase font-bold">{{ $tmpl['category'] }}</x-tag>
                                </div>
                                <x-tag color="emerald" class="font-bold text-[10px]">
                                    {{ $tmpl['status'] }}
                                </x-tag>
                            </div>

                            <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-sans">
                                {{ $tmpl['body'] }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500">
                            <span>Language: <strong class="text-gray-800 dark:text-gray-200">{{ strtoupper($tmpl['language']) }}</strong></span>
                            <x-button wire:click="$set('templateName', '{{ $tmpl['name'] }}'); setTab('create'); setWizardStep(3);" variant="default" size="xs">
                                Use in Broadcast
                            </x-button>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 4: GRANULAR DELIVERY LOGS                                  -->
    <!-- ============================================================== -->
    @if($activeTab === 'logs')
        <x-card gutterless class="overflow-hidden">
            <!-- Filter Toolbar -->
            <div class="p-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3 flex-1 flex-wrap">
                    <div class="relative w-full sm:w-64">
                        <x-input 
                            type="text" 
                            wire:model.live.debounce.300ms="logSearch" 
                            placeholder="Search phone number..." 
                            size="sm"
                        />
                    </div>

                    <x-select wire:model.live="filterCampaignId" placeholder="All Campaigns" class="!py-1 !text-xs">
                        <option value="">All Campaigns</option>
                        @foreach($campaigns as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </x-select>

                    <x-select wire:model.live="logStatus" class="!py-1 !text-xs">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="sent">Sent</option>
                        <option value="delivered">Delivered</option>
                        <option value="read">Read</option>
                        <option value="failed">Failed</option>
                    </x-select>
                </div>

                <x-button wire:click="exportLogsCsv" variant="default" size="xs">
                    Export Filtered CSV
                </x-button>
            </div>

            <!-- Logs Table -->
            @if($logs->isEmpty())
                <div class="p-12 text-center text-xs text-gray-500">
                    No delivery logs found matching the selected filters.
                </div>
            @else
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Campaign</th>
                            <th>Status</th>
                            <th>Message ID / Reference</th>
                            <th>Sent Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <div class="font-bold text-gray-900 dark:text-white text-xs">
                                        {{ $log->contact?->name ?? 'Contact' }}
                                    </div>
                                    <div class="font-mono text-[11px] text-gray-500 dark:text-gray-400">
                                        {{ $log->phone }}
                                    </div>
                                </td>
                                <td>
                                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $log->campaign->name ?? 'Campaign #' . $log->campaign_id }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->status === 'delivered' || $log->status === 'read')
                                        <x-tag color="emerald" class="text-[10px] font-bold capitalize">{{ $log->status }}</x-tag>
                                    @elseif($log->status === 'sent')
                                        <x-tag color="primary" class="text-[10px] font-bold">Sent</x-tag>
                                    @elseif($log->status === 'failed')
                                        <x-tag color="rose" class="text-[10px] font-bold">Failed</x-tag>
                                    @else
                                        <x-tag color="default" class="text-[10px] capitalize">{{ $log->status }}</x-tag>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-mono text-[11px] text-gray-400">
                                        {{ $log->external_message_id ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-xs text-gray-500">
                                    {{ $log->created_at->format('M d, H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $logs->links() }}
                </div>
            @endif
        </x-card>
    @endif
</div>
