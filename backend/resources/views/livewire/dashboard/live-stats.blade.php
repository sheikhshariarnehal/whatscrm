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
            <a href="{{ route('inbox') }}" class="px-4 py-2.5 rounded-xl bg-white text-gray-900 font-semibold text-sm hover:bg-blue-50 transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <span>Open Live Inbox</span>
            </a>
            <a href="{{ route('campaigns') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm backdrop-blur-sm border border-white/20 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Campaign</span>
            </a>
        </div>

        <!-- Subtle geometric background accent -->
        <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Contacts -->
        <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Contacts</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-primary flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalContacts) }}</span>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    Synced
                </span>
            </div>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Workspace Phonebook Directory</p>
        </div>

        <!-- Card 2: Active Conversations -->
        <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Open Conversations</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($activeConversations) }}</span>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Real-time</span>
            </div>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Live agent assignments</p>
        </div>

        <!-- Card 3: Outbound Messages Sent -->
        <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Outbound Messages</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalMessagesSent) }}</span>
                <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">Cloud API</span>
            </div>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Campaigns + Direct replies</p>
        </div>

        <!-- Card 4: Inbound Received -->
        <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Inbound Webhooks</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalMessagesReceived) }}</span>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">100% Delivered</span>
            </div>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Processed synchronously</p>
        </div>
    </div>

    <!-- Main Grid: Recent Conversations & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Conversations (2 cols on large screens) -->
        <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></div>
                    <h2 class="font-bold text-base text-gray-900 dark:text-white">Recent WhatsApp Conversations</h2>
                </div>
                <a href="{{ route('inbox') }}" class="text-xs font-semibold text-primary hover:text-primary-deep flex items-center gap-1">
                    <span>View Inbox</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($recentConversations as $conv)
                    <div class="p-4 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-primary/20 to-blue-200 dark:from-primary/30 dark:to-blue-900 flex items-center justify-center text-primary font-bold text-sm shrink-0">
                                {{ substr($conv->sender_name ?? $conv->sender_mobile ?? 'W', 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ $conv->sender_name ?? $conv->sender_mobile }}</span>
                                    @if ($conv->unread_count > 0)
                                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-primary text-white">{{ $conv->unread_count }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $conv->last_message ?? 'No messages yet' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $conv->updated_at->diffForHumans() }}</span>
                            <a href="{{ route('inbox') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-800 hover:bg-primary hover:text-white transition-colors">
                                Reply
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-12 px-4 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto text-gray-400 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No Conversations Yet</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                            Send a campaign or start a test chat from your connected WhatsApp Cloud API number to see messages here.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Shortcuts & Cloud API Setup Card -->
        <div class="space-y-6">
            <!-- Cloud API Connection Widget -->
            <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-base text-gray-900 dark:text-white">Cloud API Status</h2>
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">Verified</span>
                </div>
                <div class="space-y-3">
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Webhook Status</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Listening (Hybrid Sync)
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Real-time Engine</span>
                        <span class="font-semibold text-primary flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            Pusher Connected
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Queue Worker</span>
                        <span class="font-semibold text-purple-600 dark:text-purple-400">Database (Cron Active)</span>
                    </div>
                </div>

                <a href="{{ route('devices') }}" class="mt-4 w-full py-2 px-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-300 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    <span>Manage Meta Credentials</span>
                </a>
            </div>

            <!-- Quick CRM Shortcuts -->
            <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-2.5">
                <h2 class="font-bold text-base text-gray-900 dark:text-white mb-3">Quick Navigation</h2>
                
                <a href="{{ route('crm') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                        </div>
                        <span class="text-xs font-semibold">CRM Kanban Pipeline</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('contacts') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span class="text-xs font-semibold">Phonebook & Contacts</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('automations') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-primary/10 hover:text-primary transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-xs font-semibold">Chatbot Flow Builder</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>
