<x-app-layout>
    <div class="p-4 sm:p-6 lg:p-8 space-y-6">
        <!-- Profile Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Account Profile</h1>
                    <x-tag color="primary" class="font-bold capitalize">{{ auth()->user()->role ?? 'Admin' }}</x-tag>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage your personal credentials, contact email, and account security.
                </p>
            </div>
            <div>
                <x-button variant="default" size="sm" as="a" href="{{ route('settings') }}" icon="settings">
                    Workspace Settings
                </x-button>
            </div>
        </div>

        <!-- 2-Column Responsive Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: User Summary Card -->
            <div class="space-y-6">
                <x-card bodyClass="p-6 space-y-5">
                    <div class="flex items-center gap-4">
                        <x-avatar :name="auth()->user()->name" size="lg" />
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <x-tag color="emerald" prefix class="text-[10px] font-bold">Active</x-tag>
                                <span class="text-xs text-gray-400 font-mono capitalize">{{ auth()->user()->role ?? 'Member' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 space-y-3 text-xs">
                        <div class="flex items-center justify-between text-gray-500 dark:text-gray-400">
                            <span>User ID</span>
                            <span class="font-mono font-semibold text-gray-800 dark:text-gray-200">#{{ auth()->id() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-500 dark:text-gray-400">
                            <span>Email Verified</span>
                            @if(auth()->user()->hasVerifiedEmail())
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span>Verified</span>
                                </span>
                            @else
                                <span class="text-amber-500 font-semibold">Unverified</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-gray-500 dark:text-gray-400">
                            <span>Member Since</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ auth()->user()->created_at?->format('M Y') ?? 'N/A' }}</span>
                        </div>
                    </div>
                </x-card>

                <!-- Quick Navigation Card -->
                <x-card bodyClass="p-5 space-y-3">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Quick Navigation</h4>
                    <div class="space-y-1">
                        <a href="{{ route('settings') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <span class="flex items-center gap-2">
                                <x-nav-icon name="settings" class="w-4 h-4 text-primary" />
                                <span>Workspace Settings</span>
                            </span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('developer') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <span class="flex items-center gap-2">
                                <x-nav-icon name="developer" class="w-4 h-4 text-primary" />
                                <span>API Keys & Webhooks</span>
                            </span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </x-card>
            </div>

            <!-- Right Column: Profile, Password & Danger Zone Forms -->
            <div class="lg:col-span-2 space-y-6">
                <livewire:profile.update-profile-information-form />
                <livewire:profile.update-password-form />
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
