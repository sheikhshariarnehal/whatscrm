<div class="p-4 sm:p-6 lg:p-8 space-y-6" @if($showPairModal) wire:poll.2s="pollQrStatus" @endif>
    <!-- Page Header & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">WhatsApp Devices & Channels</h1>
                <x-tag color="primary" class="font-bold">
                    <x-ph-icon name="devices" weight="bold" class="text-xs mr-1" />
                    {{ count($pairedDevices) }} Linked Devices
                </x-tag>
                @if ($metaConnected)
                    <x-tag color="emerald" :prefix="true" class="font-bold">
                        Cloud API Live
                    </x-tag>
                @endif
                @if ($warmerEngineRunning)
                    <x-tag color="amber" class="font-bold">
                        <x-ph-icon name="flame" weight="fill" class="text-xs mr-1 text-amber-500" />
                        Warmer Active ({{ $activeWarmerCount }} Lines)
                    </x-tag>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Manage Baileys QR pairings, official Meta Cloud API credentials, and the automated Number Warmer engine.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <x-button wire:click="openPairModal" variant="solid" size="md" class="gap-2">
                <x-ph-icon name="qr-code" weight="bold" class="text-lg" />
                <span>Connect WhatsApp (QR)</span>
            </x-button>
        </div>
    </div>

    <!-- Top KPI Telemetry Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Total Channels -->
        <x-card bodyClass="p-5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Active Channels</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalChannels }}</span>
                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ count($pairedDevices) }} QR + {{ $metaConnected ? '1 Meta' : '0 Meta' }}</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <x-ph-icon name="whatsapp-logo" weight="fill" class="text-2xl" />
            </div>
        </x-card>

        <!-- KPI 2: Meta Cloud API Status -->
        <x-card bodyClass="p-5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Meta Cloud API</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-base font-bold text-gray-900 dark:text-white">
                        {{ $metaConnected ? 'Live & Connected' : 'Unconfigured' }}
                    </span>
                </div>
                <p class="text-[11px] font-mono text-gray-400 truncate max-w-[160px]">
                    {{ $display_phone_number ?: ($metaConnected ? 'ID: ' . substr($phone_number_id, -6) : 'Setup in Meta tab') }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl {{ $metaConnected ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }} flex items-center justify-center shrink-0">
                <x-ph-icon name="lightning" weight="duotone" class="text-2xl" />
            </div>
        </x-card>

        <!-- KPI 3: Messages Dispatched Today -->
        <x-card bodyClass="p-5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Dispatched Today</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($totalMessagesToday) }}</span>
                    <span class="text-xs font-medium text-gray-400">messages</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <x-ph-icon name="paper-plane-tilt" weight="duotone" class="text-2xl" />
            </div>
        </x-card>

        <!-- KPI 4: Number Warmer Loop -->
        <x-card bodyClass="p-5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Number Warmer Loop</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-base font-bold text-gray-900 dark:text-white">
                        {{ $warmerEngineRunning ? 'Engine Active' : 'Engine Paused' }}
                    </span>
                </div>
                <p class="text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                    {{ $activeWarmerCount }} Peer Lines Warming
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <x-ph-icon name="flame" weight="duotone" class="text-2xl" />
            </div>
        </x-card>
    </div>

    <!-- Alert Notifications / Feedback Messages -->
    @if (session()->has('message'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <x-ph-icon name="check-circle" weight="fill" class="text-lg text-emerald-500 shrink-0" />
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 dark:hover:text-emerald-200 p-1">
                <x-ph-icon name="x" weight="bold" class="text-sm" />
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <x-ph-icon name="warning-circle" weight="fill" class="text-lg text-rose-500 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-800 dark:hover:text-rose-200 p-1">
                <x-ph-icon name="x" weight="bold" class="text-sm" />
            </button>
        </div>
    @endif

    <!-- Navigation Tabs Bar -->
    <x-tabs>
        <x-tab-item wire:click="setTab('qr')" :active="$activeTab === 'qr'">
            <x-ph-icon name="qr-code" weight="duotone" class="text-lg shrink-0" />
            <span>Paired Sessions (QR)</span>
            <x-tag color="primary" class="ml-1 text-[10px] font-bold py-0.5 px-2">{{ count($pairedDevices) }}</x-tag>
        </x-tab-item>
        <x-tab-item wire:click="setTab('meta')" :active="$activeTab === 'meta'">
            <x-ph-icon name="cloud-check" weight="duotone" class="text-lg shrink-0" />
            <span>Meta Cloud API</span>
            @if ($metaConnected)
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse ml-1"></span>
            @endif
        </x-tab-item>
        <x-tab-item wire:click="setTab('warmer')" :active="$activeTab === 'warmer'">
            <x-ph-icon name="flame" weight="duotone" class="text-lg shrink-0" />
            <span>Number Warmer Engine</span>
            @if ($activeWarmerCount > 0)
                <x-tag color="amber" class="ml-1 text-[10px] font-bold py-0.5 px-2">{{ $activeWarmerCount }}</x-tag>
            @endif
        </x-tab-item>
        <x-tab-item wire:click="setTab('social')" :active="$activeTab === 'social'">
            <x-ph-icon name="share-network" weight="duotone" class="text-lg shrink-0" />
            <span>Telegram & Social Channels</span>
            @if ($telegramConnected || $instagramConnected || $messengerConnected)
                <x-tag color="emerald" class="ml-1 text-[10px] font-bold py-0.5 px-2">Active</x-tag>
            @endif
        </x-tab-item>
    </x-tabs>

    <!-- ============================================================== -->
    <!-- TAB 1: PAIRED SESSIONS (QR / BAILEYS ENGINE)                   -->
    <!-- ============================================================== -->
    @if ($activeTab === 'qr')
        <div class="space-y-6">


            <!-- Device Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($pairedDevices as $device)
                    <x-card bodyClass="p-6 flex flex-col justify-between h-full space-y-5 hover:border-gray-300 dark:hover:border-gray-700 transition-all duration-200">
                        <div class="space-y-4">
                            <!-- Card Header: Device info & Status -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 ring-4 ring-emerald-500/10">
                                        <x-ph-icon name="whatsapp-logo" weight="fill" class="text-2xl" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">{{ $device['name'] }}</h3>
                                        <p class="text-xs font-mono font-semibold text-gray-500 dark:text-gray-400 mt-0.5">{{ $device['phone'] }}</p>
                                    </div>
                                </div>

                                <x-tag color="emerald" :prefix="true" class="text-[10px] font-bold shrink-0">Connected</x-tag>
                            </div>

                            <!-- Device Telemetry Panel -->
                            <div class="p-3.5 rounded-2xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 space-y-2.5 text-xs">
                                <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                                    <span class="flex items-center gap-1.5 text-gray-400">
                                        <x-ph-icon name="{{ ($device['is_charging'] ?? false) ? 'battery-charging' : 'battery-high' }}" weight="duotone" class="text-base text-emerald-500" />
                                        <span>Battery & Power</span>
                                    </span>
                                    <span class="font-bold flex items-center gap-1.5">
                                        <span>{{ $device['battery'] ?? 90 }}%</span>
                                        @if($device['is_charging'] ?? false)
                                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-100/60 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded">⚡ Charging</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                                    <span class="flex items-center gap-1.5 text-gray-400">
                                        <x-ph-icon name="cpu" weight="duotone" class="text-base text-gray-400" />
                                        <span>Protocol Engine</span>
                                    </span>
                                    <span class="font-mono font-medium text-[11px] text-gray-800 dark:text-gray-200">{{ $device['engine'] ?? 'Baileys Multi-Device' }}</span>
                                </div>

                                <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                                    <span class="flex items-center gap-1.5 text-gray-400">
                                        <x-ph-icon name="clock" weight="duotone" class="text-base text-gray-400" />
                                        <span>Last Synced</span>
                                    </span>
                                    <span class="text-gray-500 font-medium">{{ $device['last_sync'] ?? 'Just now' }}</span>
                                </div>
                            </div>

                            <!-- Daily Message Volume Progress Bar -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-xs font-semibold">
                                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                        <x-ph-icon name="chat-teardrop-text" weight="duotone" class="text-sm text-gray-400" />
                                        Daily Outbound
                                    </span>
                                    <span class="font-mono text-gray-800 dark:text-gray-200">{{ $device['messages_today'] ?? 0 }} / {{ $device['daily_limit'] ?? 1000 }} msgs</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-800 h-2.5 rounded-full overflow-hidden p-0.5">
                                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-300" style="width: {{ min(100, round((($device['messages_today'] ?? 0) / max(1, ($device['daily_limit'] ?? 1000))) * 100)) }}%"></div>
                                </div>
                            </div>

                            <!-- Warmer Status Toggle Row -->
                            <div class="flex items-center justify-between pt-1 p-2 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-100/80 dark:border-amber-900/30">
                                <div class="flex items-center gap-2">
                                    <x-ph-icon name="flame" weight="fill" class="text-base text-amber-500" />
                                    <div>
                                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200 block">Number Warmer</span>
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400 block">{{ ($device['warmer_active'] ?? false) ? 'Participating in daily dialogues' : 'Warmer participation idle' }}</span>
                                    </div>
                                </div>
                                <x-switcher :checked="$device['warmer_active'] ?? false" wire:click="toggleWarmerDevice('{{ $device['id'] }}')" />
                            </div>
                        </div>

                        <!-- Card Actions Footer -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-2">
                            <x-button wire:click="reconnectQrDevice('{{ $device['id'] }}')" variant="default" size="sm">
                                <x-ph-icon name="arrows-clockwise" weight="bold" class="text-sm mr-1 text-emerald-600" />
                                <span>Reconnect</span>
                            </x-button>

                            <x-button wire:click="disconnectQrDevice('{{ $device['id'] }}')" wire:confirm="Disconnect this WhatsApp session? This will remove the link from WhatsCRM." variant="plain" size="sm" class="text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                <x-ph-icon name="trash" weight="duotone" class="text-sm mr-1" />
                                <span>Disconnect</span>
                            </x-button>
                        </div>
                    </x-card>
                @empty
                    <div class="col-span-full">
                        <x-card bodyClass="p-12 text-center space-y-4">
                            <div class="w-16 h-16 rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center mx-auto ring-8 ring-emerald-500/10">
                                <x-ph-icon name="qr-code" weight="duotone" class="text-3xl" />
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">No Paired WhatsApp Sessions</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                                    Link your personal or business WhatsApp number via QR scan to start sending and receiving chats directly in WhatsCRM.
                                </p>
                            </div>
                            <x-button wire:click="openPairModal" variant="solid" size="md">
                                <x-ph-icon name="plus" weight="bold" class="text-base mr-1.5" />
                                <span>Connect Your First Device</span>
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
            <!-- Left 2 Cols: Credentials Form & Diagnostics Sandbox -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Credentials Configuration Form Card -->
                <x-card bodyClass="p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                <x-ph-icon name="meta-logo" weight="fill" class="text-xl" />
                            </div>
                            <div>
                                <h2 class="font-bold text-base text-gray-900 dark:text-white tracking-tight">Meta Cloud API Credentials</h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Obtain these credentials from your Meta Developer Portal under WhatsApp &gt; API Setup.</p>
                            </div>
                        </div>
                        @if ($metaConnected)
                            <x-tag color="emerald" :prefix="true" class="font-bold text-[10px]">Active Integration</x-tag>
                        @else
                            <x-tag color="gray" class="text-[10px]">Not Connected</x-tag>
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
                                <x-input wire:model="display_phone_number" placeholder="e.g. +1 (555) 019-2834" />
                            </x-form-item>

                            <x-form-item label="Meta App ID (Optional)">
                                <x-input wire:model="app_id" placeholder="e.g. 781923401928374" class="font-mono text-xs" />
                            </x-form-item>
                        </div>

                        <x-form-item label="Permanent System User Access Token" :required="true" :error="$errors->first('access_token')">
                            <x-input type="password" wire:model="access_token" :invalid="$errors->has('access_token')" placeholder="{{ $credential ? '••••••••••••••••••••••••••••••••' : 'EAAG...' }}" class="font-mono text-xs" />
                            <p class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1.5">
                                <x-ph-icon name="shield-check" weight="duotone" class="text-sm text-emerald-500" />
                                Stored securely in database with 256-bit AES encryption.
                            </p>
                        </x-form-item>

                        <x-form-item label="Webhook Verify Token" :required="true" :error="$errors->first('verify_token')">
                            <x-input wire:model="verify_token" :invalid="$errors->has('verify_token')" class="font-mono text-xs" />
                        </x-form-item>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                            @if ($metaConnected)
                                <x-button wire:click="disconnect" wire:confirm="Are you sure you want to disconnect this Meta Cloud API account?" variant="plain" size="sm" class="text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                    <x-ph-icon name="link-break" weight="bold" class="text-sm mr-1" />
                                    Disconnect Integration
                                </x-button>
                            @else
                                <div></div>
                            @endif

                            <x-button type="submit" variant="solid" size="md">
                                <x-ph-icon name="check-circle" weight="bold" class="text-base mr-1.5" />
                                Save Meta Credentials
                            </x-button>
                        </div>
                    </form>
                </x-card>

                <!-- Live Ping Verification & Webhook Sandbox -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Outbound Ping Tool -->
                    <x-card bodyClass="p-6 space-y-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                <x-ph-icon name="paper-plane-tilt" weight="duotone" class="text-base" />
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-gray-900 dark:text-white tracking-tight">Verify Outbound Message (Ping)</h3>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Send a real test WhatsApp message to verify Cloud API dispatch.</p>
                            </div>
                        </div>

                        @if ($test_status)
                            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold border border-emerald-200 dark:border-emerald-800 flex items-center gap-2">
                                <x-ph-icon name="check-circle" weight="fill" class="text-base text-emerald-500 shrink-0" />
                                <span>{{ $test_status }}</span>
                            </div>
                        @endif

                        @if ($test_error)
                            <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-xs font-semibold border border-rose-200 dark:border-rose-800 flex items-center gap-2">
                                <x-ph-icon name="warning-circle" weight="fill" class="text-base text-rose-500 shrink-0" />
                                <span>{{ $test_error }}</span>
                            </div>
                        @endif

                        <form wire:submit.prevent="sendTestMessage" class="space-y-3">
                            <x-form-item label="Destination Phone Number" :required="true">
                                <x-input wire:model="test_phone_number" placeholder="+1 (555) 019-2834" class="font-mono text-xs" />
                            </x-form-item>

                            <x-button type="submit" wire:loading.attr="disabled" variant="default" size="sm" class="w-full">
                                <span wire:loading.remove class="flex items-center justify-center gap-1.5">
                                    <x-ph-icon name="paper-plane-tilt" weight="bold" class="text-sm text-primary" />
                                    <span>Send Test Ping</span>
                                </span>
                                <span wire:loading class="flex items-center justify-center gap-1.5">
                                    <x-ph-icon name="spinner" weight="bold" class="text-sm animate-spin" />
                                    <span>Sending via Cloud API...</span>
                                </span>
                            </x-button>
                        </form>
                    </x-card>

                    <!-- Webhook Ingestion & Template Tools -->
                    <x-card bodyClass="p-6 space-y-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <x-ph-icon name="plugs-connected" weight="duotone" class="text-base" />
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-gray-900 dark:text-white tracking-tight">Webhook & Template Tools</h3>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Simulate inbound lead chats and synchronize Meta templates.</p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-1">
                            <x-button wire:click="simulateInboundWebhook" variant="default" size="sm" class="w-full flex items-center justify-center gap-2">
                                <x-ph-icon name="chat-circle-dots" weight="duotone" class="text-base text-emerald-500" />
                                <span>Simulate Inbound Webhook Chat</span>
                            </x-button>

                            <x-button wire:click="syncTemplates" variant="default" size="sm" class="w-full flex items-center justify-center gap-2">
                                <x-ph-icon name="arrows-clockwise" weight="duotone" class="text-base text-sky-500" />
                                <span>Sync WhatsApp Templates</span>
                            </x-button>
                        </div>
                    </x-card>
                </div>
            </div>

            <!-- Right Col: Meta Webhook Setup Instructions -->
            <div class="space-y-6">
                <x-card bodyClass="p-6 space-y-5">
                    <div class="flex items-center gap-2.5 pb-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="font-bold text-sm text-gray-900 dark:text-white">Meta Webhook Configuration</h3>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        In your Meta App Dashboard under <strong>WhatsApp &gt; Configuration</strong>, configure these endpoint values:
                    </p>

                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 font-semibold mb-1">Callback URL</span>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 font-mono text-[11px] text-gray-800 dark:text-gray-200 break-all select-all border border-gray-200 dark:border-gray-700 flex items-center justify-between gap-2">
                                <span>{{ $webhookCallbackUrl }}</span>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $webhookCallbackUrl }}'); alert('Callback URL copied!')" class="text-primary hover:text-primary-dark font-bold text-xs shrink-0 flex items-center gap-1 p-1">
                                    <x-ph-icon name="copy" weight="bold" />
                                    <span>Copy</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 font-semibold mb-1">Verify Token</span>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 font-mono text-[11px] text-gray-800 dark:text-gray-200 select-all border border-gray-200 dark:border-gray-700 flex items-center justify-between gap-2">
                                <span>{{ $verify_token }}</span>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $verify_token }}'); alert('Verify token copied!')" class="text-primary hover:text-primary-dark font-bold text-xs shrink-0 flex items-center gap-1 p-1">
                                    <x-ph-icon name="copy" weight="bold" />
                                    <span>Copy</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 font-semibold mb-1.5">Webhook Field Subscriptions</span>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 text-[11px] text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 space-y-1">
                                <div class="flex items-center gap-2">
                                    <x-ph-icon name="check" weight="bold" class="text-emerald-500" />
                                    <span>Subscribe to: <code class="font-bold text-primary font-mono">messages</code></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-ph-icon name="check" weight="bold" class="text-emerald-500" />
                                    <span>Subscribe to: <code class="font-bold text-primary font-mono">message_template_status_update</code></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step-by-step Quick Setup Guide -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800 space-y-2 text-xs">
                        <span class="font-bold text-gray-800 dark:text-gray-200 block">Setup Instructions:</span>
                        <ol class="space-y-1.5 text-gray-500 dark:text-gray-400 text-[11px] pl-4 list-decimal">
                            <li>Go to <a href="https://developers.facebook.com" target="_blank" class="text-primary hover:underline font-semibold">developers.facebook.com</a> &gt; Your App.</li>
                            <li>Select <strong>WhatsApp &gt; Configuration</strong> on the sidebar.</li>
                            <li>Paste the Callback URL and Verify Token above, then click <strong>Verify and Save</strong>.</li>
                            <li>Click <strong>Manage Subscriptions</strong> and check <code>messages</code>.</li>
                        </ol>
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
            <!-- Stage Progression Metrics Banner -->
            <x-card bodyClass="p-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <x-ph-icon name="flame" weight="fill" class="text-xl" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">WhatsApp Number Warmer Engine</h3>
                                @if ($warmerEngineRunning)
                                    <x-tag color="emerald" class="text-[10px] font-bold">Engine Running</x-tag>
                                @else
                                    <x-tag color="amber" class="text-[10px] font-bold">Engine Paused</x-tag>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Automated peer-to-peer dialogues between paired lines to build phone number reputation and prevent spam bans.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <x-button wire:click="toggleEngineState" variant="{{ $warmerEngineRunning ? 'plain' : 'solid' }}" size="sm" class="{{ $warmerEngineRunning ? 'text-amber-600 border border-amber-200 dark:border-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/30' : 'bg-emerald-600 text-white' }}">
                            <x-ph-icon name="{{ $warmerEngineRunning ? 'pause' : 'play' }}" weight="bold" class="text-sm mr-1.5" />
                            <span>{{ $warmerEngineRunning ? 'Pause Warmer' : 'Start Warmer' }}</span>
                        </x-button>

                        <x-button wire:click="runInstantWarmupTest" variant="default" size="sm">
                            <x-ph-icon name="lightning" weight="bold" class="text-sm mr-1.5 text-amber-500" />
                            <span>Simulate Dialogue Test</span>
                        </x-button>
                    </div>
                </div>

                <!-- 4-Stage Warmup Roadmap Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-1">
                    <div class="p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900 dark:text-white">Stage 1: Day 1–3</span>
                            <x-tag color="gray" class="text-[9px] font-bold">Cold Start</x-tag>
                        </div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">5 <span class="text-xs font-normal text-gray-400">msgs/day</span></p>
                        <p class="text-[11px] text-gray-400">Light handshake small talk</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900 dark:text-white">Stage 2: Day 4–7</span>
                            <x-tag color="amber" class="text-[9px] font-bold">Warming</x-tag>
                        </div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">15 <span class="text-xs font-normal text-gray-400">msgs/day</span></p>
                        <p class="text-[11px] text-gray-400">Bidirectional conversational flow</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900 dark:text-white">Stage 3: Day 8–14</span>
                            <x-tag color="emerald" class="text-[9px] font-bold">Warm</x-tag>
                        </div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">50 <span class="text-xs font-normal text-gray-400">msgs/day</span></p>
                        <p class="text-[11px] text-gray-400">Simulated natural customer inquiry</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900 dark:text-white">Stage 4: Day 15+</span>
                            <x-tag color="primary" class="text-[9px] font-bold">Broadcast Ready</x-tag>
                        </div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">150+ <span class="text-xs font-normal text-gray-400">msgs/day</span></p>
                        <p class="text-[11px] text-gray-400">Ready for mass outbound broadcast</p>
                    </div>
                </div>
            </x-card>

            <!-- Configuration & Active Matrix -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Warmup Rules Form Card -->
                <x-card bodyClass="p-6 sm:p-8 space-y-5">
                    <div class="border-b border-gray-100 dark:border-gray-800 pb-3">
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
                            <x-button type="submit" variant="solid" size="md">
                                <x-ph-icon name="check-circle" weight="bold" class="text-base mr-1.5" />
                                Save Warmer Preferences
                            </x-button>
                        </div>
                    </form>
                </x-card>

                <!-- Active Warmup Pairing Matrix Card -->
                <x-card bodyClass="p-6 sm:p-8 space-y-4">
                    <div class="border-b border-gray-100 dark:border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Active Device Warmup Matrix</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Paired device communication channels participating in automated warming.</p>
                    </div>

                    <div class="space-y-3">
                        @forelse ($pairedDevices as $d)
                            <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl {{ ($d['warmer_active'] ?? false) ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }} flex items-center justify-center shrink-0">
                                        <x-ph-icon name="flame" weight="fill" class="text-lg" />
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $d['name'] }}</h4>
                                        <p class="text-[11px] font-mono text-gray-400">{{ $d['phone'] }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if ($d['warmer_active'] ?? false)
                                        <span class="text-[11px] text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                            <span>Warming Active</span>
                                        </span>
                                    @else
                                        <span class="text-[11px] text-gray-400 font-medium">Idle</span>
                                    @endif
                                    <x-switcher :checked="$d['warmer_active'] ?? false" wire:click="toggleWarmerDevice('{{ $d['id'] }}')" />
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">No paired devices found to participate in warming.</p>
                        @endforelse
                    </div>

                    <!-- Live Dialogue Activity Log -->
                    @if (count($warmerActivityLogs) > 0)
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800 space-y-2">
                            <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <x-ph-icon name="terminal-window" weight="duotone" class="text-sm text-primary" />
                                Recent Warmup Dialogue Turns:
                            </span>
                            <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                                @foreach ($warmerActivityLogs as $log)
                                    <div class="p-2.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-[11px] flex items-center justify-between gap-2 shadow-2xs">
                                        <div class="truncate">
                                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $log['from'] }} &rarr; {{ $log['to'] }}:</span>
                                            <span class="text-gray-500 italic">"{{ $log['message'] }}"</span>
                                        </div>
                                        <span class="font-mono text-[10px] text-emerald-600 font-bold shrink-0">{{ $log['time'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-card>
            </div>

            <!-- Dialogue Script Library Manager Card -->
            <x-card bodyClass="p-6 sm:p-8 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Warmer Conversation Dialogue Script Manager</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Customize the natural phrases and chat turns exchanged between warming numbers.</p>
                    </div>
                </div>

                <!-- Add new script turn -->
                <form wire:submit.prevent="addScriptMessage" class="flex gap-2">
                    <x-input wire:model="newScriptText" placeholder="Add a new dialogue line (e.g. 'Hey, did you get a chance to review the proposal?')" class="text-xs flex-1" />
                    <x-button type="submit" variant="solid" size="md" class="shrink-0">
                        <x-ph-icon name="plus" weight="bold" class="text-sm mr-1" />
                        <span>Add Dialogue Turn</span>
                    </x-button>
                </form>

                <!-- Script turns list chips -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($warmerScripts as $s)
                        <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 flex items-center justify-between gap-2">
                            <span class="text-xs text-gray-800 dark:text-gray-200 truncate font-medium">"{{ $s['message'] }}"</span>
                            @if(isset($s['id']))
                                <button type="button" wire:click="deleteScriptMessage({{ $s['id'] }})" class="text-gray-400 hover:text-rose-500 text-xs shrink-0 p-1">
                                    <x-ph-icon name="trash" weight="bold" />
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 4: TELEGRAM & SOCIAL CHANNELS                              -->
    <!-- ============================================================== -->
    @if ($activeTab === 'social')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Telegram Bot API Card -->
            <x-card bodyClass="p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-950/50 text-sky-500 flex items-center justify-center shrink-0">
                            <x-ph-icon name="paper-plane-tilt" weight="fill" class="text-xl" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Telegram Bot API</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Receive and respond to Telegram customer messages.</p>
                        </div>
                    </div>

                    @if ($telegramConnected)
                        <x-tag color="emerald" :prefix="true" class="text-[10px] font-bold">Connected</x-tag>
                    @endif
                </div>

                <div class="space-y-4">
                    <x-form-item label="Telegram Bot Token" :required="true">
                        <x-input wire:model="telegramBotToken" placeholder="123456789:ABCdefGHIjklmNOPqrsTUVwxyz" class="font-mono text-xs" />
                        <p class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1">
                            <x-ph-icon name="info" weight="bold" class="text-primary" />
                            Obtain this token from @BotFather on Telegram.
                        </p>
                    </x-form-item>

                    <x-form-item label="Bot Username">
                        <x-input wire:model="telegramBotUsername" placeholder="@MyCompanyCRM_bot" />
                    </x-form-item>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800">
                        <x-button wire:click="testTelegramConnection" variant="default" size="sm">
                            <x-ph-icon name="check-circle" weight="bold" class="text-sm mr-1 text-sky-500" />
                            <span>Test & Verify Bot</span>
                        </x-button>

                        <x-button wire:click="saveSocialSettings" variant="solid" size="md">
                            <x-ph-icon name="check" weight="bold" class="text-sm mr-1" />
                            <span>Save Telegram Bot</span>
                        </x-button>
                    </div>
                </div>
            </x-card>

            <!-- Instagram & Facebook Messenger Card -->
            <x-card bodyClass="p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-3 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <div class="w-10 h-10 rounded-2xl bg-pink-50 dark:bg-pink-950/50 text-pink-500 flex items-center justify-center shrink-0">
                        <x-ph-icon name="instagram-logo" weight="fill" class="text-xl" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Instagram Direct & Messenger</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Omnichannel Meta messaging directly in your unified inbox.</p>
                    </div>
                </div>

                <div class="space-y-3 pt-1">
                    <div class="p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <x-ph-icon name="instagram-logo" weight="duotone" class="text-2xl text-pink-500 shrink-0" />
                            <div>
                                <span class="font-bold text-sm text-gray-800 dark:text-gray-200 block">Instagram Business Direct</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Sync customer direct DMs and story replies</span>
                            </div>
                        </div>
                        <x-switcher wire:model="instagramConnected" />
                    </div>

                    <div class="p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <x-ph-icon name="messenger-logo" weight="duotone" class="text-2xl text-blue-500 shrink-0" />
                            <div>
                                <span class="font-bold text-sm text-gray-800 dark:text-gray-200 block">Facebook Messenger Page</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Sync customer Facebook page inbox messages</span>
                            </div>
                        </div>
                        <x-switcher wire:model="messengerConnected" />
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button wire:click="saveSocialSettings" variant="solid" size="md">
                        <x-ph-icon name="check-circle" weight="bold" class="text-sm mr-1.5" />
                        <span>Save Social Integrations</span>
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
            <div class="flex items-start justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <x-ph-icon name="qr-code" weight="bold" class="text-xl" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Link WhatsApp Device</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Pair your phone via WhatsApp Web scanner or 8-digit pairing code.
                        </p>
                    </div>
                </div>
                <button wire:click="closePairModal" class="text-gray-400 hover:text-gray-500 p-1">
                    <x-ph-icon name="x" weight="bold" class="text-base" />
                </button>
            </div>

            <!-- Segment Switcher: QR Code vs Phone Code -->
            <div class="flex justify-center">
                <x-segment>
                    <x-segment-item :active="$pairModalTab === 'qr'" wire:click="setPairModalTab('qr')">
                        <x-ph-icon name="qr-code" weight="bold" class="text-sm mr-1" />
                        <span>QR Code Scan</span>
                    </x-segment-item>
                    <x-segment-item :active="$pairModalTab === 'code'" wire:click="setPairModalTab('code')">
                        <x-ph-icon name="hash" weight="bold" class="text-sm mr-1" />
                        <span>Pairing Code</span>
                    </x-segment-item>
                </x-segment>
            </div>

            @if ($pairModalTab === 'qr')
                <!-- Tab 1: QR Code Scanner Display -->
                <div class="flex flex-col items-center justify-center py-2 space-y-4">
                    <div class="relative p-4 rounded-3xl bg-white dark:bg-gray-800 border-2 border-dashed border-emerald-500/40 shadow-inner flex items-center justify-center">
                        <div class="w-56 h-56 bg-white p-3 rounded-2xl flex items-center justify-center relative overflow-hidden shadow-xs">
                            @if ($currentQrImage)
                                <img src="{{ $currentQrImage }}" alt="WhatsApp QR Code" class="w-full h-full object-contain" />
                            @else
                                <div class="flex flex-col items-center justify-center space-y-2 text-center">
                                    <x-ph-icon name="spinner" weight="bold" class="text-3xl text-emerald-500 animate-spin" />
                                    <span class="text-xs text-gray-500 font-medium">Generating Baileys session...</span>
                                </div>
                            @endif

                            @if (!$qrExpired)
                                <!-- Animated Scanning Laser Line -->
                                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent animate-pulse"></div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center space-y-2 max-w-sm">
                        @if ($qrExpired)
                            <div class="space-y-2">
                                <p class="text-xs font-bold text-rose-500 flex items-center justify-center gap-1">
                                    <x-ph-icon name="warning-circle" weight="bold" />
                                    QR Code has expired
                                </p>
                                <x-button wire:click="refreshQrCode" variant="default" size="sm">
                                    <x-ph-icon name="arrows-clockwise" weight="bold" class="text-xs mr-1 text-emerald-600" />
                                    <span>Refresh QR Code</span>
                                </x-button>
                            </div>
                        @else
                            <div class="flex items-center justify-center gap-2 text-xs text-gray-500">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                <span>Waiting for WhatsApp scan... (Expires in {{ max(0, $qrExpiresIn) }}s)</span>
                                <button type="button" wire:click="refreshQrCode" class="text-emerald-600 dark:text-emerald-400 hover:underline font-bold ml-1">Refresh</button>
                            </div>
                        @endif

                        <ol class="text-[11px] text-gray-500 dark:text-gray-400 space-y-1 text-left bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                            <li>1. Open <strong>WhatsApp</strong> on your mobile phone</li>
                            <li>2. Tap <strong>Settings</strong> &gt; <strong>Linked Devices</strong> &gt; <strong>Link a Device</strong></li>
                            <li>3. Point your camera at this QR code to scan and sync</li>
                        </ol>
                    </div>
                </div>
            @else
                <!-- Tab 2: 8-Digit Pairing Code -->
                <div class="space-y-4 py-2">
                    <x-form-item label="Your WhatsApp Mobile Number" :required="true">
                        <x-input wire:model="newDevicePhone" placeholder="+1 (555) 019-2834" class="font-mono text-sm" />
                    </x-form-item>

                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-800 text-center space-y-2 border border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 font-semibold block">Enter this code on your WhatsApp phone:</span>
                        <div class="font-mono text-3xl font-black text-primary tracking-widest bg-white dark:bg-gray-900 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 select-all shadow-inner">
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
            <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                <x-button wire:click="confirmPairing" variant="plain" size="sm" class="text-emerald-600 hover:text-emerald-700">
                    <x-ph-icon name="lightning" weight="bold" class="text-sm mr-1 text-emerald-500" />
                    <span>Instant Link / Demo Verify</span>
                </x-button>

                <div class="flex items-center gap-2">
                    <x-button wire:click="closePairModal" variant="default" size="md" type="button">
                        Cancel
                    </x-button>
                    <x-button wire:click="confirmPairing" variant="solid" size="md" type="button">
                        <x-ph-icon name="check-circle" weight="bold" class="text-base mr-1.5" />
                        <span>Confirm Pairing</span>
                    </x-button>
                </div>
            </div>
        </div>
    </x-modal>
</div>
