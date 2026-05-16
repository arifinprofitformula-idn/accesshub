<?php

namespace App\Http\Controllers\Auth;

use App\Filament\Resources\Users\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->to($this->redirectPathFor($request));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function redirectPathFor(Request $request): string
    {
        $user = $request->user();

        if ($user?->hasAnyRole(['super_admin', 'admin'])) {
            return UserResource::getUrl('index');
        }

        return route('app.dashboard', absolute: false);
    }
}
