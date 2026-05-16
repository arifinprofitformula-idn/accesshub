<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-50">Confirm Your Access</h2>
            <p class="auth-page-copy">
                This protected action needs one more confirmation. Enter your password to continue safely.
            </p>
        </div>

        @if ($errors->any())
            <div class="auth-alert-error">
                <ul class="space-y-1 text-sm text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.confirm', absolute: false) }}" class="space-y-5">
            @csrf

            <div>
                <label for="password" class="auth-label">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="auth-input @error('password') auth-input-error @enderror"
                    placeholder="Enter your password"
                >
            </div>

            <button type="submit" class="gradient-button w-full">
                Confirm Access
            </button>
        </form>
    </div>
</x-guest-layout>
