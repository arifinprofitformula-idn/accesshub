<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-50">Welcome Back</h2>
            <p class="auth-page-copy">
                Sign in to continue managing your team’s important work access from one secure command center.
            </p>
        </div>

        @if (session('status'))
            <div class="auth-alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="auth-alert-error">
                <p class="font-medium text-rose-100">We couldn’t complete your sign in.</p>
                <ul class="mt-2 space-y-1 text-sm text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="auth-label">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="auth-input @error('email') auth-input-error @enderror"
                    placeholder="you@company.com"
                >
            </div>

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

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <label for="remember_me" class="inline-flex items-center gap-3 text-sm text-slate-300">
                    <input id="remember_me" type="checkbox" class="auth-checkbox" name="remember">
                    <span>Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                @endif
            </div>

            <button type="submit" class="gradient-button w-full">
                Sign in to AccessHub
            </button>
        </form>
    </div>
</x-guest-layout>
