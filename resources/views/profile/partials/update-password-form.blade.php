<section>
    <header>
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-violet-300">Keamanan</p>
        <h2 class="mt-1 text-xl font-semibold text-white">{{ __('Update Password') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-300">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="mb-2 block text-sm font-semibold text-cyan-200">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="ah-input-lg" autocomplete="current-password">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="mb-2 block text-sm font-semibold text-cyan-200">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="ah-input-lg" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="mb-2 block text-sm font-semibold text-cyan-200">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="ah-input-lg" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 pt-1 sm:flex-row sm:items-center">
            <button type="submit" class="ah-accent-btn justify-center">{{ __('Update Password') }}</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-300"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
