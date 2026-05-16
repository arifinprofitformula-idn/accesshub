<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-50">Create New Access Key</h2>
            <p class="auth-page-copy">
                Set a new password for your AccessHub account and return to your workspace with confidence.
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

        <form method="POST" action="{{ route('password.store', absolute: false) }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="auth-label">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="auth-input @error('email') auth-input-error @enderror"
                    placeholder="you@company.com"
                >
            </div>

            <div>
                <label for="password" class="auth-label">New Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="auth-input @error('password') auth-input-error @enderror"
                    placeholder="Create a strong password"
                >
            </div>

            <div>
                <label for="password_confirmation" class="auth-label">Confirm New Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="auth-input @error('password_confirmation') auth-input-error @enderror"
                    placeholder="Repeat your new password"
                >
            </div>

            <button type="submit" class="gradient-button w-full">
                Update Access Key
            </button>
        </form>
    </div>
</x-guest-layout>
