<x-app-layout>
    <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-5xl mx-auto">
        <!-- Profile Page Header -->
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Account Profile</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage your personal credentials, contact email, and security settings.</p>
        </div>

        <!-- Cards -->
        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
