<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (! $user->is_active) {
            $this->logout($request);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi admin.']);
        }

        if (blank($user->approved_at)) {
            $this->logout($request);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun Anda sedang menunggu persetujuan admin.']);
        }

        return $next($request);
    }

    private function logout(Request $request): void
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
