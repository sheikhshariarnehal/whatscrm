<div class="p-4 sm:p-6 lg:p-8 space-y-6" @if($hasActiveCampaigns) wire:poll.3s @endif>
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-primary-subtle text-primary flex items-center justify-center shrink-0">
                    <x-ph-icon name="broadcast" weight="duotone" class="text-2xl" />
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Broadcast Campaigns</h1>
                        <x-tag color="primary" class="font-bold">{{ $campaigns->total() }} Campaigns</x-tag>
                        @if($hasActiveCampaigns)
                            <x-tag color="emerald" class="font-bold animate-pulse">
                                <x-ph-icon name="lightning" weight="fill" class="text-xs mr-1 inline" />
                                Broadcast In Progress
                            </x-tag>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        High-throughput outreach via Official WhatsApp Meta Cloud API or Paired Web Baileys lines.
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <x-button wire:click="exportLogsCsv" variant="default" size="md" title="Export campaign delivery logs">
                <x-ph-icon name="file-csv" weight="bold" class="text-base mr-1.5 text-gray-500 dark:text-gray-400" />
                <span>Export Logs (CSV)</span>
            </x-button>

            <x-button wire:click="setTab('create')" variant="solid" size="md">
                <x-ph-icon name="plus" weight="bold" class="text-base mr-1.5" />
                <span>Create Campaign</span>
            </x-button>
        </div>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between gap-2 shadow-xs">
            <div class="flex items-center gap-2">
                <x-ph-icon name="check-circle" weight="fill" class="text-lg text-emerald-500" />
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                <x-ph-icon name="x" weight="bold" class="text-xs" />
            </button>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-xs font-semibold border border-blue-200 dark:border-blue-800/50 flex items-center justify-between gap-2 shadow-xs">
            <div class="flex items-center gap-2">
                <x-ph-icon name="info" weight="fill" class="text-lg text-blue-500" />
                <span>{{ session('info') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-blue-600 hover:text-blue-800">
                <x-ph-icon name="x" weight="bold" class="text-xs" />
            </button>
        </div>
    @endif

    <!-- Starter KPI Metric Cards with Phosphor Duotone Icons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card bordered class="shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider block">Total Campaigns</span>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalCampaigns) }}</h3>
                    <span class="text-[11px] text-gray-400 mt-1 block">Scheduled & Executed</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-primary-subtle text-primary flex items-center justify-center shrink-0">
                    <x-ph-icon name="megaphone" weight="duotone" class="text-2xl" />
                </div>
            </div>
        </x-card>

        <x-card bordered class="shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider block">Total Outbound Sent</span>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalSent) }}</h3>
                    <span class="text-[11px] text-gray-400 mt-1 block">Messages Dispatched</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center shrink-0">
                    <x-ph-icon name="paper-plane-tilt" weight="duotone" class="text-2xl" />
                </div>
            </div>
        </x-card>

        <x-card bordered class="shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider block">Delivered Messages</span>
                    <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalDelivered) }}</h3>
                    <span class="text-[11px] text-gray-400 mt-1 block">{{ $deliveryRate }}% Delivery Success Rate</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center shrink-0">
                    <x-ph-icon name="checks" weight="duotone" class="text-2xl" />
                </div>
            </div>
        </x-card>

        <x-card bordered class="shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider block">Active Queue Broadcasts</span>
                    <div class="flex items-center gap-2 mt-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activeBroadcasts }}</h3>
                        @if($activeBroadcasts > 0)
                            <x-tag color="primary" class="animate-pulse text-[10px]">Processing</x-tag>
                        @else
                            <x-tag color="gray" class="text-[10px]">Idle</x-tag>
                        @endif
                    </div>
                    <span class="text-[11px] text-gray-400 mt-1 block">Real-time worker pool</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center shrink-0">
                    <x-ph-icon name="lightning" weight="duotone" class="text-2xl" />
                </div>
            </div>
        </x-card>
    </div>

    <!-- Sub-Navbar Tabs with Phosphor Icons -->
    <x-tabs variant="underline">
        <x-tab-item wire:click="setTab('all')" :active="$activeTab === 'all'">
            <x-ph-icon name="broadcast" weight="bold" class="text-base mr-1.5" />
            <span>All Campaigns ({{ $campaigns->total() }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('create')" :active="$activeTab === 'create'">
            <x-ph-icon name="magic-wand" weight="bold" class="text-base mr-1.5" />
            <span>Campaign Wizard</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('templates')" :active="$activeTab === 'templates'">
            <x-ph-icon name="chats-circle" weight="bold" class="text-base mr-1.5" />
            <span>Message Templates ({{ count($templates) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('logs')" :active="$activeTab === 'logs'">
            <x-ph-icon name="list-checks" weight="bold" class="text-base mr-1.5" />
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
                        <x-ph-icon name="megaphone" weight="duotone" class="text-4xl" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No campaigns created yet</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                        Launch your first WhatsApp broadcast to reach hundreds or thousands of customers instantly.
                    </p>
                    <x-button wire:click="setTab('create')" variant="solid" size="sm" class="mt-2">
                        <x-ph-icon name="plus" weight="bold" class="text-base mr-1" />
                        <span>Create Broadcast</span>
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
                                            <x-tag color="emerald" class="text-[10px] font-bold">
                                                <x-ph-icon name="whatsapp-logo" weight="fill" class="text-xs mr-1 inline" />
                                                QR Web Session
                                            </x-tag>
                                            @if($camp->instance_id)
                                                <span class="text-[10px] text-gray-400 font-mono">({{ $camp->instance_id }})</span>
                                            @endif
                                        </div>
                                    @else
                                        <x-tag color="primary" class="text-[10px] font-bold">
                                            <x-ph-icon name="seal-check" weight="fill" class="text-xs mr-1 inline" />
                                            Meta Cloud API
                                        </x-tag>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-1">
                                        <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                            {{ $camp->template_name }}
                                        </span>
                                        @if($camp->media_type && $camp->media_type !== 'none')
                                            <x-tag color="amber" class="text-[9px] uppercase">
                                                @if($camp->media_type === 'image')
                                                    <x-ph-icon name="image" weight="bold" class="text-[10px] mr-0.5 inline" />
                                                @elseif($camp->media_type === 'video')
                                                    <x-ph-icon name="video-camera" weight="bold" class="text-[10px] mr-0.5 inline" />
                                                @elseif($camp->media_type === 'document')
                                                    <x-ph-icon name="file-text" weight="bold" class="text-[10px] mr-0.5 inline" />
                                                @endif
                                                {{ $camp->media_type }}
                                            </x-tag>
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
                                        <x-progress :percent="$pct" size="sm" color="bg-primary" :showInfo="false" />
                                    </div>
                                </td>
                                <td>
                                    @if($camp->status === 'completed')
                                        <x-tag color="emerald" class="font-bold text-[10px]">
                                            <x-ph-icon name="check-circle" weight="fill" class="text-[11px] mr-1 inline" />
                                            Completed
                                        </x-tag>
                                    @elseif($camp->status === 'processing')
                                        <x-tag color="primary" class="font-bold text-[10px] animate-pulse">
                                            <x-ph-icon name="lightning" weight="fill" class="text-[11px] mr-1 inline" />
                                            Sending...
                                        </x-tag>
                                    @elseif($camp->status === 'paused')
                                        <x-tag color="amber" class="font-bold text-[10px]">
                                            <x-ph-icon name="pause" weight="bold" class="text-[11px] mr-1 inline" />
                                            Paused
                                        </x-tag>
                                    @elseif($camp->status === 'draft')
                                        <x-tag color="gray" class="text-[10px]">Draft</x-tag>
                                    @else
                                        <x-tag color="default" class="text-[10px]">{{ $camp->status }}</x-tag>
                                    @endif
                                </td>
                                <td class="text-xs text-gray-500">
                                    {{ $camp->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- View Logs -->
                                        <x-button wire:click="setFilterCampaign({{ $camp->id }})" variant="default" size="xs" title="View Delivery Logs">
                                            <x-ph-icon name="article" weight="bold" class="text-xs mr-1" />
                                            <span>Logs</span>
                                        </x-button>

                                        <!-- Pause / Resume -->
                                        @if($camp->status === 'processing')
                                            <x-button wire:click="pauseCampaign({{ $camp->id }})" variant="default" size="xs" class="text-amber-500" title="Pause broadcast">
                                                <x-ph-icon name="pause" weight="bold" class="text-xs mr-1" />
                                                <span>Pause</span>
                                            </x-button>
                                        @elseif($camp->status === 'paused')
                                            <x-button wire:click="resumeCampaign({{ $camp->id }})" variant="default" size="xs" class="text-emerald-500" title="Resume broadcast">
                                                <x-ph-icon name="play" weight="fill" class="text-xs mr-1" />
                                                <span>Resume</span>
                                            </x-button>
                                        @endif

                                        <!-- Delete -->
                                        <x-button wire:click="deleteCampaign({{ $camp->id }})" wire:confirm="Delete this campaign and all its delivery logs?" variant="plain" size="xs" class="text-rose-500 hover:text-rose-700" title="Delete">
                                            <x-ph-icon name="trash" weight="bold" class="text-sm" />
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

    <!-- ============================================================== -->
    <!-- TAB 2: 5-STEP CREATE CAMPAIGN WIZARD                           -->
    <!-- ============================================================== -->
    @if($activeTab === 'create')
        <div class="space-y-6">
            <!-- 5-Step Interactive Wizard Header Breadcrumbs using Elstar Steps component -->
            <x-card class="bg-white dark:bg-gray-800 shadow-xs border border-gray-200/80 dark:border-gray-700" bodyClass="p-4 sm:p-5">
                <x-steps>
                    <x-step-item :step="1" :status="$wizardStep > 1 ? 'complete' : ($wizardStep === 1 ? 'in_progress' : 'pending')" title="Channel & Title" wire:click="setWizardStep(1)" class="cursor-pointer" />
                    <x-step-item :step="2" :status="$wizardStep > 2 ? 'complete' : ($wizardStep === 2 ? 'in_progress' : 'pending')" title="Target Audience" wire:click="setWizardStep(2)" class="cursor-pointer" />
                    <x-step-item :step="3" :status="$wizardStep > 3 ? 'complete' : ($wizardStep === 3 ? 'in_progress' : 'pending')" title="Message & Media" wire:click="setWizardStep(3)" class="cursor-pointer" />
                    <x-step-item :step="4" :status="$wizardStep > 4 ? 'complete' : ($wizardStep === 4 ? 'in_progress' : 'pending')" title="Variables Mapping" wire:click="setWizardStep(4)" class="cursor-pointer" />
                    <x-step-item :step="5" :status="$wizardStep === 5 ? 'in_progress' : 'pending'" :isLast="true" title="Pacing & Launch" wire:click="setWizardStep(5)" class="cursor-pointer" />
                </x-steps>
            </x-card>

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
                                                    <x-ph-icon name="seal-check" weight="fill" class="text-sm text-primary" />
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
                                                    <x-ph-icon name="whatsapp-logo" weight="fill" class="text-sm text-emerald-500" />
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
                                        <x-select wire:model="qrDeviceId" prefixIcon="whatsapp-logo" placeholder="Select dispatch line...">
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
                                    <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'all' ? 'border-primary bg-primary-subtle ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer transition-all">
                                        <input type="radio" wire:model.live="targetType" value="all" class="text-primary focus:ring-primary">
                                        <div class="flex items-center gap-2">
                                            <x-ph-icon name="users" weight="duotone" class="text-xl text-primary shrink-0" />
                                            <div>
                                                <div class="text-xs font-bold text-gray-900 dark:text-white">All Contacts</div>
                                                <div class="text-[11px] text-gray-500">Entire workspace</div>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'phonebook' ? 'border-primary bg-primary-subtle ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer transition-all">
                                        <input type="radio" wire:model.live="targetType" value="phonebook" class="text-primary focus:ring-primary">
                                        <div class="flex items-center gap-2">
                                            <x-ph-icon name="address-book" weight="duotone" class="text-xl text-emerald-500 shrink-0" />
                                            <div>
                                                <div class="text-xs font-bold text-gray-900 dark:text-white">Phonebook Group</div>
                                                <div class="text-[11px] text-gray-500">Segmented list</div>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3.5 rounded-xl border {{ $targetType === 'tags' ? 'border-primary bg-primary-subtle ring-1 ring-primary' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50' }} cursor-pointer transition-all">
                                        <input type="radio" wire:model.live="targetType" value="tags" class="text-primary focus:ring-primary">
                                        <div class="flex items-center gap-2">
                                            <x-ph-icon name="tag" weight="duotone" class="text-xl text-amber-500 shrink-0" />
                                            <div>
                                                <div class="text-xs font-bold text-gray-900 dark:text-white">Tag Segment</div>
                                                <div class="text-[11px] text-gray-500">Filtered by tag</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                @if($targetType === 'phonebook')
                                    <x-form-item label="Select Phonebook Group" :required="true" :error="$errors->first('targetId')">
                                        <x-select wire:model="targetId" prefixIcon="address-book" placeholder="Choose a Phonebook...">
                                            @foreach($phonebooks as $pb)
                                                <option value="{{ $pb->id }}">{{ $pb->name }} ({{ $pb->contacts_count }} contacts)</option>
                                            @endforeach
                                        </x-select>
                                    </x-form-item>
                                @elseif($targetType === 'tags')
                                    <x-form-item label="Select Tag Segment" :required="true" :error="$errors->first('targetId')">
                                        <x-select wire:model="targetId" prefixIcon="tag" placeholder="Choose a Tag...">
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
                                        <x-select wire:model="templateName" prefixIcon="chat-circle-dots" placeholder="Choose an approved template...">
                                            @foreach($templates as $tmpl)
                                                <option value="{{ $tmpl['name'] }}">{{ $tmpl['name'] }} ({{ $tmpl['category'] }} - {{ strtoupper($tmpl['language']) }})</option>
                                            @endforeach
                                        </x-select>
                                    </x-form-item>
                                @else
                                    <x-form-item label="Custom Message Body" :required="true" :error="$errors->first('customMessageText')">
                                        <textarea wire:model.live="customMessageText" rows="5" class="input text-xs w-full font-sans" placeholder="Hello @{{name}}, here is your exclusive promo update! Use coupon VIP2026 at checkout."></textarea>
                                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                            <span class="text-[11px] text-gray-400 flex items-center gap-1">
                                                <x-ph-icon name="brackets-curly" weight="bold" class="text-xs" />
                                                <span>Insert tag:</span>
                                            </span>
                                            <button type="button" @click="$wire.set('customMessageText', $wire.customMessageText + ' @{{name}}')" class="px-2 py-0.5 text-[10px] font-mono bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 inline-flex items-center gap-0.5">+ @{{name}}</button>
                                            <button type="button" @click="$wire.set('customMessageText', $wire.customMessageText + ' @{{phone}}')" class="px-2 py-0.5 text-[10px] font-mono bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 inline-flex items-center gap-0.5">+ @{{phone}}</button>
                                            <button type="button" @click="$wire.set('customMessageText', $wire.customMessageText + ' @{{first_name}}')" class="px-2 py-0.5 text-[10px] font-mono bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 inline-flex items-center gap-0.5">+ @{{first_name}}</button>
                                        </div>
                                    </x-form-item>

                                    <!-- Media Attachment Segment Selector with Phosphor Icons -->
                                    <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Media Attachment</label>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                            <label class="p-3 text-center rounded-xl border {{ $mediaType === 'none' ? 'border-primary bg-primary-subtle text-primary font-bold shadow-xs' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }} cursor-pointer text-xs transition-all">
                                                <input type="radio" wire:model.live="mediaType" value="none" class="sr-only">
                                                <x-ph-icon name="chat-centered-text" weight="duotone" class="text-xl mb-1 block mx-auto text-primary" />
                                                <span>Text Only</span>
                                            </label>
                                            <label class="p-3 text-center rounded-xl border {{ $mediaType === 'image' ? 'border-primary bg-primary-subtle text-primary font-bold shadow-xs' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }} cursor-pointer text-xs transition-all">
                                                <input type="radio" wire:model.live="mediaType" value="image" class="sr-only">
                                                <x-ph-icon name="image" weight="duotone" class="text-xl mb-1 block mx-auto text-emerald-500" />
                                                <span>Image</span>
                                            </label>
                                            <label class="p-3 text-center rounded-xl border {{ $mediaType === 'document' ? 'border-primary bg-primary-subtle text-primary font-bold shadow-xs' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }} cursor-pointer text-xs transition-all">
                                                <input type="radio" wire:model.live="mediaType" value="document" class="sr-only">
                                                <x-ph-icon name="file-text" weight="duotone" class="text-xl mb-1 block mx-auto text-blue-500" />
                                                <span>Document</span>
                                            </label>
                                            <label class="p-3 text-center rounded-xl border {{ $mediaType === 'video' ? 'border-primary bg-primary-subtle text-primary font-bold shadow-xs' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }} cursor-pointer text-xs transition-all">
                                                <input type="radio" wire:model.live="mediaType" value="video" class="sr-only">
                                                <x-ph-icon name="video-camera" weight="duotone" class="text-xl mb-1 block mx-auto text-rose-500" />
                                                <span>Video</span>
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
                                    <x-form-item :label="'Parameter ' . '{' . '{1}' . '}'">
                                        <x-select wire:model="templateVariables.1" prefixIcon="brackets-curly">
                                            <option value="name">Contact Full Name</option>
                                            <option value="first_name">Contact First Name</option>
                                            <option value="phone">Phone Number</option>
                                        </x-select>
                                    </x-form-item>

                                    <x-form-item :label="'Parameter ' . '{' . '{2}' . '}'">
                                        <x-select wire:model="templateVariables.2" prefixIcon="brackets-curly">
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
                                    <div class="flex items-start gap-3">
                                        <x-ph-icon name="shield-check" weight="duotone" class="text-2xl text-emerald-500 shrink-0 mt-0.5" />
                                        <div>
                                            <span class="font-bold text-xs text-gray-800 dark:text-gray-200 block">Anti-Ban Zero-Width Suffix</span>
                                            <span class="text-[11px] text-gray-500 dark:text-gray-400 block">Appends invisible Unicode variations to prevent hash duplicate spam triggers.</span>
                                        </div>
                                    </div>
                                    <x-switcher wire:model="randomSuffix" />
                                </div>

                                <x-form-item label="Scheduled Launch Time (Optional)" hint="Leave empty to launch immediately upon creation.">
                                    <x-date-picker type="datetime-local" wire:model="scheduledAt" placeholder="Pick broadcast launch date & time..." />
                                </x-form-item>
                            </div>
                        @endif

                        <!-- Wizard Footer Actions with Phosphor Icons -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div>
                                @if($wizardStep > 1)
                                    <x-button type="button" wire:click="prevStep" variant="default" size="md">
                                        <x-ph-icon name="arrow-left" weight="bold" class="text-sm mr-1.5" />
                                        <span>Back</span>
                                    </x-button>
                                @else
                                    <x-button type="button" wire:click="setTab('all')" variant="plain" size="md">
                                        <span>Cancel</span>
                                    </x-button>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if($wizardStep === 5)
                                    <x-button type="button" wire:click="runTestBroadcast" variant="default" size="md" class="text-emerald-600 font-bold">
                                        <x-ph-icon name="lightning" weight="fill" class="text-sm mr-1.5 text-emerald-600" />
                                        <span>Run 3-Contact Test</span>
                                    </x-button>

                                    <x-button type="button" wire:click="createCampaign" variant="solid" size="md">
                                        <x-ph-icon name="rocket-launch" weight="bold" class="text-base mr-1.5" />
                                        <span>Launch Broadcast Campaign</span>
                                    </x-button>
                                @else
                                    <x-button type="button" wire:click="nextStep" variant="solid" size="md">
                                        <span>Next Step</span>
                                        <x-ph-icon name="arrow-right" weight="bold" class="text-sm ml-1.5" />
                                    </x-button>
                                @endif
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Right Column: Live Message Preview Card -->
                <div class="space-y-6">
                    <x-card bodyClass="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                            <div class="flex items-center gap-1.5">
                                <x-ph-icon name="device-mobile" weight="duotone" class="text-base text-gray-400" />
                                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">WhatsApp Phone Preview</h3>
                            </div>
                            <x-tag color="emerald" class="text-[9px]">Live Preview</x-tag>
                        </div>
                        
                        <!-- Simulated WhatsApp Message Bubble -->
                        <div class="rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 p-4 border border-emerald-100 dark:border-emerald-900/30 space-y-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shadow-xs">
                                    <x-ph-icon name="whatsapp-logo" weight="fill" class="text-sm text-white" />
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-gray-900 dark:text-white block leading-none">Your Business</span>
                                    <span class="text-[10px] text-gray-400">Verified WhatsApp Channel</span>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-white dark:bg-gray-800 shadow-xs border border-gray-100 dark:border-gray-700 text-xs text-gray-800 dark:text-gray-200 leading-relaxed space-y-2">
                                @if($mediaType !== 'none' && !empty($mediaUrl))
                                    <div class="rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 p-2 text-center text-[11px] font-mono text-gray-500 flex items-center justify-center gap-1">
                                        <x-ph-icon name="paperclip" weight="bold" class="text-xs" />
                                        <span>[Attached: {{ strtoupper($mediaType) }}]</span>
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
                                <div class="text-[10px] text-gray-400 text-right mt-1 flex items-center justify-end gap-1">
                                    <span>10:45 AM</span>
                                    <x-ph-icon name="checks" weight="bold" class="text-xs text-sky-500" />
                                </div>
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
                    <x-ph-icon name="arrows-clockwise" weight="bold" class="text-sm mr-1.5" />
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
                                    <x-ph-icon name="seal-check" weight="fill" class="text-xs mr-0.5 inline" />
                                    {{ $tmpl['status'] }}
                                </x-tag>
                            </div>

                            <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-sans">
                                {{ $tmpl['body'] }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <x-ph-icon name="translate" weight="bold" class="text-xs" />
                                <span>Language: <strong class="text-gray-800 dark:text-gray-200">{{ strtoupper($tmpl['language']) }}</strong></span>
                            </span>
                            <x-button wire:click="$set('templateName', '{{ $tmpl['name'] }}'); setTab('create'); setWizardStep(3);" variant="default" size="xs">
                                <x-ph-icon name="arrow-square-out" weight="bold" class="text-xs mr-1" />
                                <span>Use in Broadcast</span>
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
            <div class="p-4 sm:px-6 py-3.5 flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-800">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 flex-wrap">
                    <div class="w-full sm:w-64">
                        <x-input 
                            type="text" 
                            wire:model.live.debounce.300ms="logSearch" 
                            placeholder="Search phone number..." 
                            size="sm"
                            prefixIcon="search"
                        />
                    </div>

                    <div class="w-full sm:w-52">
                        <x-select wire:model.live="filterCampaignId" placeholder="All Campaigns" size="sm">
                            <option value="">All Campaigns</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    <div class="w-full sm:w-44">
                        <x-select wire:model.live="logStatus" size="sm">
                            <option value="all">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="sent">Sent</option>
                            <option value="delivered">Delivered</option>
                            <option value="read">Read</option>
                            <option value="failed">Failed</option>
                        </x-select>
                    </div>
                </div>

                <div class="shrink-0 flex items-center">
                    <x-button wire:click="exportLogsCsv" variant="default" size="sm" class="w-full sm:w-auto">
                        <x-ph-icon name="file-csv" weight="bold" class="text-sm mr-1.5 text-gray-500" />
                        <span>Export Filtered CSV</span>
                    </x-button>
                </div>
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
                                        <x-tag color="emerald" class="text-[10px] font-bold capitalize">
                                            <x-ph-icon name="checks" weight="bold" class="text-[11px] mr-1 inline" />
                                            {{ $log->status }}
                                        </x-tag>
                                    @elseif($log->status === 'sent')
                                        <x-tag color="primary" class="text-[10px] font-bold">
                                            <x-ph-icon name="check" weight="bold" class="text-[11px] mr-1 inline" />
                                            Sent
                                        </x-tag>
                                    @elseif($log->status === 'failed')
                                        <x-tag color="rose" class="text-[10px] font-bold">
                                            <x-ph-icon name="warning-circle" weight="bold" class="text-[11px] mr-1 inline" />
                                            Failed
                                        </x-tag>
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
