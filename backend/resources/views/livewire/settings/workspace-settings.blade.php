<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Workspace Settings</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Manage company preferences, security credentials, sound alerts, and subscription tier.
        </p>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-sm border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800">
        <button wire:click="setTab('company')" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'company' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span>Company Profile</span>
        </button>
        <button wire:click="setTab('security')" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'security' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Security & Password</span>
        </button>
        <button wire:click="setTab('notifications')" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'notifications' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span>Notifications & Sounds</span>
        </button>
        <button wire:click="setTab('billing')" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'billing' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <span>Billing & Quotas</span>
        </button>
    </div>

    <!-- TAB 1: COMPANY PROFILE -->
    @if($activeTab === 'company')
        <div class="max-w-2xl bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm p-6 sm:p-8">
            <form wire:submit.prevent="saveCompanySettings" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Company / Workspace Name</label>
                    <input 
                        type="text" 
                        wire:model="companyName" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                    @error('companyName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Default Country Code</label>
                        <select wire:model="countryCode" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="+1">+1 (United States / Canada)</option>
                            <option value="+44">+44 (United Kingdom)</option>
                            <option value="+91">+91 (India)</option>
                            <option value="+880">+880 (Bangladesh)</option>
                            <option value="+971">+971 (United Arab Emirates)</option>
                            <option value="+61">+61 (Australia)</option>
                            <option value="+49">+49 (Germany)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Timezone</label>
                        <select wire:model="timezone" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="UTC">UTC (Universal Coordinated Time)</option>
                            <option value="America/New_York">America/New York (EST/EDT)</option>
                            <option value="Europe/London">Europe/London (GMT/BST)</option>
                            <option value="Asia/Dhaka">Asia/Dhaka (GMT+6)</option>
                            <option value="Asia/Dubai">Asia/Dubai (GST)</option>
                            <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition-all">
                        Save Company Profile
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 2: SECURITY & PASSWORD -->
    @if($activeTab === 'security')
        <div class="max-w-xl bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm p-6 sm:p-8">
            <form wire:submit.prevent="updatePassword" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Current Password</label>
                    <input 
                        type="password" 
                        wire:model="currentPassword" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                    @error('currentPassword') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">New Password</label>
                    <input 
                        type="password" 
                        wire:model="newPassword" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                    @error('newPassword') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Confirm New Password</label>
                    <input 
                        type="password" 
                        wire:model="newPassword_confirmation" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition-all">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 3: NOTIFICATIONS & SOUNDS -->
    @if($activeTab === 'notifications')
        <div class="max-w-2xl bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Alert Preferences</h3>

            <div class="space-y-4">
                <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 cursor-pointer">
                    <input type="checkbox" wire:model="soundEnabled" class="mt-0.5 rounded text-primary focus:ring-primary">
                    <div>
                        <div class="text-xs font-bold text-gray-900 dark:text-white">Incoming Message Audio Chime</div>
                        <div class="text-[11px] text-gray-500">Play a pleasant soft sound when a WhatsApp customer sends a new message.</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 cursor-pointer">
                    <input type="checkbox" wire:model="browserPushEnabled" class="mt-0.5 rounded text-primary focus:ring-primary">
                    <div>
                        <div class="text-xs font-bold text-gray-900 dark:text-white">Desktop Push Notifications</div>
                        <div class="text-[11px] text-gray-500">Receive native browser alerts even when the browser tab is minimized.</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 cursor-pointer">
                    <input type="checkbox" wire:model="campaignDigestEnabled" class="mt-0.5 rounded text-primary focus:ring-primary">
                    <div>
                        <div class="text-xs font-bold text-gray-900 dark:text-white">Broadcast Completion Email Digest</div>
                        <div class="text-[11px] text-gray-500">Receive an email summary with read-rates and delivery logs when a mass campaign finishes.</div>
                    </div>
                </label>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                <button wire:click="saveNotificationSettings" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition-all">
                    Save Preferences
                </button>
            </div>
        </div>
    @endif

    <!-- TAB 4: BILLING & QUOTAS -->
    @if($activeTab === 'billing')
        <div class="space-y-6">
            <!-- Active Plan & Usage Bars -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Workspace Plan</span>
                        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white mt-0.5 flex items-center gap-2">
                            <span>{{ $currentPlan->name ?? 'Professional Plan' }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                Active
                            </span>
                        </h2>
                    </div>
                    <div class="text-xs text-gray-500">
                        Renews on <strong>{{ now()->addDays(24)->format('M d, Y') }}</strong>
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
            </div>

            <!-- Available Plans Upgrade -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($availablePlans as $p)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border {{ $currentPlan && $currentPlan->id === $p->id ? 'border-primary ring-2 ring-primary/20' : 'border-gray-200/80 dark:border-gray-800' }} shadow-sm p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ $p->name }}</h3>
                                @if($currentPlan && $currentPlan->id === $p->id)
                                    <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full">Current</span>
                                @endif
                            </div>
                            <div class="text-2xl font-black text-gray-900 dark:text-white mb-4">
                                ${{ number_format($p->price_monthly, 0) }} <span class="text-xs font-normal text-gray-400">/ month</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-4">{{ $p->description }}</p>

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
                                <button disabled class="w-full py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 text-xs font-bold cursor-not-allowed">
                                    Current Plan
                                </button>
                            @else
                                <button wire:click="changePlan({{ $p->id }})" class="w-full py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-bold shadow-sm transition-all">
                                    Upgrade to {{ $p->name }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
