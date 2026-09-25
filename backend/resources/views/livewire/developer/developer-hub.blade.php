<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Developer, Webhooks & API</h1>
                <x-tag color="primary" class="font-bold">v1 REST API</x-tag>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Generate REST API keys, configure outbound event webhooks, and inspect real-time payload logs.
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($activeTab === 'tokens')
                <x-button wire:click="openTokenModal" variant="solid" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <span>Create API Key</span>
                </x-button>
            @elseif($activeTab === 'webhooks')
                <x-button wire:click="openWebhookModal" variant="solid" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Register Webhook</span>
                </x-button>
            @endif
        </div>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/50">
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-xs font-semibold border border-blue-200 dark:border-blue-800/50">
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <x-tabs>
        <x-tab-item wire:click="setTab('tokens')" :active="$activeTab === 'tokens'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            <span>API Keys ({{ count($tokens) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('webhooks')" :active="$activeTab === 'webhooks'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Event Webhooks ({{ count($webhooks) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('logs')" :active="$activeTab === 'logs'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Webhook Logs ({{ $logs->total() }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('docs')" :active="$activeTab === 'docs'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            <span>REST API Docs</span>
        </x-tab-item>
    </x-tabs>

    <!-- TAB 1: API KEYS -->
    @if($activeTab === 'tokens')
        <x-card gutterless class="overflow-hidden">
            @if($tokens->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary-subtle text-primary flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No API Keys Generated</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Create personal access tokens to programmatically send messages, sync contacts, and trigger automations from external systems.
                    </p>
                    <x-button wire:click="openTokenModal" variant="solid" size="sm" class="mt-4">
                        Create API Key
                    </x-button>
                </div>
            @else
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Key Name</th>
                            <th>Permissions</th>
                            <th>Created Date</th>
                            <th>Last Used</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tokens as $tok)
                            <tr>
                                <td>
                                    <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span>{{ $tok->name }}</span>
                                    </div>
                                    <div class="text-[11px] font-mono text-gray-400 mt-0.5">Token ID: #{{ $tok->id }}</div>
                                </td>
                                <td>
                                    <x-tag color="gray">
                                        Full Access (All Scopes)
                                    </x-tag>
                                </td>
                                <td class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $tok->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $tok->last_used_at ? $tok->last_used_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="text-right">
                                    <x-button wire:click="revokeToken({{ $tok->id }})" wire:confirm="Are you sure you want to revoke this API token? Any connected external apps will lose access immediately." variant="plain" size="xs" class="text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                        Revoke Key
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            @endif
        </x-card>
    @endif

    <!-- TAB 2: WEBHOOKS -->
    @if($activeTab === 'webhooks')
        <x-card gutterless class="overflow-hidden">
            @if($webhooks->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary-subtle text-primary flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No Webhooks Registered</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Connect Shopify, WooCommerce, or your internal CRM to stream live message events in real-time.
                    </p>
                    <x-button wire:click="openWebhookModal" variant="solid" size="sm" class="mt-4">
                        Register Webhook
                    </x-button>
                </div>
            @else
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Webhook Name</th>
                            <th>Destination URL</th>
                            <th>Subscribed Events</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($webhooks as $wh)
                            <tr>
                                <td class="font-bold text-gray-900 dark:text-white">
                                    {{ $wh->name }}
                                </td>
                                <td class="font-mono text-xs text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                    {{ $wh->url }}
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($wh->events ?? [] as $ev)
                                            <x-tag color="primary">
                                                {{ $ev }}
                                            </x-tag>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <button wire:click="toggleWebhook({{ $wh->id }})" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ $wh->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $wh->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $wh->is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-button wire:click="testWebhook({{ $wh->id }})" variant="default" size="xs">
                                            Test Ping
                                        </x-button>
                                        <button wire:click="deleteWebhook({{ $wh->id }})" wire:confirm="Delete this webhook?" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors" title="Delete Webhook">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            @endif
        </x-card>
    @endif

    <!-- TAB 3: WEBHOOK LOGS -->
    @if($activeTab === 'logs')
        <x-card gutterless class="overflow-hidden">
            @if($logs->isEmpty())
                <div class="p-12 text-center text-gray-500 dark:text-gray-400 text-sm">
                    No webhook logs recorded yet. Send a test ping to see real-time payload traffic.
                </div>
            @else
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Direction</th>
                            <th>HTTP Status</th>
                            <th>Timestamp</th>
                            <th class="text-right">Payload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($logs as $l)
                            <tr>
                                <td class="font-mono font-bold text-gray-900 dark:text-white">
                                    {{ $l->event }}
                                </td>
                                <td>
                                    <x-tag color="{{ $l->direction === 'inbound' ? 'primary' : 'purple' }}">
                                        {{ strtoupper($l->direction) }}
                                    </x-tag>
                                </td>
                                <td>
                                    @if($l->response_status >= 200 && $l->response_status < 300)
                                        <x-tag color="emerald" class="font-mono">
                                             {{ $l->response_status }} OK
                                        </x-tag>
                                    @else
                                        <x-tag color="rose" class="font-mono">
                                            {{ $l->response_status ?? 'Error' }}
                                        </x-tag>
                                    @endif
                                </td>
                                <td class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $l->created_at->format('M d, H:i:s') }}
                                </td>
                                <td class="text-right">
                                    <x-button wire:click="inspectLog({{ $l->id }})" variant="default" size="xs">
                                        View JSON
                                    </x-button>
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

    <!-- TAB 4: INTERACTIVE REST API DOCS -->
    @if($activeTab === 'docs')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Endpoint 1: Send Text Message -->
            <x-card bodyClass="p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <x-tag color="emerald" class="font-mono font-bold">POST</x-tag>
                    <span class="font-mono text-xs text-gray-900 dark:text-white font-bold">/api/v1/send-message</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Send an immediate WhatsApp message to any phone number via your connected Meta Cloud number.</p>

                <div class="p-4 rounded-xl bg-gray-900 text-gray-200 font-mono text-xs overflow-x-auto leading-relaxed">
