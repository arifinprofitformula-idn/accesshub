<section class="space-y-6">
    <header>
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-300">Danger Zone</p>
        <h2 class="mt-1 text-xl font-semibold text-white">{{ __('Delete Account') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-300">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-rose-400/25 bg-rose-500/12 px-5 py-3 text-sm font-semibold text-rose-100 transition hover:bg-rose-500/18"
    >{{ __('Delete Account') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="rounded-[1.6rem] bg-slate-950 p-6 text-slate-100 shadow-[0_30px_90px_-48px_rgba(15,23,42,0.9)]">
            @csrf
            @method('delete')

            <h2 class="text-xl font-semibold text-white">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-3 text-sm leading-6 text-slate-300">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="mb-2 block text-sm font-semibold text-cyan-200">{{ __('Password') }}</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="ah-input-lg"
                    placeholder="{{ __('Password') }}"
                >

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="ah-secondary-btn justify-center">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-rose-400/25 bg-rose-500/14 px-5 py-3 text-sm font-semibold text-rose-100 transition hover:bg-rose-500/20">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
