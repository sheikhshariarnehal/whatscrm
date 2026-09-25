<div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">WhatsApp Business API</h1>
                @if ($credential && $credential->isConnected())
                    <x-tag color="emerald" prefix class="font-bold">Cloud API Live</x-tag>
                @else
                    <x-tag color="amber" prefix class="font-bold">Setup Required</x-tag>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Connect and manage your official Meta Cloud API numbers, webhooks, and phone credentials</p>
        </div>

        @if ($credential && $credential->isConnected())
            <div class="flex items-center gap-2">
                <x-button wire:click="disconnect" wire:confirm="Are you sure you want to disconnect this number?" variant="plain" size="sm" class="text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                    Disconnect Number
                </x-button>
            </div>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Credentials Form & Test Message -->
        <div class="lg:col-span-2 space-y-6">
            <x-card bodyClass="p-6 space-y-5">
                <div>
                    <h2 class="font-bold text-base text-gray-900 dark:text-white tracking-tight">Meta Cloud API Credentials</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Obtain these credentials from your Meta Developer Portal under WhatsApp &gt; API Setup.</p>
                </div>

                <form wire:submit.prevent="saveCredentials" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Phone Number ID *</label>
                            <input wire:model="phone_number_id" 
                                   type="text" 
                                   placeholder="e.g. 109876543210987"
                                   class="input input-sm font-mono">
                            @error('phone_number_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">WABA ID (Account ID) *</label>
                            <input wire:model="waba_id" 
                                   type="text" 
                                   placeholder="e.g. 987654321098765"
                                   class="input input-sm font-mono">
                            @error('waba_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Display Phone Number</label>
                        <input wire:model="display_phone_number" 
                               type="text" 
                               placeholder="e.g. +1 555-019-2834"
                               class="input input-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Permanent Access Token *</label>
                        <input wire:model="access_token" 
                               type="password" 
                               placeholder="{{ $credential ? '••••••••••••••••••••••••••••••••' : 'EAAG...' }}"
                               class="input input-sm font-mono">
                        <p class="text-[11px] text-gray-400 mt-1">Stored with 256-bit AES database encryption.</p>
                        @error('access_token') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Webhook Verify Token *</label>
                        <input wire:model="verify_token" 
                               type="text" 
                               class="input input-sm font-mono">
                        @error('verify_token') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 flex justify-end">
                        <x-button type="submit" variant="solid" size="sm">
                            Save Credentials
                        </x-button>
                    </div>
                </form>
            </x-card>

            <!-- Send Test Message Card -->
            <x-card bodyClass="p-6 space-y-4">
                <div>
                    <h2 class="font-bold text-base text-gray-900 dark:text-white tracking-tight">Verify Connection (Live WhatsApp Ping)</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Send a real-time message to any WhatsApp number to verify API delivery.</p>
                </div>

                @if ($test_status)
                    <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold border border-emerald-200 dark:border-emerald-800">
                        {{ $test_status }}
                    </div>
                @endif

                @if ($test_error)
                    <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-xs font-semibold border border-rose-200 dark:border-rose-800">
                        {{ $test_error }}
                    </div>
                @endif

                <form wire:submit.prevent="sendTestMessage" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Destination Phone Number</label>
                        <input wire:model="test_phone_number" 
                               type="text" 
                               placeholder="+15551234567 (with country code)" 
                               class="input input-sm font-mono">
                        @error('test_phone_number') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end">
                        <x-button type="submit" 
                                  wire:loading.attr="disabled"
                                  variant="default" 
                                  size="sm">
                            <span wire:loading.remove>Send Test Ping</span>
                            <span wire:loading>Sending...</span>
                            <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Right Col: Meta Webhook Setup Instructions -->
        <div class="space-y-6">
            <x-card bodyClass="p-5 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
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
            <x-card bodyClass="p-5 space-y-3">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white">Cloud API Capabilities</h3>
                <div class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
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
</div>
