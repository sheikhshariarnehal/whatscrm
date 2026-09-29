<div class="p-2 sm:p-2.5 md:p-3 bg-[#f0f2f5] dark:bg-[#0c1317] min-h-[calc(100vh-4rem)] flex flex-col font-sans" 
     @if($showPairModal) wire:poll.2s="pollQrStatus" @endif
     x-data="{
         init() {
             const syncPath = () => {
                 const path = window.location.pathname;
                 const match = path.match(/\/devices\/([a-zA-Z0-9_-]+)/);
                 if (match && match[1]) {
                     const tab = match[1];
                     if (['qr', 'meta', 'warmer', 'social'].includes(tab) && tab !== @js($activeTab)) {
                         $wire.setTab(tab, false);
                     }
                 } else if (path === '/devices' || path === '/devices/') {
                     const currentTab = @js($activeTab);
                     window.history.replaceState({ tab: currentTab }, '', '/devices/' + currentTab);
                 }
             };

             if (window.location.pathname === '/devices' || window.location.pathname === '/devices/') {
                 window.history.replaceState({ tab: @js($activeTab) }, '', '/devices/' + @js($activeTab));
             }

             window.addEventListener('popstate', () => {
                 syncPath();
             });
         }
     }"
     @device-tab-changed.window="
         const tab = $event.detail.tab;
         const currentPath = window.location.pathname;
         const targetPath = '/devices/' + tab;
         if (currentPath !== targetPath) {
             window.history.pushState({ tab: tab }, '', targetPath);
         }
     ">
    <!-- Page Header & Top Bar -->
    <div class="px-2 pt-1 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#111b21] dark:text-[#e9edef] tracking-tight">WhatsApp Devices</h1>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <x-button wire:click="openPairModal" variant="solid" size="md" class="gap-2">
                <x-ph-icon name="qr-code" weight="bold" class="text-lg" />
                <span>Connect WhatsApp (QR)</span>
            </x-button>
        </div>
    </div>

    <!-- Alert Notifications / Feedback Messages -->
    @if (session()->has('message'))
        <div class="mb-3 mx-2 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
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
        <div class="mb-3 mx-2 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <x-ph-icon name="warning-circle" weight="fill" class="text-lg text-rose-500 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-800 dark:hover:text-rose-200 p-1">
                <x-ph-icon name="x" weight="bold" class="text-sm" />
            </button>
        </div>
    @endif

    <!-- Navigation Tabs Bar (Flush with Content Card) -->
    <div class="-mb-px relative z-10">
        <x-tabs>
            <x-tab-item href="{{ route('devices', 'qr') }}" wire:click.prevent="setTab('qr')" :active="$activeTab === 'qr'">
                <x-ph-icon name="qr-code" weight="duotone" class="text-lg shrink-0" />
                <span>Paired Sessions (QR)</span>
                <x-tag color="primary" class="ml-1 text-[10px] font-bold py-0.5 px-2">{{ count($pairedDevices) }}</x-tag>
            </x-tab-item>
            <x-tab-item href="{{ route('devices', 'meta') }}" wire:click.prevent="setTab('meta')" :active="$activeTab === 'meta'">
                <x-ph-icon name="cloud-check" weight="duotone" class="text-lg shrink-0" />
                <span>Meta Cloud API</span>
                @if ($metaConnected)
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse ml-1"></span>
                @endif
            </x-tab-item>
            <x-tab-item href="{{ route('devices', 'warmer') }}" wire:click.prevent="setTab('warmer')" :active="$activeTab === 'warmer'">
                <x-ph-icon name="flame" weight="duotone" class="text-lg shrink-0" />
                <span>Number Warmer Engine</span>
                @if ($activeWarmerCount > 0)
                    <x-tag color="amber" class="ml-1 text-[10px] font-bold py-0.5 px-2">{{ $activeWarmerCount }}</x-tag>
                @endif
            </x-tab-item>
            <x-tab-item href="{{ route('devices', 'social') }}" wire:click.prevent="setTab('social')" :active="$activeTab === 'social'">
                <x-ph-icon name="share-network" weight="duotone" class="text-lg shrink-0" />
                <span>Telegram & Social Channels</span>
                @if ($telegramConnected || $instagramConnected || $messengerConnected)
                    <x-tag color="emerald" class="ml-1 text-[10px] font-bold py-0.5 px-2">Active</x-tag>
                @endif
            </x-tab-item>
        </x-tabs>
    </div>

    <!-- Main Floating Card Container -->
    <div class="flex-1 flex flex-col rounded-2xl rounded-tl-none border border-[#d1d7db] dark:border-[#222e35] shadow-xs bg-white dark:bg-[#111b21] overflow-hidden">

        <div class="p-6 flex-1 overflow-y-auto bg-white dark:bg-[#111b21]">
            <!-- ============================================================== -->
            <!-- TAB 1: PAIRED SESSIONS (QR / BAILEYS ENGINE)                   -->
            <!-- ============================================================== -->
            @if ($activeTab === 'qr')
                <div wire:key="tab-panel-devices-qr" class="tab-pane">
        <div class="space-y-6">
            <!-- Top Telemetry Ribbon (Quiet, Unified Minimal Metrics) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <!-- Metric 1: Paired Lines -->
                <x-card bodyClass="p-4 flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Paired QR Sessions</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ count($pairedDevices) }}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ count(array_filter($pairedDevices, fn($d) => ($d['status'] ?? '') === 'connected')) }} connected</span>
                        </div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-gray-100/80 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 flex items-center justify-center shrink-0">
                        <x-ph-icon name="whatsapp-logo" weight="duotone" class="text-lg" />
                    </div>
                </x-card>

                <!-- Metric 2: Messages Dispatched Today -->
                <x-card bodyClass="p-4 flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Dispatched Today</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($totalMessagesToday) }}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">messages</span>
                        </div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-gray-100/80 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 flex items-center justify-center shrink-0">
                        <x-ph-icon name="paper-plane-tilt" weight="duotone" class="text-lg" />
                    </div>
                </x-card>

                <!-- Metric 3: Number Warmer -->
                <x-card bodyClass="p-4 flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Number Warmer</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $activeWarmerCount }}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">lines warming</span>
                        </div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-gray-100/80 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 flex items-center justify-center shrink-0">
                        <x-ph-icon name="flame" weight="duotone" class="text-lg" />
                    </div>
                </x-card>

                <!-- Metric 4: Gateway Engine -->
                <x-card bodyClass="p-4 flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Engine Gateway</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ $baileysOnline ? 'Engine Online' : 'Baileys Active' }}
                            </span>
                        </div>
                        <p class="text-[11px] font-mono text-gray-400 dark:text-gray-500 truncate">
                            Socket v6 Multi-Device
                        </p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-gray-100/80 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 flex items-center justify-center shrink-0">
                        <x-ph-icon name="cpu" weight="duotone" class="text-lg" />
                    </div>
                </x-card>
            </div>

            <!-- Device Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($pairedDevices as $device)
                    @php
                        $isConnected = ($device['status'] ?? 'connected') === 'connected';
                    @endphp
                    <x-card class="hover:border-gray-300 dark:hover:border-gray-700 transition-colors duration-150 flex flex-col justify-between h-full group" bodyClass="p-4 flex flex-col justify-between h-full space-y-3.5">
                        <div class="space-y-3">
                            <!-- Card Header: Device info & Status -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-9 h-9 rounded-xl {{ $isConnected ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }} flex items-center justify-center shrink-0">
                                        <x-ph-icon name="whatsapp-logo" weight="{{ $isConnected ? 'fill' : 'regular' }}" class="text-lg" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $device['name'] }}</h3>
                                        <p class="text-[11px] font-mono text-gray-400 dark:text-gray-500 mt-0.5 truncate">{{ $device['phone'] ?? 'Unlinked' }}</p>
                                    </div>
                                </div>

                                @if ($isConnected)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Connected
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                        Offline
                                    </span>
                                @endif
                            </div>

                            <!-- Daily Outbound Progress Bar (Calm & Restrained) -->
                            <div class="space-y-1 pt-0.5">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-gray-400 dark:text-gray-500 font-normal">Daily Outbound</span>
                                    <span class="font-mono text-gray-500 dark:text-gray-400">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $device['messages_today'] ?? 0 }}</span>
                                        <span class="text-gray-400">/ {{ $device['daily_limit'] ?? 80 }} msgs</span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-800 h-1 rounded-full overflow-hidden">
                                    <div class="bg-gray-400 dark:bg-gray-500 h-full rounded-full transition-all duration-300" style="width: {{ min(100, round((($device['messages_today'] ?? 0) / max(1, ($device['daily_limit'] ?? 80))) * 100)) }}%"></div>
                                </div>
                            </div>

                            <!-- Metadata & Warmer Switcher -->
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800/60 text-xs">
                                <div class="flex items-center gap-1 text-gray-400 dark:text-gray-500 text-[11px]">
                                    <x-ph-icon name="clock" weight="regular" class="text-xs" />
                                    <span>Synced {{ $device['last_sync'] ?? 'Just now' }}</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-normal text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                        <x-ph-icon name="flame" weight="{{ ($device['warmer_active'] ?? false) ? 'fill' : 'regular' }}" class="text-xs {{ ($device['warmer_active'] ?? false) ? 'text-amber-500' : 'text-gray-400' }}" />
                                        <span>Warmer</span>
                                    </span>
                                    <x-switcher :checked="$device['warmer_active'] ?? false" wire:click="toggleWarmerDevice('{{ $device['id'] }}')" />
                                </div>
                            </div>
                        </div>

                        <!-- Card Actions Footer (Quiet Actions) -->
                        <div class="pt-2.5 border-t border-gray-100 dark:border-gray-800/60 flex items-center justify-between gap-2">
                            <button type="button" wire:click="reconnectQrDevice('{{ $device['id'] }}')" class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors py-1 px-1.5 -ml-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-800">
                                <x-ph-icon name="arrows-clockwise" weight="regular" class="text-xs" />
                                <span>Reconnect</span>
                            </button>

                            <button type="button" wire:click="disconnectQrDevice('{{ $device['id'] }}')" wire:confirm="Disconnect this WhatsApp session? This will remove the link from WhatsCRM." class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500 hover:text-rose-500 dark:hover:text-rose-400 transition-colors py-1 px-1.5 -mr-1.5 rounded hover:bg-rose-50/50 dark:hover:bg-rose-950/20">
                                <x-ph-icon name="trash" weight="regular" class="text-xs" />
                                <span>Disconnect</span>
                            </button>
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
                </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 2: META CLOUD API CONFIGURATION                            -->
    <!-- ============================================================== -->
    @if ($activeTab === 'meta')
        <div wire:key="tab-panel-devices-meta" class="tab-pane">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Credentials Form & Diagnostics Sandbox -->
            <div class="lg:col-span-2 space-y-6">
                @if ($metaConnected)
                    <!-- Active Connected Account Telemetry Card -->
                    <x-card bodyClass="p-6 sm:p-7 space-y-5">
                        <!-- Header with Meta Verified Badge, Title, and Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-blue-50/70 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900/40">
                                    <x-ph-icon name="meta-logo" weight="fill" class="text-xl" />
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="font-semibold text-base sm:text-lg text-gray-900 dark:text-white tracking-tight truncate">{{ $metaVerifiedName }}</h2>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-blue-600 dark:text-blue-400 bg-blue-50/60 dark:bg-blue-950/40 px-2 py-0.5 rounded-full border border-blue-200/40 dark:border-blue-800/40">
                                            <x-ph-icon name="seal-check" weight="fill" class="text-xs" />
                                            Meta Verified
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100/80 dark:bg-gray-800/80 px-2 py-0.5 rounded-full border border-gray-200/50 dark:border-gray-700/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Active Channel
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                                        <span class="font-medium text-gray-800 dark:text-gray-200 font-mono">{{ $display_phone_number ?: 'Active Phone Line' }}</span>
                                        <span class="text-gray-300 dark:text-gray-600">&bull;</span>
                                        <span class="font-mono text-gray-400 dark:text-gray-500 text-[11px]">WABA ID: {{ $waba_id }}</span>
                                        <span class="text-gray-300 dark:text-gray-600">&bull;</span>
                                        <span class="font-mono text-gray-400 dark:text-gray-500 text-[11px]">Phone ID: {{ $phone_number_id }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 shrink-0">
                                <x-button wire:click="refreshMetaStatus" variant="default" size="sm" title="Sync real-time account status from Meta">
                                    <x-ph-icon name="arrows-clockwise" weight="bold" class="text-sm mr-1.5 text-gray-500 dark:text-gray-400" />
                                    <span>Refresh Status</span>
                                </x-button>

                                <x-button wire:click="syncTemplates" variant="default" size="sm" title="Fetch approved HSM templates from Meta Business Manager">
                                    <x-ph-icon name="cloud-arrow-down" weight="bold" class="text-sm mr-1.5 text-gray-500 dark:text-gray-400" />
                                    <span>Sync Templates</span>
                                </x-button>

                                <x-button wire:click="openEditMetaModal" variant="default" size="sm" title="Update Graph API keys or tokens">
                                    <x-ph-icon name="gear-six" weight="bold" class="text-sm mr-1.5 text-gray-500 dark:text-gray-400" />
                                    <span>Edit</span>
                                </x-button>

                                <x-button wire:click="disconnect" wire:confirm="Disconnect this Meta WhatsApp Cloud API account? Inbound webhooks will stop receiving messages and broadcast campaigns on this line will be paused." variant="plain" size="sm" class="text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                                    <x-ph-icon name="link-break" weight="bold" class="text-sm mr-1" />
                                    <span>Disconnect</span>
                                </x-button>
                            </div>
                        </div>

                        <!-- Live Telemetry Row (Single-surface, zero nesting) -->
                        <div class="pt-5 border-t border-gray-100 dark:border-gray-800 grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                            <!-- Quality Rating -->
                            <div>
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400 block">Quality Rating</span>
                                <div class="flex items-center gap-1.5 mt-1">
                                    @if(strtoupper($metaQualityRating) === 'GREEN')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200">High Quality</span>
                                    @elseif(strtoupper($metaQualityRating) === 'YELLOW')
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                        <span class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200">Medium (Yellow)</span>
                                    @elseif(strtoupper($metaQualityRating) === 'RED')
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-xs sm:text-sm font-medium text-rose-600 dark:text-rose-400">Low Quality</span>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200">Approved Live</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Messaging Tier -->
                            <div>
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400 block">Messaging Limit</span>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <x-ph-icon name="lightning" weight="fill" class="text-amber-500 text-sm shrink-0" />
                                    <span class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ str_replace(['TIER_', '_'], ['', ' '], $metaMessagingLimit) }} / 24h
                                    </span>
                                </div>
                            </div>

                            <!-- Connection Mode -->
                            <div>
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400 block">Connection Mode</span>
                                <div class="flex items-center gap-1.5 mt-1">
                                    @if($metaIsOnBizApp)
                                        <x-ph-icon name="device-mobile" weight="bold" class="text-gray-500 dark:text-gray-400 text-sm shrink-0" />
                                        <span class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200">SMB Coexistence</span>
                                    @else
                                        <x-ph-icon name="cloud-check" weight="bold" class="text-gray-500 dark:text-gray-400 text-sm shrink-0" />
                                        <span class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200">Direct Cloud API</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Webhook Status -->
                            <div>
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400 block">Webhook Ingestion</span>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $metaWebhookSubscribed ? 'Active & Subscribed' : 'Listening (v20.0)' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </x-card>
                @else
                    <!-- Credentials Configuration Form Card (When Not Connected) -->
                    <x-card bodyClass="p-6 sm:p-8 space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <x-ph-icon name="meta-logo" weight="fill" class="text-xl" />
                                </div>
                                <div>
                                    <h2 class="font-bold text-base text-gray-900 dark:text-white tracking-tight">Connect WhatsApp Cloud API</h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Enter your credentials from Meta Developer Portal (developers.facebook.com &gt; Your App &gt; WhatsApp &gt; API Setup).</p>
                                </div>
                            </div>
                            <x-tag color="gray" class="text-[10px]">Not Connected</x-tag>
                        </div>

                        <form wire:submit.prevent="saveCredentials" class="space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-form-item label="Phone Number ID" :required="true" :error="$errors->first('phone_number_id')">
                                    <x-input wire:model="phone_number_id" :invalid="$errors->has('phone_number_id')" placeholder="e.g. 109876543210987" class="font-mono text-xs" />
                                    <p class="text-[11px] text-gray-400 mt-1">Found in Step 1 under WhatsApp &gt; API Setup.</p>
                                </x-form-item>

                                <x-form-item label="WhatsApp Business Account ID (WABA ID)" :required="true" :error="$errors->first('waba_id')">
                                    <x-input wire:model="waba_id" :invalid="$errors->has('waba_id')" placeholder="e.g. 987654321098765" class="font-mono text-xs" />
                                    <p class="text-[11px] text-gray-400 mt-1">Found in Step 1 under WhatsApp &gt; API Setup.</p>
                                </x-form-item>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-form-item label="Display Phone Number (Optional — auto-fetched)">
                                    <x-input wire:model="display_phone_number" placeholder="e.g. +880 1712-345678" />
                                </x-form-item>

                                <x-form-item label="Meta App ID (Optional)">
                                    <x-input wire:model="app_id" placeholder="e.g. 781923401928374" class="font-mono text-xs" />
                                </x-form-item>
                            </div>

                            <x-form-item label="Permanent System User Access Token" :required="true" :error="$errors->first('access_token')">
                                <div x-data="{ showToken: false }" class="relative">
                                    <x-input x-bind:type="showToken ? 'text' : 'password'" wire:model="access_token" :invalid="$errors->has('access_token')" placeholder="{{ $credential ? '••••••••••••••••••••••••••••••••' : 'EAAG...' }}" class="font-mono text-xs pr-10" />
                                    <button type="button" @click="showToken = !showToken" title="Toggle token visibility" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                        <x-ph-icon x-show="!showToken" name="eye" weight="bold" class="text-base" />
                                        <x-ph-icon x-show="showToken" name="eye-slash" weight="bold" class="text-base" style="display:none;" />
                                    </button>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1.5">
                                    <x-ph-icon name="shield-check" weight="duotone" class="text-sm text-emerald-500" />
                                    Must have <code>whatsapp_business_messaging</code> and <code>whatsapp_business_management</code> permissions. Stored securely with AES-256 encryption.
                                </p>
                            </x-form-item>

                            <x-form-item label="Webhook Verify Token" :required="true" :error="$errors->first('verify_token')">
                                <x-input wire:model="verify_token" :invalid="$errors->has('verify_token')" class="font-mono text-xs" />
                                <p class="text-[11px] text-gray-400 mt-1">A custom secret passphrase configured in your Meta Developer Webhook settings.</p>
                            </x-form-item>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                                <div></div>
                                <x-button type="submit" variant="solid" size="md">
                                    <x-ph-icon name="check-circle" weight="bold" class="text-base mr-1.5" />
                                    <span>Verify & Connect Meta Account</span>
                                </x-button>
                            </div>
                        </form>
                    </x-card>
                @endif

                <!-- Live Diagnostics & Test Sandbox -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Outbound Message Delivery Test -->
                    <x-card bodyClass="p-6 space-y-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 flex items-center justify-center shrink-0">
                                <x-ph-icon name="paper-plane-tilt" weight="bold" class="text-sm" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-white tracking-tight">Send Test Message</h3>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Verify outbound message delivery directly via Meta Cloud API.</p>
                            </div>
                        </div>

                        @if ($test_status)
                            <div class="p-3 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-medium border border-emerald-200/60 dark:border-emerald-800/60 flex items-center gap-2">
                                <x-ph-icon name="check-circle" weight="fill" class="text-base text-emerald-500 shrink-0" />
                                <span>{{ $test_status }}</span>
                            </div>
                        @endif

                        @if ($test_error)
                            <div class="p-3 rounded-xl bg-rose-50/80 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-xs font-medium border border-rose-200/60 dark:border-rose-800/60 flex items-center gap-2">
                                <x-ph-icon name="warning-circle" weight="fill" class="text-base text-rose-500 shrink-0" />
                                <span>{{ $test_error }}</span>
                            </div>
                        @endif

                        <form wire:submit.prevent="sendTestMessage" class="space-y-3">
                            <x-form-item label="Recipient Phone Number" :required="true">
                                <x-input wire:model="test_phone_number" placeholder="e.g. +880 1712-345678" class="font-mono text-xs" />
                            </x-form-item>

                            <x-button type="submit" wire:loading.attr="disabled" variant="default" size="sm" class="w-full">
                                <span wire:loading.remove class="flex items-center justify-center gap-1.5">
                                    <x-ph-icon name="paper-plane-tilt" weight="bold" class="text-sm text-gray-600 dark:text-gray-300" />
                                    <span>Send Test Message</span>
                                </span>
                                <span wire:loading class="flex items-center justify-center gap-1.5">
                                    <x-ph-icon name="spinner" weight="bold" class="text-sm animate-spin" />
                                    <span>Sending via Meta Cloud API...</span>
                                </span>
                            </x-button>
                        </form>
                    </x-card>

                    <!-- Webhook Ingestion & Simulation Sandbox -->
                    <x-card bodyClass="p-6 space-y-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 flex items-center justify-center shrink-0">
                                <x-ph-icon name="plugs-connected" weight="bold" class="text-sm" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-white tracking-tight">Webhook Diagnostics & Testing</h3>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Simulate incoming customer messages and sync Meta template assets.</p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-1">
                            <div class="space-y-1">
                                <x-button wire:click="simulateInboundWebhook" variant="default" size="sm" class="w-full flex items-center justify-center gap-2">
                                    <x-ph-icon name="chat-circle-dots" weight="bold" class="text-sm text-gray-500 dark:text-gray-400" />
                                    <span>Simulate Inbound Lead Message</span>
                                </x-button>
                                <p class="text-[10px] text-gray-400 text-center">Triggers a mock customer chat to test inbox routing and automations.</p>
                            </div>

                            <div class="space-y-1 pt-1 border-t border-gray-100 dark:border-gray-800">
                                <x-button wire:click="syncTemplates" variant="default" size="sm" class="w-full flex items-center justify-center gap-2">
                                    <x-ph-icon name="arrows-clockwise" weight="bold" class="text-sm text-gray-500 dark:text-gray-400" />
                                    <span>Re-sync Meta Templates</span>
                                </x-button>
                                <p class="text-[10px] text-gray-400 text-center">Pulls latest approved templates from your Meta Business Manager.</p>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>

            <!-- Right Col: Meta Webhook Setup Instructions -->
            <div class="space-y-6">
                <x-card bodyClass="p-6 space-y-5">
                    <div class="flex items-center gap-2.5 pb-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <h3 class="font-semibold text-sm text-gray-900 dark:text-white">Meta Webhook Configuration</h3>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Configure these two parameters in your Meta Developer Portal under <strong>WhatsApp &gt; Configuration</strong> to route customer replies to WhatsCRM:
                    </p>

                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 font-semibold mb-1">Callback URL</span>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 font-mono text-[11px] text-gray-800 dark:text-gray-200 break-all select-all border border-gray-200 dark:border-gray-700 flex items-center justify-between gap-2">
                                <span>{{ $webhookCallbackUrl }}</span>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $webhookCallbackUrl }}'); alert('Callback URL copied!')" class="text-primary hover:text-primary-dark font-bold text-xs shrink-0 flex items-center gap-1 p-1">
                                    <x-ph-icon name="copy" weight="bold" />
                                    <span>Copy URL</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 font-semibold mb-1">Verify Token</span>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 font-mono text-[11px] text-gray-800 dark:text-gray-200 select-all border border-gray-200 dark:border-gray-700 flex items-center justify-between gap-2">
                                <span>{{ $verify_token }}</span>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $verify_token }}'); alert('Verify token copied!')" class="text-primary hover:text-primary-dark font-bold text-xs shrink-0 flex items-center gap-1 p-1">
                                    <x-ph-icon name="copy" weight="bold" />
                                    <span>Copy Token</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 font-semibold mb-1.5">Required Webhook Field Subscriptions</span>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 text-[11px] text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 space-y-1.5">
                                <div class="flex items-start gap-2">
                                    <x-ph-icon name="check" weight="bold" class="text-emerald-500 mt-0.5 shrink-0" />
                                    <div>
                                        <code class="font-bold text-primary font-mono">messages</code>
                                        <span class="text-gray-400 block text-[10px]">Incoming chats, media attachments & delivery receipts</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-2 pt-1 border-t border-gray-200/60 dark:border-gray-700/60">
                                    <x-ph-icon name="check" weight="bold" class="text-emerald-500 mt-0.5 shrink-0" />
                                    <div>
                                        <code class="font-bold text-primary font-mono">message_template_status_update</code>
                                        <span class="text-gray-400 block text-[10px]">Real-time template approval and rejection updates</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step-by-step Quick Setup Guide -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800 space-y-2 text-xs">
                        <span class="font-bold text-gray-800 dark:text-gray-200 block">Setup Instructions:</span>
                        <ol class="space-y-1.5 text-gray-500 dark:text-gray-400 text-[11px] pl-4 list-decimal">
                            <li>Go to <a href="https://developers.facebook.com" target="_blank" class="text-primary hover:underline font-semibold">developers.facebook.com</a> &gt; open your App.</li>
                            <li>In the sidebar, navigate to <strong>WhatsApp &gt; Configuration</strong>.</li>
                            <li>Under <strong>Webhook</strong>, click <strong>Edit</strong>, paste the <strong>Callback URL</strong> and <strong>Verify Token</strong> above, then click <strong>Verify and Save</strong>.</li>
                            <li>Under <strong>Webhook fields</strong>, click <strong>Manage</strong> and check both <code>messages</code> and <code>message_template_status_update</code>.</li>
                        </ol>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 3: NUMBER WARMER ENGINE                                    -->
    <!-- ============================================================== -->
    @if ($activeTab === 'warmer')
        <div wire:key="tab-panel-devices-warmer" class="tab-pane">
            <div class="space-y-6">
                <!-- Stage Progression Metrics Banner -->
                <x-card bodyClass="p-6 sm:p-7 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-100 dark:border-amber-900/40">
                                <x-ph-icon name="flame" weight="fill" class="text-xl" />
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="font-semibold text-base sm:text-lg text-gray-900 dark:text-white tracking-tight truncate">Number Warmer Engine</h2>
                                    @if ($warmerEngineRunning)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full border border-emerald-200/50 dark:border-emerald-800/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Running
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 px-2 py-0.5 rounded-full border border-amber-200/50 dark:border-amber-800/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Paused
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Automated peer-to-peer dialogues between paired lines to build sender reputation and prevent bans.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <x-button wire:click="toggleEngineState" variant="{{ $warmerEngineRunning ? 'default' : 'solid' }}" size="sm" class="{{ $warmerEngineRunning ? 'text-amber-600 dark:text-amber-400' : 'bg-emerald-600 text-white' }}">
                                <x-ph-icon name="{{ $warmerEngineRunning ? 'pause' : 'play' }}" weight="bold" class="text-sm mr-1.5" />
                                <span>{{ $warmerEngineRunning ? 'Pause Engine' : 'Start Warmer' }}</span>
                            </x-button>

                            <x-button wire:click="runInstantWarmupTest" variant="default" size="sm" title="Simulate an instant dialogue exchange between paired lines">
                                <x-ph-icon name="lightning" weight="bold" class="text-sm mr-1.5 text-gray-500 dark:text-gray-400" />
                                <span>Simulate Dialogue</span>
                            </x-button>
                        </div>
                    </div>

                    <!-- 4-Stage Warmup Roadmap (Single-surface, zero nesting) -->
                    <div class="pt-5 border-t border-gray-100 dark:border-gray-800 grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                        <div>
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400 block">Stage 1 · Cold Start</span>
                            <div class="flex items-baseline gap-1 mt-1">
                                <span class="text-base font-semibold text-gray-900 dark:text-white">5</span>
                                <span class="text-xs text-gray-400">msgs/day</span>
                            </div>
                            <span class="text-[11px] text-gray-400 dark:text-gray-500 block mt-0.5">Day 1–3 · Initial handshake</span>
                        </div>

                        <div>
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400 block">Stage 2 · Warming</span>
                            <div class="flex items-baseline gap-1 mt-1">
                                <span class="text-base font-semibold text-gray-900 dark:text-white">15</span>
                                <span class="text-xs text-gray-400">msgs/day</span>
                            </div>
                            <span class="text-[11px] text-gray-400 dark:text-gray-500 block mt-0.5">Day 4–7 · Conversational flow</span>
                        </div>

                        <div>
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400 block">Stage 3 · Warm</span>
                            <div class="flex items-baseline gap-1 mt-1">
                                <span class="text-base font-semibold text-gray-900 dark:text-white">50</span>
                                <span class="text-xs text-gray-400">msgs/day</span>
                            </div>
                            <span class="text-[11px] text-gray-400 dark:text-gray-500 block mt-0.5">Day 8–14 · Simulated inquiries</span>
                        </div>

                        <div>
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400 block">Stage 4 · Broadcast Ready</span>
                            <div class="flex items-baseline gap-1 mt-1">
                                <span class="text-base font-semibold text-gray-900 dark:text-white">150+</span>
                                <span class="text-xs text-gray-400">msgs/day</span>
                            </div>
                            <span class="text-[11px] text-gray-400 dark:text-gray-500 block mt-0.5">Day 15+ · Outbound campaigns</span>
                        </div>
                    </div>
                </x-card>

                <!-- Configuration & Active Matrix (Equal Height Grid) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                    <!-- Warmup Rules Form Card -->
                    <x-card class="h-full flex flex-col" bodyClass="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white tracking-tight">Warmup Intervals & Throttling</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Control inter-message sleep jitter to emulate human chat patterns.</p>
                            </div>

                            <form id="warmer-settings-form" wire:submit.prevent="saveWarmerSettings" class="space-y-4">
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
                            </form>
                        </div>

                        <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-800 mt-auto">
                            <x-button type="submit" form="warmer-settings-form" variant="solid" size="md">
                                <x-ph-icon name="check-circle" weight="bold" class="text-base mr-1.5" />
                                <span>Save Preferences</span>
                            </x-button>
                        </div>
                    </x-card>

                    <!-- Active Warmup Pairing Matrix Card (Scrollable & Equal Height) -->
                    <x-card class="h-full flex flex-col" bodyClass="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div class="flex-1 flex flex-col min-h-0">
                            <div class="border-b border-gray-100 dark:border-gray-800 pb-3 mb-3 flex items-center justify-between shrink-0">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white tracking-tight">Active Device Warmup Matrix</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Paired lines participating in automated peer warming.</p>
                                </div>
                                <span class="text-[11px] font-mono font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md shrink-0">
                                    {{ count($pairedDevices) }} {{ count($pairedDevices) === 1 ? 'Line' : 'Lines' }}
                                </span>
                            </div>

                            <!-- Scrollable Devices List -->
                            <div class="flex-1 overflow-y-auto max-h-72 sm:max-h-80 pr-1 divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($pairedDevices as $d)
                                    <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-lg {{ ($d['warmer_active'] ?? false) ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }} flex items-center justify-center shrink-0">
                                                <x-ph-icon name="flame" weight="fill" class="text-base" />
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $d['name'] }}</h4>
                                                <p class="text-[11px] font-mono text-gray-500 dark:text-gray-400 truncate">{{ $d['phone'] ?: 'No phone number' }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 shrink-0">
                                            @if ($d['warmer_active'] ?? false)
                                                <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    <span>Active</span>
                                                </span>
                                            @else
                                                <span class="text-[11px] text-gray-400 font-normal">Idle</span>
                                            @endif
                                            <x-switcher :checked="$d['warmer_active'] ?? false" wire:click="toggleWarmerDevice('{{ $d['id'] }}')" />
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400 text-center py-6">No paired devices found to participate in warming.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Live Dialogue Activity Log -->
                        @if (count($warmerActivityLogs) > 0)
                            <div class="pt-3 border-t border-gray-100 dark:border-gray-800 space-y-1.5 shrink-0">
                                <span class="text-[11px] font-medium text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                    <x-ph-icon name="terminal-window" weight="bold" class="text-xs text-gray-500" />
                                    <span>Recent Dialogue Turns</span>
                                </span>
                                <div class="space-y-1 max-h-28 overflow-y-auto pr-1">
                                    @foreach ($warmerActivityLogs as $log)
                                        <div class="p-1.5 rounded-lg bg-gray-50 dark:bg-gray-800/60 text-[10px] flex items-center justify-between gap-2">
                                            <div class="truncate">
                                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $log['from'] }} &rarr; {{ $log['to'] }}:</span>
                                                <span class="text-gray-500">"{{ $log['message'] }}"</span>
                                            </div>
                                            <span class="font-mono text-[9px] text-gray-400 shrink-0">{{ $log['time'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </x-card>
                </div>

                <!-- Dialogue Script Library Manager Card -->
                <x-card bodyClass="p-6 space-y-5">
                    <div class="border-b border-gray-100 dark:border-gray-800 pb-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white tracking-tight">Dialogue Script Manager</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Customize natural conversational turns exchanged between warming lines.</p>
                    </div>

                    <!-- Add new script turn -->
                    <form wire:submit.prevent="addScriptMessage" class="flex gap-2">
                        <x-input wire:model="newScriptText" placeholder="Add a new dialogue line (e.g. 'Hey, did you get a chance to review the proposal?')" class="text-xs flex-1" />
                        <x-button type="submit" variant="solid" size="md" class="shrink-0">
                            <x-ph-icon name="plus" weight="bold" class="text-sm mr-1" />
                            <span>Add Turn</span>
                        </x-button>
                    </form>

                    <!-- Script turns list chips -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        @foreach ($warmerScripts as $s)
                            <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 flex items-center justify-between gap-2">
                                <span class="text-xs text-gray-700 dark:text-gray-300 truncate font-normal">"{{ $s['message'] }}"</span>
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
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- TAB 4: TELEGRAM & SOCIAL CHANNELS                              -->
    <!-- ============================================================== -->
    @if ($activeTab === 'social')
        <div wire:key="tab-panel-devices-social" class="tab-pane">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                <!-- Telegram Bot API Card -->
                <x-card class="h-full flex flex-col" bodyClass="p-6 flex-1 flex flex-col justify-between space-y-5">
                    <div>
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 flex items-center justify-center shrink-0 border border-gray-200/60 dark:border-gray-700/60">
                                    <x-ph-icon name="paper-plane-tilt" weight="duotone" class="text-xl text-gray-700 dark:text-gray-300" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white tracking-tight">Telegram Bot API</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Receive and respond to Telegram customer messages.</p>
                                </div>
                            </div>

                            @if ($telegramConnected)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100/80 dark:bg-gray-800/80 px-2 py-0.5 rounded-full border border-gray-200/50 dark:border-gray-700/50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Connected
                                </span>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <x-form-item label="Telegram Bot Token" :required="true">
                                <x-input wire:model="telegramBotToken" placeholder="123456789:ABCdefGHIjklmNOPqrsTUVwxyz" class="font-mono text-xs" />
                                <p class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1">
                                    <x-ph-icon name="info" weight="duotone" class="text-gray-400" />
                                    <span>Obtain this token from @BotFather on Telegram.</span>
                                </p>
                            </x-form-item>

                            <x-form-item label="Bot Username">
                                <x-input wire:model="telegramBotUsername" placeholder="@MyCompanyCRM_bot" />
                            </x-form-item>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800 mt-auto">
                        <x-button wire:click="testTelegramConnection" variant="default" size="sm">
                            <x-ph-icon name="check-circle" weight="duotone" class="text-sm mr-1 text-gray-500 dark:text-gray-400" />
                            <span>Test & Verify</span>
                        </x-button>

                        <x-button wire:click="saveSocialSettings" variant="solid" size="md">
                            <x-ph-icon name="check" weight="bold" class="text-sm mr-1" />
                            <span>Save Bot</span>
                        </x-button>
                    </div>
                </x-card>

                <!-- Instagram & Facebook Messenger Card -->
                <x-card class="h-full flex flex-col" bodyClass="p-6 flex-1 flex flex-col justify-between space-y-5">
                    <div>
                        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 flex items-center justify-center shrink-0 border border-gray-200/60 dark:border-gray-700/60">
                                <x-ph-icon name="share-network" weight="duotone" class="text-xl text-gray-700 dark:text-gray-300" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white tracking-tight">Instagram Direct & Messenger</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Omnichannel Meta messaging directly in your unified inbox.</p>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 flex items-center justify-center shrink-0 border border-gray-200/50 dark:border-gray-700/50">
                                        <x-ph-icon name="instagram-logo" weight="duotone" class="text-lg text-gray-700 dark:text-gray-300" />
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-medium text-xs text-gray-900 dark:text-white block">Instagram Direct</span>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400 block truncate">Sync customer DMs and story replies</span>
                                    </div>
                                </div>
                                <x-switcher wire:model="instagramConnected" />
                            </div>

                            <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 flex items-center justify-center shrink-0 border border-gray-200/50 dark:border-gray-700/50">
                                        <x-ph-icon name="messenger-logo" weight="duotone" class="text-lg text-gray-700 dark:text-gray-300" />
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-medium text-xs text-gray-900 dark:text-white block">Facebook Messenger</span>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400 block truncate">Sync Facebook page inbox messages</span>
                                    </div>
                                </div>
                                <x-switcher wire:model="messengerConnected" />
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    @endif
    </div>
</div>

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

    <!-- Modal 2: Edit Meta Cloud API Credentials Modal -->
    <x-modal name="edit-meta-modal" :show="$showEditMetaModal" maxWidth="lg">
        <div class="p-6 space-y-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <x-ph-icon name="meta-logo" weight="fill" class="text-xl" />
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-white">Configure Meta Credentials</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update Graph API access keys or verify token</p>
                    </div>
                </div>
                <button type="button" wire:click="closeEditMetaModal" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <x-ph-icon name="x" weight="bold" class="text-lg" />
                </button>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="saveCredentials" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <x-form-item label="Phone Number ID" :required="true" :error="$errors->first('phone_number_id')">
                        <x-input wire:model="phone_number_id" :invalid="$errors->has('phone_number_id')" placeholder="e.g. 109876543210987" class="font-mono text-xs" />
                    </x-form-item>

                    <x-form-item label="WABA ID" :required="true" :error="$errors->first('waba_id')">
                        <x-input wire:model="waba_id" :invalid="$errors->has('waba_id')" placeholder="e.g. 987654321098765" class="font-mono text-xs" />
                    </x-form-item>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <x-form-item label="Display Phone Number (Optional)">
                        <x-input wire:model="display_phone_number" placeholder="e.g. +1 (555) 019-2834" class="text-xs" />
                    </x-form-item>

                    <x-form-item label="Meta App ID (Optional)">
                        <x-input wire:model="app_id" placeholder="e.g. 781923401928374" class="font-mono text-xs" />
                    </x-form-item>
                </div>

                <x-form-item label="Permanent System User Access Token" :required="!$credential" :error="$errors->first('access_token')">
                    <div x-data="{ showToken: false }" class="relative">
                        <x-input x-bind:type="showToken ? 'text' : 'password'" wire:model="access_token" :invalid="$errors->has('access_token')" placeholder="{{ $credential ? '•••••••••••••••• (Leave blank to keep current)' : 'EAAG...' }}" class="font-mono text-xs pr-10" />
                        <button type="button" @click="showToken = !showToken" title="Toggle token visibility" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-ph-icon x-show="!showToken" name="eye" weight="bold" class="text-base" />
                            <x-ph-icon x-show="showToken" name="eye-slash" weight="bold" class="text-base" style="display:none;" />
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Leave empty to preserve your current encrypted token.</p>
                </x-form-item>

                <x-form-item label="Webhook Verify Token" :required="true" :error="$errors->first('verify_token')">
                    <x-input wire:model="verify_token" :invalid="$errors->has('verify_token')" class="font-mono text-xs" />
                </x-form-item>

                <!-- Modal Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="closeEditMetaModal" variant="default" size="md">
                        Cancel
                    </x-button>

                    <x-button type="submit" variant="solid" size="md">
                        <x-ph-icon name="check-circle" weight="bold" class="text-base mr-1.5" />
                        <span>Verify & Save Changes</span>
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
