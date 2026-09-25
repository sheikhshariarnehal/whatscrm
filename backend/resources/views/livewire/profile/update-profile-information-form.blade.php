<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<x-card bodyClass="p-6 sm:p-8 space-y-6">
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Profile Information') }}</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
            {{ __("Update your account's profile name and authenticated email address.") }}
        </p>
    </div>

    <form wire:submit="updateProfileInformation" class="space-y-5">
        <x-form-item label="Full Name" :required="true" :error="$errors->first('name')">
            <x-input wire:model="name" id="name" name="name" type="text" :invalid="$errors->has('name')" required autofocus autocomplete="name" placeholder="Your Full Name" />
        </x-form-item>

        <x-form-item label="Email Address" :required="true" :error="$errors->first('email')">
            <x-input wire:model="email" id="email" name="email" type="email" :invalid="$errors->has('email')" required autocomplete="username" placeholder="name@company.com" />
            
            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ __('Your email address is unverified.') }}</span>
                        <button wire:click.prevent="sendVerification" class="underline font-semibold hover:text-amber-700 dark:hover:text-amber-300 ml-1">
                            {{ __('Resend verification email') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </x-form-item>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
            <x-action-message class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1.5" on="profile-updated">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ __('Profile saved successfully.') }}</span>
            </x-action-message>

            <x-button type="submit" variant="solid" size="sm">
                {{ __('Save Changes') }}
            </x-button>
        </div>
    </form>
</x-card>
