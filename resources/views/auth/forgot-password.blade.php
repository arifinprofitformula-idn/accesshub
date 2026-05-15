<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-50">Reset Your Access</h2>
            <p class="auth-page-copy">
                Enter your work email and we’ll send a secure reset link so you can regain access to your workspace.
            </p>
        </div>

        @if (session('status'))
            <div class="auth-alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="auth-alert-error">
                <ul class="space-y-1 text-sm text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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
                    class="auth-input @error('email') auth-input-error @enderror"
                    placeholder="you@company.com"
                >
            </div>

            <button type="submit" class="gradient-button w-full">
                Send Reset Link
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">Back to login</a>
        </div>
    </div>
</x-guest-layout>
