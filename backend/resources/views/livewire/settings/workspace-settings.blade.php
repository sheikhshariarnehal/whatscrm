<div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-5xl mx-auto">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Workspace Settings</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Manage company preferences, security credentials, sound alerts, and subscription tier.
        </p>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Segment Navigation Tabs -->
    <x-segment>
        <x-segment-item wire:click="setTab('company')" :active="$activeTab === 'company'">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Company Profile</span>
            </span>
        </x-segment-item>
        <x-segment-item wire:click="setTab('security')" :active="$activeTab === 'security'">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Security & Password</span>
            </span>
        </x-segment-item>
        <x-segment-item wire:click="setTab('notifications')" :active="$activeTab === 'notifications'">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>Notifications & Sounds</span>
            </span>
        </x-segment-item>
        <x-segment-item wire:click="setTab('billing')" :active="$activeTab === 'billing'">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Billing & Quotas</span>
            </span>
        </x-segment-item>
    </x-segment>

    <!-- TAB 1: COMPANY PROFILE -->
    @if($activeTab === 'company')
        <div class="max-w-2xl">
            <x-card bodyClass="p-6 sm:p-8">
                <form wire:submit.prevent="saveCompanySettings" class="space-y-5">
                    <x-form-item label="Company / Workspace Name" :required="true" :error="$errors->first('companyName')">
                        <x-input wire:model="companyName" :invalid="$errors->has('companyName')" />
                    </x-form-item>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form-item label="Default Country Code">
                            <x-select wire:model="countryCode">
                                <option value="+1">+1 (United States / Canada)</option>
                                <option value="+44">+44 (United Kingdom)</option>
                                <option value="+91">+91 (India)</option>
                                <option value="+880">+880 (Bangladesh)</option>
                                <option value="+971">+971 (United Arab Emirates)</option>
                                <option value="+61">+61 (Australia)</option>
                                <option value="+49">+49 (Germany)</option>
                            </x-select>
                        </x-form-item>

                        <x-form-item label="Timezone">
                            <x-select wire:model="timezone">
                                <option value="UTC">UTC (Universal Coordinated Time)</option>
                                <option value="America/New_York">America/New York (EST/EDT)</option>
                                <option value="Europe/London">Europe/London (GMT/BST)</option>
                                <option value="Asia/Dhaka">Asia/Dhaka (GMT+6)</option>
                                <option value="Asia/Dubai">Asia/Dubai (GST)</option>
                                <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
                            </x-select>
                        </x-form-item>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                        <x-button type="submit" variant="solid" size="md">
                            Save Company Profile
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    @endif

    <!-- TAB 2: SECURITY & PASSWORD -->
    @if($activeTab === 'security')
        <div class="max-w-xl">
            <x-card bodyClass="p-6 sm:p-8">
                <form wire:submit.prevent="updatePassword" class="space-y-5">
                    <x-form-item label="Current Password" :required="true" :error="$errors->first('currentPassword')">
                        <x-input type="password" wire:model="currentPassword" :invalid="$errors->has('currentPassword')" />
                    </x-form-item>

                    <x-form-item label="New Password" :required="true" :error="$errors->first('newPassword')">
                        <x-input type="password" wire:model="newPassword" :invalid="$errors->has('newPassword')" />
                    </x-form-item>

                    <x-form-item label="Confirm New Password" :required="true">
                        <x-input type="password" wire:model="newPassword_confirmation" />
                    </x-form-item>

                    <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                        <x-button type="submit" variant="solid" size="md">
                            Update Password
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    @endif

    <!-- TAB 3: NOTIFICATIONS & SOUNDS -->
    @if($activeTab === 'notifications')
        <div class="max-w-2xl">
            <x-card bodyClass="p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Alert Preferences</h3>

                <div class="space-y-4">
                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
                        <x-switcher 
                            wire:model="soundEnabled"
                            label="Incoming Message Audio Chime" 
                            description="Play a pleasant soft sound when a WhatsApp customer sends a new message."
                        />
                    </div>

                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
                        <x-switcher 
                            wire:model="browserPushEnabled"
                            label="Desktop Push Notifications" 
                            description="Receive native browser alerts even when the browser tab is minimized."
                        />
                    </div>

                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
                        <x-switcher 
                            wire:model="campaignDigestEnabled"
                            label="Broadcast Completion Email Digest" 
                            description="Receive an email summary with read-rates and delivery logs when a mass campaign finishes."
                        />
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
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Workspace Plan</span>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-0.5 flex items-center gap-2">
                            <span>{{ $currentPlan->name ?? 'Professional Plan' }}</span>
                            <x-tag color="emerald" prefix class="font-bold">Active</x-tag>
                        </h2>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Renews on <strong class="text-gray-900 dark:text-white">{{ now()->addDays(24)->format('M d, Y') }}</strong>
                    </div>
                </div>

                <!-- Progress bars -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                    <!-- Message Quota -->
                    <div class="p-4 rounded-xl bg-gray-50/60 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span class="font-medium">Outbound Messages</span>
                            <span class="font-bold">{{ $messagesThisMonth }} / {{ number_format($planLimits['messages_per_month'] ?? 50000) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full" style="width: {{ min(100, round(($messagesThisMonth / max(1, $planLimits['messages_per_month'] ?? 50000)) * 100)) }}%"></div>
                        </div>
                    </div>

                    <!-- Contact Quota -->
                    <div class="p-4 rounded-xl bg-gray-50/60 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span class="font-medium">CRM Contacts</span>
                            <span class="font-bold">{{ $totalContacts }} / {{ number_format($planLimits['contacts'] ?? 10000) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full" style="width: {{ min(100, round(($totalContacts / max(1, $planLimits['contacts'] ?? 10000)) * 100)) }}%"></div>
                        </div>
                    </div>

                    <!-- Seats Quota -->
                    <div class="p-4 rounded-xl bg-gray-50/60 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span class="font-medium">Team Agent Seats</span>
                            <span class="font-bold">{{ $totalMembers }} / {{ $planLimits['seats'] ?? 5 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-purple-500 h-full" style="width: {{ min(100, round(($totalMembers / max(1, $planLimits['seats'] ?? 5)) * 100)) }}%"></div>
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

                            <ul class="text-xs text-gray-600 dark:text-gray-300 space-y-2 mb-6">
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
