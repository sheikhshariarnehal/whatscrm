<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<x-card bodyClass="p-6 sm:p-8 space-y-4" class="border-rose-200/80 dark:border-rose-900/50 bg-rose-50/20 dark:bg-rose-950/10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-rose-600 dark:text-rose-400 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ __('Danger Zone: Delete Account') }}</span>
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-xl">
                {{ __('Permanently remove your personal user profile, credentials, and associated direct records. This action cannot be undone.') }}
            </p>
        </div>

        <x-button
            variant="danger"
            size="sm"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            {{ __('Delete Account') }}
        </x-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6 space-y-5">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 text-rose-600 dark:text-rose-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>{{ __('Are you sure you want to delete your account?') }}</span>
                </h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Once your account is deleted, all of its resources will be permanently removed. Please enter your password to confirm.') }}
                </p>
            </div>

            <x-form-item label="Confirm Password" :required="true" :error="$errors->first('password')">
                <x-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    :invalid="$errors->has('password')"
                    placeholder="{{ __('Enter your current password') }}"
                />
            </x-form-item>

            <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                <x-button variant="default" size="sm" type="button" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-button>

                <x-button variant="danger" size="sm" type="submit">
                    {{ __('Permanently Delete') }}
                </x-button>
            </div>
        </form>
    </x-modal>
</x-card>
