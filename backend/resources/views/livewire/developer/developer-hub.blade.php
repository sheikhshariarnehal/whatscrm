<div class="space-y-6">
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Developer, Webhooks & API</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Generate REST API keys, configure outbound event webhooks, and inspect real-time payload logs.
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($activeTab === 'tokens')
                <button wire:click="openTokenModal" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-medium text-sm shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <span>Create API Key</span>
                </button>
            @elseif($activeTab === 'webhooks')
                <button wire:click="openWebhookModal" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-medium text-sm shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Register Webhook</span>
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
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-sm border border-rose-200 dark:border-rose-800/50">
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-sm border border-blue-200 dark:border-blue-800/50">
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800">
        <button wire:click="setTab('tokens')" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'tokens' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            <span>API Keys ({{ count($tokens) }})</span>
        </button>
        <button wire:click="setTab('webhooks')" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'webhooks' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Event Webhooks ({{ count($webhooks) }})</span>
        </button>
        <button wire:click="setTab('logs')" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'logs' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Webhook Logs ({{ $logs->total() }})</span>
        </button>
        <button wire:click="setTab('docs')" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'docs' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            <span>REST API Docs</span>
        </button>
    </div>

    <!-- TAB 1: API KEYS -->
    @if($activeTab === 'tokens')
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
            @if($tokens->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No API Keys Generated</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Create personal access tokens to programmatically send messages, sync contacts, and trigger automations from external systems.
                    </p>
                    <button wire:click="openTokenModal" class="mt-4 px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90">
                        Create API Key
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/40 text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-4">Key Name</th>
                                <th class="px-6 py-4">Permissions</th>
                                <th class="px-6 py-4">Created Date</th>
                                <th class="px-6 py-4">Last Used</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($tokens as $tok)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>{{ $tok->name }}</span>
                                        </div>
                                        <div class="text-xs font-mono text-gray-400 mt-0.5">Token ID: #{{ $tok->id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                            Full Access (All Scopes)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $tok->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $tok->last_used_at ? $tok->last_used_at->diffForHumans() : 'Never' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="revokeToken({{ $tok->id }})" wire:confirm="Are you sure you want to revoke this API token? Any connected external apps will lose access immediately." class="px-3 py-1 rounded-lg text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50">
                                            Revoke Key
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: WEBHOOKS -->
    @if($activeTab === 'webhooks')
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
            @if($webhooks->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No Webhooks Registered</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Connect Shopify, WooCommerce, or your internal CRM to stream live message events in real-time.
                    </p>
                    <button wire:click="openWebhookModal" class="mt-4 px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90">
                        Register Webhook
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/40 text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-4">Webhook Name</th>
                                <th class="px-6 py-4">Destination URL</th>
                                <th class="px-6 py-4">Subscribed Events</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($webhooks as $wh)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        {{ $wh->name }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                        {{ $wh->url }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($wh->events ?? [] as $ev)
                                                <span class="px-2 py-0.5 rounded text-[11px] font-mono bg-primary/10 text-primary font-semibold">
                                                    {{ $ev }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button wire:click="toggleWebhook({{ $wh->id }})" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $wh->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $wh->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                            {{ $wh->is_active ? 'Active' : 'Disabled' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="testWebhook({{ $wh->id }})" title="Send Test Ping" class="px-2.5 py-1 rounded-lg text-xs font-medium text-primary hover:bg-primary/10 border border-primary/20">
                                                Test Ping
                                            </button>
                                            <button wire:click="deleteWebhook({{ $wh->id }})" wire:confirm="Delete this webhook?" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 3: WEBHOOK LOGS -->
    @if($activeTab === 'logs')
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
            @if($logs->isEmpty())
                <div class="p-12 text-center text-gray-500 text-sm">
                    No webhook logs recorded yet. Send a test ping to see real-time payload traffic.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/40 uppercase font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-4">Event</th>
                                <th class="px-6 py-4">Direction</th>
                                <th class="px-6 py-4">HTTP Status</th>
                                <th class="px-6 py-4">Timestamp</th>
                                <th class="px-6 py-4 text-right">Payload</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($logs as $l)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30">
                                    <td class="px-6 py-4 font-mono font-bold text-gray-900 dark:text-white">
                                        {{ $l->event }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold {{ $l->direction === 'inbound' ? 'bg-sky-50 dark:bg-sky-950/40 text-sky-600' : 'bg-purple-50 dark:bg-purple-950/40 text-purple-600' }}">
                                            {{ strtoupper($l->direction) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($l->response_status >= 200 && $l->response_status < 300)
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600">
                                                {{ $l->response_status }} OK
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-mono font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-600">
                                                {{ $l->response_status ?? 'Error' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $l->created_at->format('M d, H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="inspectLog({{ $l->id }})" class="px-2.5 py-1 rounded-lg text-xs font-medium text-primary hover:bg-primary/10 border border-primary/20">
                                            View JSON
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 4: INTERACTIVE REST API DOCS -->
    @if($activeTab === 'docs')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Endpoint 1: Send Text Message -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md bg-emerald-500 text-white font-mono text-xs font-bold">POST</span>
                    <span class="font-mono text-xs text-gray-900 dark:text-white font-bold">/api/v1/send-message</span>
                </div>
                <p class="text-xs text-gray-500">Send an immediate WhatsApp message to any phone number via your connected Meta Cloud number.</p>

                <div class="p-4 rounded-xl bg-gray-900 text-gray-200 font-mono text-xs overflow-x-auto leading-relaxed">
<pre>curl -X POST https://app.whatscrm.com/api/v1/send-message \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "message": "Hello from WhatsCRM API!"
  }'</pre>
                </div>
            </div>

            <!-- Endpoint 2: Sync / Create Contact -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md bg-emerald-500 text-white font-mono text-xs font-bold">POST</span>
                    <span class="font-mono text-xs text-gray-900 dark:text-white font-bold">/api/v1/contacts</span>
                </div>
                <p class="text-xs text-gray-500">Create or update a contact in your workspace CRM directory with custom metadata.</p>

                <div class="p-4 rounded-xl bg-gray-900 text-gray-200 font-mono text-xs overflow-x-auto leading-relaxed">
<pre>curl -X POST https://app.whatscrm.com/api/v1/contacts \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "name": "Alex Mercer",
    "tags": ["Lead", "VIP"],
    "custom_fields": {
      "company": "Mercer Global",
      "budget": "$15,000"
    }
  }'</pre>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: GENERATE TOKEN -->
    @if($showTokenModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Generate REST API Key</h3>
                    <button wire:click="$set('showTokenModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                @if($newlyCreatedToken)
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 space-y-3">
                        <div class="text-xs font-bold text-emerald-800 dark:text-emerald-200">
                            🔑 API Key Generated! Copy it now:
                        </div>
                        <div class="p-3 bg-white dark:bg-gray-900 rounded-lg border border-emerald-200 dark:border-emerald-800 font-mono text-xs break-all text-gray-800 dark:text-gray-200 select-all">
                            {{ $newlyCreatedToken }}
                        </div>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-400">For security reasons, this token will never be displayed again.</p>
                    </div>
                    <div class="flex justify-end pt-3">
                        <button wire:click="$set('showTokenModal', false)" class="px-4 py-2 rounded-lg bg-primary text-white font-medium text-xs">Done</button>
                    </div>
                @else
                    <div class="space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Key Name / Description</label>
                            <input type="text" wire:model="tokenName" placeholder="e.g. WooCommerce Production Sync" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                            @error('tokenName') <span class="text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <button wire:click="$set('showTokenModal', false)" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">Cancel</button>
                        <button wire:click="createToken" class="px-4 py-2 rounded-lg bg-primary text-white font-medium">Generate Key</button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- MODAL: REGISTER WEBHOOK -->
    @if($showWebhookModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Register Webhook Endpoint</h3>
                    <button wire:click="$set('showWebhookModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Webhook Name</label>
                        <input type="text" wire:model="webhookName" placeholder="e.g. Shopify Order Sync" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        @error('webhookName') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Payload Destination URL</label>
                        <input type="url" wire:model="webhookUrl" placeholder="https://api.yourshop.com/webhooks/whatsapp" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        @error('webhookUrl') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">HMAC Secret</label>
                        <input type="text" wire:model="webhookSecret" readonly class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 font-mono text-gray-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-2">Subscribe to Events</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="webhookEvents.message.received" class="rounded text-primary focus:ring-primary">
                                <span>message.received</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="webhookEvents.message.status" class="rounded text-primary focus:ring-primary">
                                <span>message.status</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="webhookEvents.contact.created" class="rounded text-primary focus:ring-primary">
                                <span>contact.created</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="webhookEvents.campaign.completed" class="rounded text-primary focus:ring-primary">
                                <span>campaign.completed</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showWebhookModal', false)" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveWebhook" class="px-4 py-2 rounded-lg bg-primary text-white font-medium">Save Webhook</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: INSPECT LOG PAYLOAD -->
    @if($showPayloadModal && $inspectedPayload)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-2xl w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-900 dark:text-white text-base">Payload Inspector</span>
                        <span class="font-mono text-xs text-primary font-bold">{{ $inspectedPayload['event'] }}</span>
                    </div>
                    <button wire:click="$set('showPayloadModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <div class="font-semibold text-gray-700 dark:text-gray-300 mb-1">Payload Sent:</div>
                        <div class="p-3 bg-gray-900 rounded-xl font-mono text-[11px] text-gray-200 overflow-x-auto max-h-48">
                            <pre>{{ json_encode($inspectedPayload['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-700 dark:text-gray-300 mb-1">Remote Server Response:</div>
                        <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-xl font-mono text-[11px] text-gray-700 dark:text-gray-300 overflow-x-auto max-h-32">
                            <pre>{{ $inspectedPayload['response'] ?: '(No response body)' }}</pre>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showPayloadModal', false)" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-medium text-xs">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
