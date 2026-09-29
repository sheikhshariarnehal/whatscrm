<div class="space-y-6">
    <!-- Top Header (Simple Title Only) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-950 dark:text-white tracking-tight">Dashboard</h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Segmented Time Range Switcher -->
            <div class="inline-flex p-1 rounded-xl bg-gray-100/90 dark:bg-gray-800/90 border border-gray-200/70 dark:border-gray-700/60 text-xs font-semibold">
                <button wire:click="setTimeRange('today')" 
                        type="button"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $timeRange === 'today' ? 'bg-white dark:bg-gray-700 text-gray-950 dark:text-white shadow-xs font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    Today
                </button>
                <button wire:click="setTimeRange('7_days')" 
                        type="button"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $timeRange === '7_days' ? 'bg-white dark:bg-gray-700 text-gray-950 dark:text-white shadow-xs font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    7 Days
                </button>
                <button wire:click="setTimeRange('30_days')" 
                        type="button"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $timeRange === '30_days' ? 'bg-white dark:bg-gray-700 text-gray-950 dark:text-white shadow-xs font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    30 Days
                </button>
                <button wire:click="setTimeRange('all_time')" 
                        type="button"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $timeRange === 'all_time' ? 'bg-white dark:bg-gray-700 text-gray-950 dark:text-white shadow-xs font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    All Time
                </button>
            </div>

            <x-button variant="default" size="md" as="a" href="{{ route('inbox') }}">
                <x-ph-icon name="chat-circle-dots" weight="duotone" class="text-base mr-1.5 text-primary" />
                <span>Live Inbox</span>
            </x-button>

            <x-button variant="solid" size="md" as="a" href="{{ route('campaigns') }}">
                <x-ph-icon name="plus" weight="bold" class="text-base mr-1.5" />
                <span>New Campaign</span>
            </x-button>
        </div>
    </div>

    <!-- 4 Primary KPI Stat Cards (Quieter & Refined Tags) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Contacts Directory -->
        <x-card bodyClass="p-5" class="hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Contacts</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                    <x-nav-icon name="contacts" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2.5">
                <span class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($totalContacts) }}</span>
                @if($timeRange !== 'all_time' && $newContactsInRange > 0 && $newContactsInRange < $totalContacts)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400">
                        +{{ number_format($newContactsInRange) }} new
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                        Synced
                    </span>
                @endif
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                <span>{{ $totalPhonebooks }} Phonebook groups</span>
                <a href="{{ route('contacts') }}" class="text-primary hover:underline font-semibold">View &rarr;</a>
            </div>
        </x-card>

        <!-- Card 2: Open Conversations & Inbox Queue -->
        <x-card bodyClass="p-5" class="hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Live Inbox Queue</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <x-nav-icon name="inbox" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2.5">
                <span class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($activeConversations) }}</span>
                @if($unassignedConversations > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400">
                        {{ $unassignedConversations }} unassigned
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400">
                        All assigned
                    </span>
                @endif
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                <span>{{ $totalUnreadMessages }} Unread messages</span>
                <a href="{{ route('inbox') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold">Chat &rarr;</a>
            </div>
        </x-card>

        <!-- Card 3: Outbound & Inbound Messaging Volume -->
        <x-card bodyClass="p-5" class="hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Messages Volume</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <x-nav-icon name="campaigns" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2.5">
                <span class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($totalMessagesSent + $totalMessagesReceived) }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300">
                    {{ $deliveryRate }}% delivered
                </span>
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                <span>{{ number_format($totalMessagesSent) }} Sent &middot; {{ number_format($totalMessagesReceived) }} Recv</span>
                <span class="text-purple-600 dark:text-purple-400 font-semibold">{{ $readRate }}% Read</span>
            </div>
        </x-card>

        <!-- Card 4: CRM Pipeline Value -->
        <x-card bodyClass="p-5" class="hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">CRM Pipeline Value</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <x-nav-icon name="crm" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2.5">
                <span class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">${{ number_format($totalPipelineValue, 0) }}</span>
                @if($wonDealsCount > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400">
                        {{ $wonDealsCount }} won
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                        {{ $totalDealsCount }} in pipeline
                    </span>
                @endif
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                <span>{{ $totalDealsCount }} Active deal leads</span>
                <a href="{{ route('crm') }}" class="text-amber-600 dark:text-amber-400 hover:underline font-semibold">Kanban &rarr;</a>
            </div>
        </x-card>
    </div>

    <!-- Row 1: Top Asymmetric Split (Messaging Activity & Cloud API Status with Exact Equal Height) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
        
        <!-- Widget 1: Polished Messaging Activity & Traffic Trend Chart (Left 2 Columns) -->
        <div class="lg:col-span-2 flex flex-col">
            <x-card class="h-full flex flex-col overflow-hidden" bodyClass="p-5 sm:p-6 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                        <div>
                            <h2 class="font-bold text-base text-gray-900 dark:text-white">Messaging Activity</h2>
                        </div>

                        <!-- Refined Chart Legends -->
                        <div class="flex items-center gap-4 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#2a85ff]"></span>
                                <span class="text-gray-500 dark:text-gray-400 font-medium">Outbound</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#7cbc7d]"></span>
                                <span class="text-gray-500 dark:text-gray-400 font-medium">Inbound</span>
                            </div>
                        </div>
                    </div>

                    <!-- Elstar Template ApexCharts Bar Component -->
                    <div x-data="{
                        chart: null,
                        categories: {{ json_encode($chartDays) }},
                        outbound: {{ json_encode($chartOutbound) }},
                        inbound: {{ json_encode($chartInbound) }},
                        renderChart() {
                            if (typeof ApexCharts === 'undefined') {
                                setTimeout(() => this.renderChart(), 50);
                                return;
                            }
                            if (this.chart) {
                                this.chart.destroy();
                            }
                            const isDark = document.documentElement.classList.contains('dark');
                            const options = {
                                chart: {
                                    type: 'bar',
                                    height: 195,
                                    zoom: { enabled: false },
                                    toolbar: { show: false },
                                    parentHeightOffset: 0,
                                    background: 'transparent',
                                    fontFamily: 'Inter, sans-serif',
                                    animations: {
                                        enabled: true,
                                        easing: 'easeinout',
                                        speed: 400,
                                    }
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '28px',
                                        borderRadius: 4,
                                        borderRadiusApplication: 'end',
                                    },
                                },
                                colors: ['#2a85ff', '#7cbc7d'],
                                dataLabels: { enabled: false },
                                stroke: {
                                    show: true,
                                    width: 2,
                                    colors: ['transparent'],
                                },
                                series: [
                                    { name: 'Outbound', data: this.outbound },
                                    { name: 'Inbound', data: this.inbound }
                                ],
                                xaxis: {
                                    categories: this.categories,
                                    axisBorder: { show: false },
                                    axisTicks: { show: false },
                                    labels: {
                                        style: {
                                            colors: isDark ? '#737373' : '#a3a3a3',
                                            fontSize: '11px',
                                            fontWeight: 500,
                                        }
                                    }
                                },
                                yaxis: {
                                    show: false,
                                },
                                grid: {
                                    show: true,
                                    borderColor: isDark ? '#262626' : '#f5f5f5',
                                    strokeDashArray: 0,
                                    yaxis: { lines: { show: true } },
                                    xaxis: { lines: { show: false } },
                                    padding: { top: 0, right: 0, bottom: 0, left: 0 },
                                },
                                legend: {
                                    show: false
                                },
                                tooltip: {
                                    theme: isDark ? 'dark' : 'light',
                                    y: {
                                        formatter: function(val) {
                                            return val + ' messages';
                                        }
                                    }
                                }
                            };
                            this.chart = new ApexCharts(this.$refs.chartContainer, options);
                            this.chart.render();
                        }
                    }"
                    x-init="renderChart(); $watch('categories', () => renderChart()); $watch('outbound', () => renderChart());"
                    class="relative w-full">
                        <div x-ref="chartContainer" class="w-full"></div>
                    </div>
                </div>

                <!-- Bottom Metric Highlights Strip (Unboxed & Flush at bottom) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 text-left">
                    <div>
                        <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Outbound Sent</span>
                        <div class="text-xl font-bold text-gray-900 dark:text-white mt-1 tracking-tight">{{ number_format($totalMessagesSent) }}</div>
                    </div>
                    <div>
                        <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Inbound Received</span>
                        <div class="text-xl font-bold text-gray-900 dark:text-white mt-1 tracking-tight">{{ number_format($totalMessagesReceived) }}</div>
                    </div>
                    <div>
                        <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Delivery Rate</span>
                        <div class="text-xl font-bold text-gray-900 dark:text-white mt-1 tracking-tight">{{ $deliveryRate }}%</div>
                    </div>
                    <div>
                        <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Read Rate</span>
                        <div class="text-xl font-bold text-gray-900 dark:text-white mt-1 tracking-tight">{{ $readRate }}%</div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Widget 3: Cloud API & Channel Status (Right 1 Column - Matched Height) -->
        <div class="lg:col-span-1 flex flex-col">
            <x-card class="h-full flex flex-col" bodyClass="p-5 sm:p-6 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3.5">
                        <h2 class="font-bold text-base text-gray-900 dark:text-white">Cloud API Status</h2>
                        @if($isMetaConnected)
                            <x-tag color="emerald">Verified</x-tag>
                        @else
                            <x-tag color="amber">Setup Required</x-tag>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <!-- Meta Cloud API -->
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Meta Cloud API</span>
                            <span class="font-semibold {{ $isMetaConnected ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-500' }} flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isMetaConnected ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ $isMetaConnected ? 'v20.0 Connected' : 'Ready to Connect' }}
                            </span>
                        </div>

                        <!-- Baileys QR Web Instances -->
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">QR Web Instances</span>
                            <span class="font-semibold text-primary flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                {{ $activeQrInstances }} / {{ max(1, $totalQrInstances) }} Active
                            </span>
                        </div>

                        <!-- Webhook Sync -->
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Webhook Listener</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Listening (Hybrid Sync)
                            </span>
                        </div>

                        <!-- Realtime Engine -->
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Real-time Engine</span>
                            <span class="font-semibold text-primary flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                Pusher / Echo Active
                            </span>
                        </div>

                        <!-- Queue Worker -->
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Queue Worker</span>
                            <span class="font-semibold text-purple-600 dark:text-purple-400">Database (Cron Active)</span>
                        </div>
                    </div>
                </div>

                <x-button variant="default" size="sm" block as="a" href="{{ route('devices') }}" class="mt-4" icon="devices">
                    Manage Meta Credentials
                </x-button>
            </x-card>
        </div>
    </div>

    <!-- Row 2: Bottom Asymmetric Grid (Recent Conversations Left, Quick Nav & Campaigns Right - Exact Equal Height) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
        
        <!-- Left 2 Columns: Recent WhatsApp Conversations -->
        <div class="lg:col-span-2 flex flex-col">
            <!-- Widget 2: Recent WhatsApp Conversations (Preserved & Enhanced Section) -->
            <x-card class="h-full flex flex-col overflow-hidden" bodyClass="flex-1 flex flex-col justify-between p-0" gutterless>
                <x-slot name="headerSlot">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2">
                            <h2 class="font-bold text-base text-gray-900 dark:text-white">Recent WhatsApp Conversations</h2>
                        </div>
                        <a href="{{ route('inbox') }}" class="text-xs font-semibold text-primary hover:text-primary-deep flex items-center gap-1">
                            <span>View All in Live Inbox</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </x-slot>

                <div class="divide-y divide-gray-100 dark:divide-gray-800 flex-1 flex flex-col justify-between">
                    @forelse ($recentConversations as $conv)
                        <div class="p-3.5 sm:p-4 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors flex items-center justify-between gap-4 flex-1">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <x-avatar :name="$conv->sender_name ?? $conv->sender_mobile" size="md" status="online" />
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ $conv->sender_name ?? $conv->sender_mobile }}</span>
                                        
                                        <!-- Channel Badge -->
                                        @if($conv->channel === 'whatsapp_cloud')
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">Cloud API</span>
                                        @else
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">Baileys QR</span>
                                        @endif

                                        @if ($conv->unread_count > 0)
                                            <x-badge :content="$conv->unread_count" color="primary" />
                                        @endif

                                        <!-- Tags if attached -->
                                        @foreach($conv->tags->take(2) as $tag)
                                            <span class="px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                                #{{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5 max-w-md">
                                        {{ $conv->last_message ?? 'No messages yet' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs text-gray-400 dark:text-gray-500 hidden sm:inline-block">
                                    {{ $conv->updated_at ? $conv->updated_at->diffForHumans() : 'Just now' }}
                                </span>
                                <x-button variant="default" size="xs" as="a" href="{{ route('inbox') }}">
                                    Reply
                                </x-button>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 px-4 text-center flex-1 flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto text-gray-400 mb-3">
                                <x-nav-icon name="inbox" class="w-6 h-6" />
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No Conversations Yet</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                                Send a campaign or start a test chat from your connected WhatsApp Cloud API number or QR instance.
                            </p>
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Right 1 Column: Quick Navigation & Broadcast Campaigns Spotlight -->
        <div class="lg:col-span-1 flex flex-col justify-between gap-6">
            
            <!-- Widget 4: Preserved Quick Navigation Shortcuts -->
            <x-card bodyClass="p-5 flex-1 flex flex-col justify-between" class="flex flex-col">
                <div>
                    <h2 class="font-bold text-base text-gray-900 dark:text-white mb-3">Quick Navigation</h2>
                    
                    <div class="space-y-2">
                        <a href="{{ route('crm') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-white dark:bg-gray-800 shadow-xs flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                                    <x-nav-icon name="crm" class="w-4 h-4" />
                                </div>
                                <span class="text-xs font-semibold">CRM Kanban Pipeline</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <a href="{{ route('contacts') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-white dark:bg-gray-800 shadow-xs flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                                    <x-nav-icon name="contacts" class="w-4 h-4" />
                                </div>
                                <span class="text-xs font-semibold">Phonebook & Contacts</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <a href="{{ route('automations') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-white dark:bg-gray-800 shadow-xs flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                                    <x-nav-icon name="automations" class="w-4 h-4" />
                                </div>
                                <span class="text-xs font-semibold">Chatbot Flow Builder</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <a href="{{ route('campaigns') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-white dark:bg-gray-800 shadow-xs flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                                    <x-nav-icon name="campaigns" class="w-4 h-4" />
                                </div>
                                <span class="text-xs font-semibold">Broadcast Campaigns</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </x-card>

            <!-- Widget 5: Broadcast Campaigns & CRM Pipeline (Polished Spacing) -->
            <x-card bodyClass="p-5 flex-1 flex flex-col justify-between" class="flex flex-col">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-bold text-base text-gray-900 dark:text-white">Active Campaigns</h2>
                        <a href="{{ route('campaigns') }}" class="text-xs text-primary hover:underline font-semibold">View all &rarr;</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentCampaigns as $camp)
                            @php
                                $progress = $camp->total_recipients > 0 ? round(($camp->sent_count / $camp->total_recipients) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $camp->name }}</span>
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                                        {{ number_format($camp->sent_count) }}/{{ number_format($camp->total_recipients) }} sent &middot; 
                                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $progress }}%</span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-800 h-1.5 rounded-full overflow-hidden mt-2">
                                    <div class="bg-primary h-full rounded-full transition-all duration-300" style="width: {{ $progress }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="py-3 text-center text-xs text-gray-400 dark:text-gray-500">
                                <span>No active broadcast campaigns.</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quiet CRM Pipeline Stages Row -->
                <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between text-xs mb-3">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">CRM Deal Pipeline</span>
                        <span class="font-bold text-gray-900 dark:text-white">${{ number_format($totalPipelineValue, 0) }}</span>
                    </div>

                    <div class="flex flex-row items-center justify-between divide-x divide-gray-200/80 dark:divide-gray-700/60 p-2.5 rounded-xl bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 text-center" style="display: flex; flex-direction: row;">
                        <div class="flex-1 min-w-0 px-1 text-center" style="flex: 1 1 0%;">
                            <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $pipelineStages['new_lead'] ?? 0 }}</div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500 truncate mt-0.5">Leads</div>
                        </div>
                        <div class="flex-1 min-w-0 px-1 text-center" style="flex: 1 1 0%;">
                            <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $pipelineStages['contacted'] ?? 0 }}</div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500 truncate mt-0.5">Chat</div>
                        </div>
                        <div class="flex-1 min-w-0 px-1 text-center" style="flex: 1 1 0%;">
                            <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $pipelineStages['qualified'] ?? 0 }}</div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500 truncate mt-0.5">Qual</div>
                        </div>
                        <div class="flex-1 min-w-0 px-1 text-center" style="flex: 1 1 0%;">
                            <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $pipelineStages['proposal'] ?? 0 }}</div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500 truncate mt-0.5">Prop</div>
                        </div>
                        <div class="flex-1 min-w-0 px-1 text-center" style="flex: 1 1 0%;">
                            <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $pipelineStages['won'] ?? 0 }}</div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500 truncate mt-0.5">Won</div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>

