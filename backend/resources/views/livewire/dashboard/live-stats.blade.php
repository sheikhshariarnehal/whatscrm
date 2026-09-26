<div class="space-y-6">
    <!-- Top Welcome & WhatsApp Cloud API Banner -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-primary text-white shadow-lg shadow-primary/10 relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1 z-10">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-md">Cloud API v20.0</span>
                <span class="text-xs text-blue-100 font-medium">Official Meta Integration</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">WhatsApp Business Command Center</h1>
            <p class="text-sm text-blue-100 max-w-xl">
                Manage conversations, execute targeted broadcast campaigns, and trigger automated sales workflows across your workspace.
            </p>
        </div>

        <div class="flex items-center gap-3 z-10 shrink-0">
            <x-button variant="default" size="md" as="a" href="{{ route('inbox') }}" class="bg-white text-gray-900 border-none hover:bg-blue-50 shadow-md font-bold">
                <x-ph-icon name="chat-circle-dots" weight="duotone" class="text-lg mr-1.5 text-primary" />
                <span>Open Live Inbox</span>
            </x-button>
            <x-button variant="solid" size="md" as="a" href="{{ route('campaigns') }}" class="bg-white/15 hover:bg-white/25 text-white border border-white/20 font-bold backdrop-blur-sm">
                <x-ph-icon name="plus" weight="bold" class="text-base mr-1.5" />
                <span>New Campaign</span>
            </x-button>
        </div>

        <!-- Subtle geometric background accent -->
        <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Contacts -->
        <x-card bodyClass="p-5" class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Contacts</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-primary flex items-center justify-center">
                    <x-nav-icon name="contacts" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalContacts) }}</span>
                <x-tag color="emerald" prefix>Synced</x-tag>
            </div>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Workspace Phonebook Directory</p>
        </x-card>

        <!-- Card 2: Active Conversations -->
        <x-card bodyClass="p-5" class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Open Conversations</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <x-nav-icon name="inbox" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($activeConversations) }}</span>
                <x-tag color="emerald" prefix>Real-time</x-tag>
            </div>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Live agent assignments</p>
        </x-card>

        <!-- Card 3: Outbound Messages Sent -->
        <x-card bodyClass="p-5" class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Outbound Messages</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <x-nav-icon name="campaigns" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalMessagesSent) }}</span>
                <x-tag color="purple" prefix>Cloud API</x-tag>
            </div>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Campaigns + Direct replies</p>
        </x-card>

        <!-- Card 4: Inbound Received -->
        <x-card bodyClass="p-5" class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Inbound Webhooks</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <x-nav-icon name="automations" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalMessagesReceived) }}</span>
                <x-tag color="emerald" prefix>100% Delivered</x-tag>
            </div>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Processed synchronously</p>
        </x-card>
    </div>

    <!-- Main Grid: Recent Conversations & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Conversations (2 cols on large screens) -->
        <x-card class="lg:col-span-2 overflow-hidden" gutterless>
            <x-slot name="headerSlot">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></div>
                        <h2 class="font-bold text-base text-gray-900 dark:text-white">Recent WhatsApp Conversations</h2>
                    </div>
                    <a href="{{ route('inbox') }}" class="text-xs font-semibold text-primary hover:text-primary-deep flex items-center gap-1">
                        <span>View Inbox</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </x-slot>

            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($recentConversations as $conv)
                    <div class="p-4 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <x-avatar :name="$conv->sender_name ?? $conv->sender_mobile" size="md" status="online" />
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ $conv->sender_name ?? $conv->sender_mobile }}</span>
                                    @if ($conv->unread_count > 0)
                                        <x-badge :content="$conv->unread_count" color="primary" />
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $conv->last_message ?? 'No messages yet' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $conv->updated_at->diffForHumans() }}</span>
                            <x-button variant="default" size="xs" as="a" href="{{ route('inbox') }}">
                                Reply
                            </x-button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 px-4 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto text-gray-400 mb-3">
                            <x-nav-icon name="inbox" class="w-6 h-6" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No Conversations Yet</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                            Send a campaign or start a test chat from your connected WhatsApp Cloud API number to see messages here.
                        </p>
                    </div>
                @endforelse
            </div>
        </x-card>

        <!-- Right Column: Shortcuts & Cloud API Setup Card -->
        <div class="space-y-6">
            <!-- Cloud API Connection Widget -->
            <x-card bodyClass="p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-base text-gray-900 dark:text-white">Cloud API Status</h2>
                    <x-tag color="emerald">Verified</x-tag>
                </div>
                <div class="space-y-3">
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Webhook Status</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Listening (Hybrid Sync)
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Real-time Engine</span>
                        <span class="font-semibold text-primary flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            Pusher Connected
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Queue Worker</span>
                        <span class="font-semibold text-purple-600 dark:text-purple-400">Database (Cron Active)</span>
                    </div>
                </div>

                <x-button variant="default" size="sm" block as="a" href="{{ route('devices') }}" class="mt-4" icon="devices">
                    Manage Meta Credentials
                </x-button>
            </x-card>

            <!-- Quick CRM Shortcuts -->
            <x-card bodyClass="p-5" class="space-y-2.5">
                <h2 class="font-bold text-base text-gray-900 dark:text-white mb-3">Quick Navigation</h2>
                
                <a href="{{ route('crm') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                            <x-nav-icon name="crm" class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-semibold">CRM Kanban Pipeline</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('contacts') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                            <x-nav-icon name="contacts" class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-semibold">Phonebook & Contacts</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('automations') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                            <x-nav-icon name="automations" class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-semibold">Chatbot Flow Builder</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </x-card>
        </div>
    </div>
</div>
