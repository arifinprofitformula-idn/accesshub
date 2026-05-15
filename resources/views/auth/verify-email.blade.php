<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-50">Verify Your Email</h2>
            <p class="auth-page-copy">
                Check your inbox and confirm your email address to activate secure access to AccessHub.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="auth-alert-success">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="rounded-2xl border border-slate-800/80 bg-slate-950/45 px-5 py-4 text-sm leading-7 text-slate-300">
            Thanks for signing up. Before getting started, verify your email address by clicking the link we just sent. If you didn’t receive it, we can send another verification email.
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                @csrf

                <button type="submit" class="gradient-button w-full sm:w-auto">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="text-center sm:text-right">
                @csrf

                <button type="submit" class="auth-link">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
