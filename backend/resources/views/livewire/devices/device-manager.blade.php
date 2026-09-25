<div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">WhatsApp Business Account</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Connect and manage your official Meta Cloud API numbers and webhooks</p>
        </div>

        @if ($credential && $credential->isConnected())
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Connected & Active</span>
                </span>
                <button wire:click="disconnect" wire:confirm="Are you sure you want to disconnect this number?" class="px-3 py-1 rounded-lg text-xs font-medium text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20">
                    Disconnect
                </button>
            </div>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Not Connected</span>
            </span>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Credentials Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-5">
                <div>
                    <h2 class="font-bold text-base text-gray-900 dark:text-white">Meta Cloud API Credentials</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Obtain these credentials from your Meta Developer Portal under WhatsApp &gt; API Setup.</p>
                </div>

                <form wire:submit.prevent="saveCredentials" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Phone Number ID *</label>
                            <input wire:model="phone_number_id" 
                                   type="text" 
                                   placeholder="e.g. 109876543210987"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            @error('phone_number_id') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">WhatsApp Business Account ID (WABA ID) *</label>
                            <input wire:model="waba_id" 
                                   type="text" 
                                   placeholder="e.g. 987654321098765"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            @error('waba_id') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Display Phone Number</label>
                        <input wire:model="display_phone_number" 
                               type="text" 
                               placeholder="e.g. +1 555-019-2834"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Permanent System User Access Token *</label>
                        <input wire:model="access_token" 
                               type="password" 
                               placeholder="{{ $credential ? 'Leave blank to keep existing token' : 'EAAG...' }}"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <p class="text-[11px] text-gray-400 mt-1">Stored with 256-bit AES database encryption.</p>
                        @error('access_token') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Webhook Verify Token *</label>
                        <input wire:model="verify_token" 
                               type="text" 
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @error('verify_token') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-deep text-white font-semibold text-xs shadow-sm shadow-primary/20 transition-colors">
                            Save Credentials
                        </button>
                    </div>
                </form>
            </div>

            <!-- Send Test Message Card -->
            <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-4">
                <div>
                    <h2 class="font-bold text-base text-gray-900 dark:text-white">Verify Connection (Live WhatsApp Ping)</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Send a real-time message to any WhatsApp number to verify API delivery.</p>
                </div>

                @if ($test_status)
                    <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-medium border border-emerald-200 dark:border-emerald-800">
                        {{ $test_status }}
                    </div>
                @endif

                @if ($test_error)
                    <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-xs font-medium border border-rose-200 dark:border-rose-800">
                        {{ $test_error }}
                    </div>
                @endif

                <form wire:submit.prevent="sendTestMessage" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Destination Phone Number</label>
                        <input wire:model="test_phone_number" 
                               type="text" 
                               placeholder="+15551234567 (with country code)" 
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @error('test_phone_number') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 rounded-xl bg-gray-900 dark:bg-gray-800 hover:bg-gray-800 dark:hover:bg-gray-700 text-white font-semibold text-xs shadow-sm transition-colors flex items-center gap-1.5">
                            <span wire:loading.remove>Send Test Ping</span>
                            <span wire:loading>Sending...</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Col: Meta Webhook Setup Instructions -->
        <div class="space-y-6">
            <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white">Meta Webhook Configuration</h3>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    In your Meta App Dashboard under <strong>WhatsApp &gt; Configuration</strong>, set the Webhook fields to:
                </p>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Callback URL</span>
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 font-mono text-[11px] text-gray-800 dark:text-gray-200 break-all select-all border border-gray-200 dark:border-gray-700">
                            {{ $webhookCallbackUrl }}
                        </div>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Verify Token</span>
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 font-mono text-[11px] text-gray-800 dark:text-gray-200 select-all border border-gray-200 dark:border-gray-700">
                            {{ $verify_token }}
                        </div>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Webhook Subscription Fields</span>
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            <span class="px-2 py-0.5 rounded font-mono text-[10px] bg-blue-50 dark:bg-blue-950/60 text-primary border border-blue-200 dark:border-blue-900/40">messages</span>
                            <span class="px-2 py-0.5 rounded font-mono text-[10px] bg-blue-50 dark:bg-blue-950/60 text-primary border border-blue-200 dark:border-blue-900/40">message_template_status_update</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Checklist -->
            <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-3">
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
            </div>
        </div>
    </div>
</div>
