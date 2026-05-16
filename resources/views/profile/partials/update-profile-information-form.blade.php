<section>
    <header>
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-300">Informasi Profil</p>
        <h2 class="mt-1 text-xl font-semibold text-white">{{ __('Profile Information') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-300">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="mb-2 block text-sm font-semibold text-cyan-200">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="ah-input-lg" required autofocus autocomplete="name">
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-cyan-200">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="ah-input-lg" required autocomplete="username">
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-[1.25rem] border border-amber-300/15 bg-amber-300/10 p-4">
                    <p class="text-sm leading-6 text-amber-100">
                        {{ __('Your email address is unverified.') }}
                    </p>

                    <button form="send-verification" class="mt-3 inline-flex min-h-11 items-center justify-center rounded-2xl border border-amber-300/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-amber-100 transition hover:bg-white/15">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-3 text-sm font-medium text-emerald-300">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-3 pt-1 sm:flex-row sm:items-center">
            <button type="submit" class="ah-accent-btn justify-center">{{ __('Save Changes') }}</button>

            @if (session('status') === 'profile-updated')
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
