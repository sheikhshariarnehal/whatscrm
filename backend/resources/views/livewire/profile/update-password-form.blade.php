<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<x-card bodyClass="p-6 sm:p-8 space-y-6">
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Update Security Password') }}</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </div>

    <form wire:submit="updatePassword" class="space-y-5">
        <x-form-item label="Current Password" :required="true" :error="$errors->first('current_password')">
            <x-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" :invalid="$errors->has('current_password')" autocomplete="current-password" placeholder="••••••••••••" />
        </x-form-item>

        <x-form-item label="New Password" :required="true" :error="$errors->first('password')">
            <x-input wire:model="password" id="update_password_password" name="password" type="password" :invalid="$errors->has('password')" autocomplete="new-password" placeholder="At least 8 characters" />
        </x-form-item>

        <x-form-item label="Confirm New Password" :required="true" :error="$errors->first('password_confirmation')">
            <x-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" :invalid="$errors->has('password_confirmation')" autocomplete="new-password" placeholder="Confirm your new password" />
        </x-form-item>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
            <x-action-message class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1.5" on="password-updated">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ __('Password updated securely.') }}</span>
            </x-action-message>

            <x-button type="submit" variant="solid" size="sm">
                {{ __('Update Password') }}
            </x-button>
        </div>
    </form>
</x-card>
