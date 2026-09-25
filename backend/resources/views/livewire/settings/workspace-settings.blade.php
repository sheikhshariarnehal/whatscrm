<div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Workspace Settings</h1>
                <x-tag color="primary" class="font-bold">{{ $workspace->name ?? 'Workspace' }}</x-tag>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Manage company preferences, security credentials, sound alerts, and subscription tier.
            </p>
        </div>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <x-tabs>
        <x-tab-item wire:click="setTab('company')" :active="$activeTab === 'company'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span>Company Profile</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('security')" :active="$activeTab === 'security'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Security & Password</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('notifications')" :active="$activeTab === 'notifications'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span>Notifications & Sounds</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('billing')" :active="$activeTab === 'billing'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <span>Billing & Quotas</span>
        </x-tab-item>
    </x-tabs>

    <!-- TAB 1: COMPANY PROFILE -->
    @if($activeTab === 'company')
        <div class="max-w-3xl space-y-6">
            <x-card bodyClass="p-6 sm:p-8 space-y-6">
                <!-- Workspace Avatar & Identity -->
                <div class="flex items-center gap-4 pb-6 border-b border-gray-100 dark:border-gray-800">
                    <x-avatar :name="$companyName ?: 'Workspace'" size="lg" />
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $companyName ?: 'My Workspace' }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">Workspace ID: {{ $workspace->slug ?? 'ws-'.($workspace->id ?? 1) }}</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveCompanySettings" class="space-y-5">
                    <x-form-item label="Company / Workspace Name" :required="true" :error="$errors->first('companyName')">
                        <x-input wire:model="companyName" :invalid="$errors->has('companyName')" placeholder="e.g. Acme Corporation" />
                    </x-form-item>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form-item label="Default Country Code" :required="true">
                            <x-select wire:model="countryCode">
                                <option value="+1">+1 (United States / Canada)</option>
                                <option value="+44">+44 (United Kingdom)</option>
                                <option value="+91">+91 (India)</option>
                                <option value="+880">+880 (Bangladesh)</option>
                                <option value="+971">+971 (United Arab Emirates)</option>
                                <option value="+61">+61 (Australia)</option>
                                <option value="+49">+49 (Germany)</option>
                                <option value="+33">+33 (France)</option>
                                <option value="+65">+65 (Singapore)</option>
                                <option value="+55">+55 (Brazil)</option>
                            </x-select>
                        </x-form-item>

                        <x-form-item label="Timezone" :required="true">
                            <x-select wire:model="timezone">
                                <option value="UTC">UTC (Universal Coordinated Time)</option>
                                <option value="America/New_York">America/New York (EST/EDT)</option>
                                <option value="America/Chicago">America/Chicago (CST/CDT)</option>
                                <option value="America/Los_Angeles">America/Los Angeles (PST/PDT)</option>
                                <option value="Europe/London">Europe/London (GMT/BST)</option>
                                <option value="Europe/Paris">Europe/Paris (CET/CEST)</option>
                                <option value="Asia/Dubai">Asia/Dubai (GST +4)</option>
                                <option value="Asia/Dhaka">Asia/Dhaka (GMT +6)</option>
                                <option value="Asia/Kolkata">Asia/Kolkata (IST +5:30)</option>
                                <option value="Asia/Singapore">Asia/Singapore (SGT +8)</option>
                                <option value="Australia/Sydney">Australia/Sydney (AEST)</option>
                            </x-select>
                        </x-form-item>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                        <x-button type="submit" variant="solid" size="md">
                            Save Changes
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    @endif

    <!-- TAB 2: SECURITY & PASSWORD -->
    @if($activeTab === 'security')
        <div class="max-w-2xl space-y-6">
            <!-- Password Update Card -->
            <x-card bodyClass="p-6 sm:p-8 space-y-5">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Change Password</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ensure your account is using a secure, random password to stay protected.</p>
                </div>

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <x-form-item label="Current Password" :required="true" :error="$errors->first('currentPassword')">
                        <x-input type="password" wire:model="currentPassword" :invalid="$errors->has('currentPassword')" placeholder="••••••••••••" />
                    </x-form-item>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form-item label="New Password" :required="true" :error="$errors->first('newPassword')">
                            <x-input type="password" wire:model="newPassword" :invalid="$errors->has('newPassword')" placeholder="At least 8 characters" />
                        </x-form-item>

                        <x-form-item label="Confirm Password" :required="true">
                            <x-input type="password" wire:model="newPassword_confirmation" placeholder="Confirm new password" />
                        </x-form-item>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                        <x-button type="submit" variant="solid" size="md">
                            Update Password
                        </x-button>
                    </div>
                </form>
            </x-card>

            <!-- Two-Factor Authentication Info Card -->
            <x-card bodyClass="p-6 sm:p-8 flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Two-Factor Authentication (2FA)</h3>
                        <x-tag color="amber" class="font-bold text-[10px]">Optional</x-tag>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Add an extra layer of security to your WhatsApp CRM account via Authenticator App.</p>
                </div>
                <x-button variant="default" size="sm">
                    Configure 2FA
                </x-button>
            </x-card>

            <!-- Active Sessions Card -->
            <x-card bodyClass="p-6 sm:p-8 space-y-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Active Browser Sessions</h3>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span>Current Windows Session</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                </div>
                                <div class="text-[11px] text-gray-400 font-mono">Chrome / Windows • Active Now</div>
                            </div>
                        </div>
                        <x-tag color="emerald" class="font-bold text-[10px]">This Device</x-tag>
                    </div>
                </div>
            </x-card>
        </div>
    @endif

    <!-- TAB 3: NOTIFICATIONS & SOUNDS -->
    @if($activeTab === 'notifications')
        <div class="max-w-2xl">
            <x-card bodyClass="p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Alert Preferences</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Configure real-time audio rings, desktop notifications, and periodic campaign digests.</p>
                </div>

                <div class="space-y-4">
                    <div class="p-4 rounded-xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-start justify-between gap-4">
                        <div class="space-y-1">
                            <span class="font-bold text-sm text-gray-800 dark:text-gray-200 block">Incoming Message Audio Chime</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Play a pleasant soft sound when a WhatsApp customer sends a new message.</span>
                            
                            <div class="pt-2">
                                <button type="button" 
                                        onclick="const ctx = new (window.AudioContext || window.webkitAudioContext)(); const osc = ctx.createOscillator(); const gain = ctx.createGain(); osc.type = 'sine'; osc.frequency.setValueAtTime(587.33, ctx.currentTime); osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15); gain.gain.setValueAtTime(0.3, ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3); osc.connect(gain); gain.connect(ctx.destination); osc.start(); osc.stop(ctx.currentTime + 0.3);"
                                        class="inline-flex items-center gap-1.5 text-xs text-primary hover:text-primary-deep font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Play Test Chime</span>
                                </button>
                            </div>
                        </div>
                        <x-switcher wire:model="soundEnabled" />
                    </div>

                    <div class="p-4 rounded-xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="font-bold text-sm text-gray-800 dark:text-gray-200 block">Desktop Push Notifications</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Receive native browser alerts even when the browser tab is minimized.</span>
                        </div>
                        <x-switcher wire:model="browserPushEnabled" />
                    </div>

                    <div class="p-4 rounded-xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="font-bold text-sm text-gray-800 dark:text-gray-200 block">Broadcast Completion Email Digest</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Receive an email summary with read-rates and delivery logs when a mass campaign finishes.</span>
                        </div>
                        <x-switcher wire:model="campaignDigestEnabled" />
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                    <x-button wire:click="saveNotificationSettings" variant="solid" size="md">
                        Save Preferences
                    </x-button>
                </div>
            </x-card>
        </div>
    @endif

    <!-- TAB 4: BILLING & QUOTAS -->
    @if($activeTab === 'billing')
        <div class="space-y-6">
            <!-- Active Plan & Usage Bars -->
            <x-card bodyClass="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Subscription Tier</span>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-0.5 flex items-center gap-2">
                            <span>{{ $currentPlan->name ?? 'Professional Plan' }}</span>
                            <x-tag color="emerald" prefix class="font-bold">Active</x-tag>
                        </h2>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Renews automatically on <strong class="text-gray-900 dark:text-white">{{ now()->addDays(24)->format('M d, Y') }}</strong>
                    </div>
                </div>

                <!-- Progress bars -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                    <!-- Message Quota -->
                    <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 space-y-2.5">
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span class="font-bold">Outbound Messages</span>
                            <span class="font-mono font-bold">{{ $messagesThisMonth }} / {{ number_format($planLimits['messages_per_month'] ?? 50000) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full rounded-full transition-all" style="width: {{ min(100, round(($messagesThisMonth / max(1, $planLimits['messages_per_month'] ?? 50000)) * 100)) }}%"></div>
                        </div>
                    </div>

                    <!-- Contact Quota -->
                    <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 space-y-2.5">
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span class="font-bold">CRM Contacts</span>
                            <span class="font-mono font-bold">{{ $totalContacts }} / {{ number_format($planLimits['contacts'] ?? 10000) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full transition-all" style="width: {{ min(100, round(($totalContacts / max(1, $planLimits['contacts'] ?? 10000)) * 100)) }}%"></div>
                        </div>
                    </div>

                    <!-- Seats Quota -->
                    <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 space-y-2.5">
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span class="font-bold">Agent Seats</span>
                            <span class="font-mono font-bold">{{ $totalMembers }} / {{ $planLimits['seats'] ?? 5 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-purple-500 h-full rounded-full transition-all" style="width: {{ min(100, round(($totalMembers / max(1, $planLimits['seats'] ?? 5)) * 100)) }}%"></div>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Available Plans Upgrade -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($availablePlans as $p)
                    <x-card bodyClass="p-6 flex flex-col justify-between h-full" class="{{ $currentPlan && $currentPlan->id === $p->id ? 'border-primary ring-2 ring-primary/20' : '' }}">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ $p->name }}</h3>
                                @if($currentPlan && $currentPlan->id === $p->id)
                                    <x-tag color="primary" class="font-bold">Current</x-tag>
                                @endif
                            </div>
                            <div class="text-2xl font-black text-gray-900 dark:text-white mb-4">
                                ${{ number_format($p->price_monthly, 0) }} <span class="text-xs font-normal text-gray-400">/ month</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ $p->description }}</p>

                            <ul class="text-xs text-gray-600 dark:text-gray-300 space-y-2.5 mb-6">
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>{{ number_format($p->limits['messages_per_month'] ?? 10000) }} Outbound msgs/mo</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>{{ number_format($p->limits['contacts'] ?? 2500) }} Contacts Directory</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>{{ $p->limits['seats'] ?? 3 }} Agent Seats</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>Official Meta Cloud API</span>
                                </li>
                            </ul>
                        </div>

                        <div>
                            @if($currentPlan && $currentPlan->id === $p->id)
                                <x-button disabled block variant="default" size="sm">
                                    Current Plan
                                </x-button>
                            @else
                                <x-button wire:click="changePlan({{ $p->id }})" block variant="solid" size="sm">
                                    Upgrade to {{ $p->name }}
                                </x-button>
                            @endif
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>
    @endif
</div>

