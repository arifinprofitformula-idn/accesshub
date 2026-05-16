<x-guest-layout>
    <div class="mx-auto w-full max-w-md">
        <div class="mb-6 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.34em] text-cyan-300/80">Personal Access</p>
            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-white">Create Your Access</h2>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-3xl border border-rose-400/20 bg-rose-500/10 p-4 text-sm text-rose-100">
                <p class="font-semibold">Registrasi belum berhasil.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-rose-100/90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-cyan-200">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="ah-input-lg w-full"
                    placeholder="you@example.com"
                >
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="whatsapp" class="mb-2 block text-sm font-semibold text-cyan-200">WhatsApp</label>
                <input
                    id="whatsapp"
                    name="whatsapp"
                    type="text"
                    value="{{ old('whatsapp') }}"
                    required
                    autocomplete="tel"
                    class="ah-input-lg w-full"
                    placeholder="08xxxxxxxxxx"
                >
                <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-cyan-200">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="ah-input-lg w-full"
                    placeholder="Minimal 8 karakter"
                >
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-cyan-200">Konfirmasi Password</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="ah-input-lg w-full"
                    placeholder="Ulangi password"
                >
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="ah-accent-btn w-full justify-center">
                Request Access
            </button>

            <div class="rounded-3xl border border-white/8 bg-slate-950/35 px-4 py-3 text-center text-sm text-slate-400">
                Sudah punya akun?
                <a href="{{ route('login', absolute: false) }}" class="font-medium text-cyan-300 transition hover:text-cyan-200">
                    Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