<pre>curl -X POST https://app.whatscrm.com/api/v1/send-message \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "message": "Hello from WhatsCRM API!"
  }'</pre>
                </div>
            </x-card>

            <!-- Endpoint 2: Sync / Create Contact -->
            <x-card bodyClass="p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <x-tag color="emerald" class="font-mono font-bold">POST</x-tag>
                    <span class="font-mono text-xs text-gray-900 dark:text-white font-bold">/api/v1/contacts</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Create or update a contact in your workspace CRM directory with custom metadata.</p>

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
            </x-card>
        </div>
    @endif

    <!-- MODAL: GENERATE TOKEN -->
    @if($showTokenModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Generate REST API Key</h3>
                    <button wire:click="$set('showTokenModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                @if($newlyCreatedToken)
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 space-y-3">
                        <div class="text-xs font-bold text-emerald-800 dark:text-emerald-200">
                            🔑 API Key Generated! Copy it now:
                        </div>
                        <div class="p-3 bg-white dark:bg-gray-900 rounded-xl border border-emerald-200 dark:border-emerald-800 font-mono text-xs break-all text-gray-800 dark:text-gray-200 select-all">
                            {{ $newlyCreatedToken }}
                        </div>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-400">For security reasons, this token will never be displayed again.</p>
                    </div>
                    <div class="flex justify-end pt-3">
                        <x-button wire:click="$set('showTokenModal', false)" variant="solid" size="sm">Done</x-button>
                    </div>
                @else
                    <div class="space-y-4 pt-1">
                        <x-form-item label="Key Name / Description" :required="true" :error="$errors->first('tokenName')">
                            <x-input wire:model="tokenName" placeholder="e.g. WooCommerce Production Sync" prefix-icon="broadcast" :invalid="$errors->has('tokenName')" />
                        </x-form-item>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <x-button wire:click="$set('showTokenModal', false)" variant="default" size="sm">Cancel</x-button>
                        <x-button wire:click="createToken" variant="solid" size="sm">Generate Key</x-button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- MODAL: REGISTER WEBHOOK -->
    @if($showWebhookModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Register Webhook Endpoint</h3>
                    <button wire:click="$set('showWebhookModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4 pt-1">
                    <x-form-item label="Webhook Name" :required="true" :error="$errors->first('webhookName')">
                        <x-input wire:model="webhookName" placeholder="e.g. Shopify Order Sync" :invalid="$errors->has('webhookName')" />
                    </x-form-item>

                    <x-form-item label="Payload Destination URL" :required="true" :error="$errors->first('webhookUrl')">
                        <x-input type="url" wire:model="webhookUrl" placeholder="https://api.yourshop.com/webhooks/whatsapp" class="font-mono text-xs" :invalid="$errors->has('webhookUrl')" />
                    </x-form-item>

                    <x-form-item label="HMAC Secret">
                        <x-input type="text" wire:model="webhookSecret" readonly class="font-mono text-xs text-gray-500 bg-gray-100 dark:bg-gray-800" />
                    </x-form-item>

                    <x-form-item label="Subscribe to Events">
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <x-checkbox wire:model="webhookEvents.message.received" label="message.received" />
                            <x-checkbox wire:model="webhookEvents.message.status" label="message.status" />
                            <x-checkbox wire:model="webhookEvents.contact.created" label="contact.created" />
                            <x-checkbox wire:model="webhookEvents.campaign.completed" label="campaign.completed" />
                        </div>
                    </x-form-item>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button wire:click="$set('showWebhookModal', false)" variant="default" size="sm">Cancel</x-button>
                    <x-button wire:click="saveWebhook" variant="solid" size="sm">Save Webhook</x-button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: INSPECT LOG PAYLOAD -->
    @if($showPayloadModal && $inspectedPayload)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-2xl w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-900 dark:text-white text-base">Payload Inspector</span>
                        <x-tag color="primary" class="font-mono font-bold">{{ $inspectedPayload['event'] }}</x-tag>
                    </div>
                    <button wire:click="$set('showPayloadModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <div class="font-bold text-gray-700 dark:text-gray-300 mb-1">Payload Sent:</div>
                        <div class="p-3 bg-gray-900 rounded-xl font-mono text-[11px] text-gray-200 overflow-x-auto max-h-48">
                            <pre>{{ json_encode($inspectedPayload['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-gray-700 dark:text-gray-300 mb-1">Remote Server Response:</div>
                        <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-xl font-mono text-[11px] text-gray-700 dark:text-gray-300 overflow-x-auto max-h-32">
                            <pre>{{ $inspectedPayload['response'] ?: '(No response body)' }}</pre>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button wire:click="$set('showPayloadModal', false)" variant="default" size="sm">Close</x-button>
                </div>
            </div>
        </div>
    @endif
</div>
