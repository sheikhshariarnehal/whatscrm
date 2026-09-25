<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">WhatsApp Devices & Channels</h1>
                <x-tag color="primary" class="font-bold">{{ count($pairedDevices) }} Linked Devices</x-tag>
                @if ($credential && $credential->isConnected())
                    <x-tag color="emerald" prefix class="font-bold">Cloud API Live</x-tag>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Manage Baileys QR pairings, official Meta Cloud API credentials, and the automated Number Warmer engine.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-button wire:click="openPairModal" variant="solid" size="sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Connect WhatsApp (QR)</span>
            </x-button>
        </div>
    </div>

    <!-- Flash message -->
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <x-tabs>
        <x-tab-item wire:click="setTab('qr')" :active="$activeTab === 'qr'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <span>Paired Sessions (QR) ({{ count($pairedDevices) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('meta')" :active="$activeTab === 'meta'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Meta Cloud API</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('warmer')" :active="$activeTab === 'warmer'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
            <span>Number Warmer Engine</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('social')" :active="$activeTab === 'social'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span>Telegram & Social Channels</span>
        </x-tab-item>
    </x-tabs>

    <!-- ============================================================== -->
    <!-- TAB 1: PAIRED SESSIONS (QR / BAILEYS ENGINE)                   -->
    <!-- ============================================================== -->
    @if ($activeTab === 'qr')
        <div class="space-y-6">
            <!-- Baileys Info Banner -->
            <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-md relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1 z-10">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-white/20 backdrop-blur-md">Baileys Engine v6.7.8</span>
                        <span class="text-xs text-emerald-100 font-medium">Multi-Device Web Session Sync</span>
                    </div>
                    <h3 class="text-lg font-bold">Direct WhatsApp Web Browser Pairings</h3>
                    <p class="text-xs text-emerald-100 max-w-2xl">
                        Connect regular WhatsApp or WhatsApp Business numbers via QR code without official Meta API per-message fees. Messages and chats sync directly to your WhatsCRM inbox.
                    </p>
                </div>
                <div class="flex items-center gap-2 z-10 shrink-0">
                    <x-button wire:click="openPairModal" variant="default" size="sm" class="bg-white text-emerald-900 border-none hover:bg-emerald-50">
                        + Pair New Device
                    </x-button>
                </div>
                <div class="absolute -right-6 -bottom-6 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            <!-- Device Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($pairedDevices as $device)
                    <x-card bodyClass="p-6 flex flex-col justify-between h-full space-y-5">
                        <div class="space-y-4">
                            <!-- Card Header: Device info & Status -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">{{ $device['name'] }}</h3>
                                        <p class="text-xs font-mono font-semibold text-gray-500 dark:text-gray-400">{{ $device['phone'] }}</p>
                                    </div>
                                </div>

                                <x-tag color="emerald" prefix class="text-[10px] font-bold">Connected</x-tag>
                            </div>

                            <!-- Device Telemetry -->
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 space-y-2 text-xs">
                                <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                                    <span class="flex items-center gap-1.5 text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span>Battery & Power</span>
                                    </span>
                                    <span class="font-bold flex items-center gap-1">
                                        <span>{{ $device['battery'] }}%</span>
                                        @if($device['is_charging'] ?? false)
                                            <span class="text-[10px] text-emerald-500 font-bold">⚡ Charging</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                                    <span class="text-gray-400">Engine Protocol</span>
                                    <span class="font-mono font-medium text-[11px] text-gray-800 dark:text-gray-200">{{ $device['engine'] ?? 'Baileys Multi-Device' }}</span>
                                </div>

                                <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                                    <span class="text-gray-400">Last Synced</span>
                                    <span class="text-gray-500">{{ $device['last_sync'] ?? 'Just now' }}</span>
                                </div>
                            </div>

                            <!-- Daily Message Activity Bar -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-xs">
                                    <span class="font-semibold text-gray-500 dark:text-gray-400">Daily Messages Sent</span>
                                    <span class="font-mono font-bold text-gray-800 dark:text-gray-200">{{ $device['messages_today'] }} / {{ $device['daily_limit'] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full rounded-full transition-all" style="width: {{ min(100, round(($device['messages_today'] / max(1, $device['daily_limit'])) * 100)) }}%"></div>
                                </div>
                            </div>

                            <!-- Warmer Status Toggle -->
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Number Warmer</span>
                                    @if ($device['warmer_active'])
                                        <x-tag color="amber" class="text-[9px] font-bold">Active Warmer</x-tag>
                                    @else
                                        <x-tag color="default" class="text-[9px]">Idle</x-tag>
                                    @endif
                                </div>
                                <x-switcher :checked="$device['warmer_active']" wire:click="toggleWarmerDevice('{{ $device['id'] }}')" />
                            </div>
                        </div>

                        <!-- Card Actions Footer -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-2">
                            <x-button wire:click="reconnectQrDevice('{{ $device['id'] }}')" variant="default" size="xs">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Reconnect</span>
                            </x-button>

                            <x-button wire:click="disconnectQrDevice('{{ $device['id'] }}')" wire:confirm="Disconnect this WhatsApp session?" variant="plain" size="xs" class="text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Disconnect</span>
                            </x-button>
                        </div>
                    </x-card>
                @empty
                    <div class="col-span-full">
                        <x-card bodyClass="p-12 text-center space-y-4">
                            <div class="w-16 h-16 rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">No Paired WhatsApp Sessions</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                                    Link your personal or business WhatsApp number via QR scan to start sending and receiving chats directly in WhatsCRM.
                                </p>
                            </div>
                            <x-button wire:click="openPairModal" variant="solid" size="sm">
                                + Connect Your First Device
                            </x-button>
                        </x-card>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 2: META CLOUD API CONFIGURATION                            -->
    <!-- ============================================================== -->
    @if ($activeTab === 'meta')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Credentials Form & Test Message -->
            <div class="lg:col-span-2 space-y-6">
                <x-card bodyClass="p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-base text-gray-900 dark:text-white tracking-tight">Meta Cloud API Credentials</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Obtain these credentials from your Meta Developer Portal under WhatsApp &gt; API Setup.</p>
                        </div>
                        @if ($credential && $credential->isConnected())
                            <x-tag color="emerald" prefix class="font-bold text-[10px]">Active Integration</x-tag>
                        @endif
                    </div>

                    <form wire:submit.prevent="saveCredentials" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-item label="Phone Number ID" :required="true" :error="$errors->first('phone_number_id')">
                                <x-input wire:model="phone_number_id" :invalid="$errors->has('phone_number_id')" placeholder="e.g. 109876543210987" class="font-mono text-xs" />
                            </x-form-item>

                            <x-form-item label="WABA ID (WhatsApp Business Account ID)" :required="true" :error="$errors->first('waba_id')">
                                <x-input wire:model="waba_id" :invalid="$errors->has('waba_id')" placeholder="e.g. 987654321098765" class="font-mono text-xs" />
                            </x-form-item>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-item label="Display Phone Number">
                                <x-input wire:model="display_phone_number" placeholder="e.g. +1 555-019-2834" />
                            </x-form-item>

                            <x-form-item label="Meta App ID (Optional)">
                                <x-input wire:model="app_id" placeholder="e.g. 781923401928374" class="font-mono text-xs" />
                            </x-form-item>
                        </div>

                        <x-form-item label="Permanent System User Access Token" :required="true" :error="$errors->first('access_token')">
                            <x-input type="password" wire:model="access_token" :invalid="$errors->has('access_token')" placeholder="{{ $credential ? '••••••••••••••••••••••••••••••••' : 'EAAG...' }}" class="font-mono text-xs" />
                            <p class="text-[11px] text-gray-400 mt-1">Stored with 256-bit AES database encryption.</p>
                        </x-form-item>

                        <x-form-item label="Webhook Verify Token" :required="true" :error="$errors->first('verify_token')">
                            <x-input wire:model="verify_token" :invalid="$errors->has('verify_token')" class="font-mono text-xs" />
                        </x-form-item>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                            @if ($credential && $credential->isConnected())
                                <x-button wire:click="disconnect" wire:confirm="Are you sure you want to disconnect this Meta Cloud API account?" variant="plain" size="sm" class="text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                    Disconnect Integration
                                </x-button>
                            @else
                                <div></div>
                            @endif

                            <x-button type="submit" variant="solid" size="sm">
                                Save Meta Credentials
                            </x-button>
                        </div>
                    </form>
                </x-card>

                <!-- Live Ping Verification -->
                <x-card bodyClass="p-6 sm:p-8 space-y-4">
                    <div>
                        <h2 class="font-bold text-base text-gray-900 dark:text-white tracking-tight">Verify Connection (Live WhatsApp Ping)</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Send a real-time test message to any WhatsApp number to verify API delivery.</p>
                    </div>

                    @if ($test_status)
                        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold border border-emerald-200 dark:border-emerald-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ $test_status }}</span>
                        </div>
                    @endif

                    @if ($test_error)
                        <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-xs font-semibold border border-rose-200 dark:border-rose-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <span>{{ $test_error }}</span>
                        </div>
                    @endif

                    <form wire:submit.prevent="sendTestMessage" class="space-y-4">
                        <x-form-item label="Destination Phone Number" :required="true" :error="$errors->first('test_phone_number')">
                            <x-input wire:model="test_phone_number" :invalid="$errors->has('test_phone_number')" placeholder="+15551234567 (with country code)" class="font-mono text-xs" />
                        </x-form-item>

                        <div class="flex justify-end">
                            <x-button type="submit" wire:loading.attr="disabled" variant="default" size="sm">
                                <span wire:loading.remove>Send Test Ping</span>
                                <span wire:loading>Sending Message...</span>
                                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </x-button>
                        </div>
                    </form>
                </x-card>
            </div>

            <!-- Right Col: Meta Webhook Setup Instructions -->
            <div class="space-y-6">
                <x-card bodyClass="p-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                        <h3 class="font-bold text-sm text-gray-900 dark:text-white">Meta Webhook Configuration</h3>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        In your Meta App Dashboard under <strong>WhatsApp &gt; Configuration</strong>, set the Webhook fields to:
                    </p>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="block text-gray-400 font-semibold mb-1">Callback URL</span>
                            <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 font-mono text-[11px] text-gray-800 dark:text-gray-200 break-all select-all border border-gray-200 dark:border-gray-700">
                                {{ $webhookCallbackUrl }}
                            </div>
                        </div>

                        <div>
                            <span class="block text-gray-400 font-semibold mb-1">Verify Token</span>
                            <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 font-mono text-[11px] text-gray-800 dark:text-gray-200 select-all border border-gray-200 dark:border-gray-700">
                                {{ $verify_token }}
                            </div>
                        </div>

                        <div>
                            <span class="block text-gray-400 font-semibold mb-1">Webhook Subscription Fields</span>
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                <x-tag color="primary">messages</x-tag>
                                <x-tag color="primary">message_template_status_update</x-tag>
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- Features Checklist -->
                <x-card bodyClass="p-6 space-y-3">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white">Cloud API Capabilities</h3>
                    <div class="space-y-2.5 text-xs text-gray-600 dark:text-gray-300">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Official Meta 24-hour service window</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>High-volume broadcast campaigns</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Zero phone ban risk (official protocol)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Real-time read receipts & status webhooks</span>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 3: NUMBER WARMER ENGINE                                    -->
    <!-- ============================================================== -->
    @if ($activeTab === 'warmer')
        <div class="space-y-6">
            <!-- Warmer Status Banner -->
            <x-card bodyClass="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Peer-to-Peer WhatsApp Number Warmer</h2>
                            <x-tag color="amber" prefix class="font-bold">P2P Engine</x-tag>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-2xl">
                            Gradually builds reputation on newly registered SIM cards by simulating authentic human conversations between your paired devices before executing mass broadcasts.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Warmup Engine Status</span>
                        <x-switcher wire:model="warmerEnabled" />
                    </div>
                </div>

                <!-- 4 Warmup Progression Stage Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900 dark:text-white">Stage 1: Days 1–3</span>
                            <x-tag color="blue" class="text-[9px]">Gentle</x-tag>
                        </div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">10 <span class="text-xs font-normal text-gray-400">msgs/day</span></p>
                        <p class="text-[11px] text-gray-400">Short greetings & ping exchanges</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900 dark:text-white">Stage 2: Days 4–7</span>
                            <x-tag color="emerald" class="text-[9px]">Moderate</x-tag>
                        </div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">35 <span class="text-xs font-normal text-gray-400">msgs/day</span></p>
                        <p class="text-[11px] text-gray-400">2-way multi-turn dialogues</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900 dark:text-white">Stage 3: Days 8–14</span>
                            <x-tag color="purple" class="text-[9px]">Elevated</x-tag>
                        </div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">75 <span class="text-xs font-normal text-gray-400">msgs/day</span></p>
                        <p class="text-[11px] text-gray-400">Simulated rich media & audio notes</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900 dark:text-white">Stage 4: Day 15+</span>
                            <x-tag color="primary" class="text-[9px]">Full Broadcast</x-tag>
                        </div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">150+ <span class="text-xs font-normal text-gray-400">msgs/day</span></p>
                        <p class="text-[11px] text-gray-400">Ready for mass outbound campaigns</p>
                    </div>
                </div>
            </x-card>

            <!-- Configuration & Script Selectors -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Warmup Rules Form -->
                <x-card bodyClass="p-6 sm:p-8 space-y-5">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Warmup Interval & Throttling Rules</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Control inter-message sleep jitter to prevent automated spam detection.</p>
                    </div>

                    <form wire:submit.prevent="saveWarmerSettings" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-item label="Minimum Sleep (seconds)" :required="true">
                                <x-input type="number" wire:model="minSleep" min="5" max="120" />
                            </x-form-item>

                            <x-form-item label="Maximum Sleep (seconds)" :required="true">
                                <x-input type="number" wire:model="maxSleep" min="10" max="300" />
                            </x-form-item>
                        </div>

                        <x-form-item label="Max Daily Messages Per Number" :required="true">
                            <x-input type="number" wire:model="maxDailyPerNumber" min="10" max="1000" />
                        </x-form-item>

                        <x-form-item label="Conversation Script Library" :required="true">
                            <x-select wire:model="warmerScript">
                                <option value="casual_dialogue">Casual E-Commerce & Tech Dialogue (120 turns)</option>
                                <option value="customer_support">Customer Service QA Dialogues (90 turns)</option>
                                <option value="friendly_greetings">Casual Daily Greetings & Small Talk (60 turns)</option>
                            </x-select>
                        </x-form-item>

                        <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-800">
                            <x-button type="submit" variant="solid" size="sm">
                                Save Warmer Preferences
                            </x-button>
                        </div>
                    </form>
                </x-card>

                <!-- Active Warmup Pairing Matrix -->
                <x-card bodyClass="p-6 sm:p-8 space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Active Device Warmup Matrix</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Paired device communication channels participating in automated warming.</p>
                    </div>

                    <div class="space-y-3">
                        @foreach ($pairedDevices as $d)
                            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-xl {{ $d['warmer_active'] ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }} flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $d['name'] }}</h4>
                                        <p class="text-[11px] font-mono text-gray-400">{{ $d['phone'] }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if ($d['warmer_active'])
                                        <span class="text-[11px] text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                            <span>Warming</span>
                                        </span>
                                    @else
                                        <span class="text-[11px] text-gray-400">Idle</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 4: TELEGRAM & SOCIAL CHANNELS                              -->
    <!-- ============================================================== -->
    @if ($activeTab === 'social')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Telegram Bot API -->
            <x-card bodyClass="p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-500 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Telegram Bot API</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Receive and respond to Telegram customer messages.</p>
                        </div>
                    </div>

                    @if ($telegramConnected)
                        <x-tag color="emerald" prefix class="text-[10px] font-bold">Connected</x-tag>
                    @endif
                </div>

                <form wire:submit.prevent="saveSocialSettings" class="space-y-4">
                    <x-form-item label="Telegram Bot Token" :required="true">
                        <x-input wire:model="telegramBotToken" placeholder="123456789:ABCdefGHIjklmNOPqrsTUVwxyz" class="font-mono text-xs" />
                        <p class="text-[11px] text-gray-400 mt-1">Obtain from @BotFather on Telegram.</p>
                    </x-form-item>

                    <x-form-item label="Bot Username">
                        <x-input wire:model="telegramBotUsername" placeholder="@MyCompanyCRM_bot" />
                    </x-form-item>

                    <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-800">
                        <x-button type="submit" variant="solid" size="sm">
                            Save Telegram Bot
                        </x-button>
                    </div>
                </form>
            </x-card>

            <!-- Instagram & Facebook Messenger -->
            <x-card bodyClass="p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-pink-50 dark:bg-pink-950/50 text-pink-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Instagram Direct & Messenger</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Omnichannel Meta messaging directly in your unified inbox.</p>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <div class="p-4 rounded-xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-center justify-between gap-4">
                        <div>
                            <span class="font-bold text-sm text-gray-800 dark:text-gray-200 block">Instagram Business Direct</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Sync direct DMs and story replies</span>
                        </div>
                        <x-switcher wire:model="instagramConnected" />
                    </div>

                    <div class="p-4 rounded-xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-center justify-between gap-4">
                        <div>
                            <span class="font-bold text-sm text-gray-800 dark:text-gray-200 block">Facebook Messenger Page</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Sync customer Facebook page inbox</span>
                        </div>
                        <x-switcher wire:model="messengerConnected" />
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button wire:click="saveSocialSettings" variant="solid" size="sm">
                        Save Social Integrations
                    </x-button>
                </div>
            </x-card>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: QR CODE PAIRING DIALOG                                  -->
    <!-- ============================================================== -->
    <x-modal name="pair-device-modal" :show="$showPairModal" focusable>
        <div class="p-6 sm:p-8 space-y-6">
            <!-- Modal Header -->
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Link WhatsApp Device</span>
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Pair your phone via WhatsApp Web scanner or 8-digit pairing code.
                    </p>
                </div>
                <button wire:click="closePairModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Segment Switcher: QR Code vs Phone Code -->
            <x-segment>
                <x-segment-item :active="$pairModalTab === 'qr'" wire:click="setPairModalTab('qr')">
                    QR Code Scan
                </x-segment-item>
                <x-segment-item :active="$pairModalTab === 'code'" wire:click="setPairModalTab('code')">
                    Pairing Code
                </x-segment-item>
            </x-segment>

            @if ($pairModalTab === 'qr')
                <!-- Tab 1: QR Code Scanner Display -->
                <div class="flex flex-col items-center justify-center py-2 space-y-4">
                    <div class="relative p-4 rounded-2xl bg-white dark:bg-gray-800 border-2 border-dashed border-emerald-500/40 shadow-inner flex items-center justify-center">
                        <!-- Simulated QR Code SVG -->
                        <div class="w-48 h-48 bg-white p-2 rounded-xl flex items-center justify-center relative overflow-hidden shadow-xs">
                            <svg class="w-full h-full text-gray-900" viewBox="0 0 100 100" fill="currentColor">
                                <rect x="5" y="5" width="25" height="25" fill="#000" rx="3" />
                                <rect x="9" y="9" width="17" height="17" fill="#fff" rx="2" />
                                <rect x="13" y="13" width="9" height="9" fill="#000" />
                                
                                <rect x="70" y="5" width="25" height="25" fill="#000" rx="3" />
                                <rect x="74" y="9" width="17" height="17" fill="#fff" rx="2" />
                                <rect x="78" y="13" width="9" height="9" fill="#000" />
                                
                                <rect x="5" y="70" width="25" height="25" fill="#000" rx="3" />
                                <rect x="9" y="74" width="17" height="17" fill="#fff" rx="2" />
                                <rect x="13" y="78" width="9" height="9" fill="#000" />
                                
                                <rect x="35" y="10" width="10" height="10" fill="#000" />
                                <rect x="50" y="15" width="12" height="6" fill="#000" />
                                <rect x="35" y="35" width="25" height="25" fill="#10b981" rx="4" />
                                <rect x="10" y="40" width="15" height="8" fill="#000" />
                                <rect x="40" y="70" width="15" height="15" fill="#000" />
                                <rect x="65" y="40" width="25" height="10" fill="#000" />
                                <rect x="70" y="65" width="20" height="20" fill="#000" />
                            </svg>

                            <!-- Animated Scanning Laser Line -->
                            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent animate-pulse"></div>

                            <!-- WhatsApp Logo in Center -->
                            <div class="absolute w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg ring-4 ring-white">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.971.53 1.771.815 2.796.815 3.183 0 5.769-2.586 5.77-5.766.001-3.18-2.585-5.766-5.77-5.766zm0 10.378c-.896 0-1.67-.253-2.385-.678l-.171-.102-1.777.466.474-1.733-.112-.178c-.467-.743-.714-1.554-.714-2.387 0-2.54 2.067-4.607 4.685-4.607 2.54 0 4.607 2.067 4.607 4.607 0 2.54-2.067 4.607-4.607 4.607z"/></svg>
                            </div>
                        </div>
                    </div>

                    <div class="text-center space-y-1">
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Scan this code with WhatsApp</p>
                        <ol class="text-[11px] text-gray-500 dark:text-gray-400 space-y-0.5 text-left max-w-xs">
                            <li>1. Open WhatsApp on your mobile phone</li>
                            <li>2. Tap <strong>Settings</strong> &gt; <strong>Linked Devices</strong></li>
                            <li>3. Point your camera at this screen to scan</li>
                        </ol>
                    </div>
                </div>
            @else
                <!-- Tab 2: 8-Digit Pairing Code -->
                <div class="space-y-4 py-2">
                    <x-form-item label="Your WhatsApp Mobile Number" :required="true">
                        <x-input wire:model="newDevicePhone" placeholder="+1 (555) 019-2834" />
                    </x-form-item>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 text-center space-y-2">
                        <span class="text-xs text-gray-500 font-semibold block">Enter this code on your WhatsApp phone:</span>
                        <div class="font-mono text-2xl font-black text-primary tracking-widest bg-white dark:bg-gray-900 py-3 rounded-xl border border-gray-200 dark:border-gray-700 select-all">
                            {{ $pairingCode }}
                        </div>
                        <p class="text-[11px] text-gray-400">Settings &gt; Linked Devices &gt; Link with phone number instead</p>
                    </div>
                </div>
            @endif

            <x-form-item label="Device Friendly Label / Alias" :required="true">
                <x-input wire:model="newDeviceName" placeholder="e.g. Sales Support Line 2" />
            </x-form-item>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                <x-button wire:click="closePairModal" variant="default" size="sm" type="button">
                    Cancel
                </x-button>
                <x-button wire:click="confirmPairing" variant="solid" size="sm" type="button">
                    Confirm & Complete Pairing
                </x-button>
            </div>
        </div>
    </x-modal>
</div>
