<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'whatsapp' => ['required', 'string', 'max:30', 'min:8', 'regex:/^[0-9+\-\s\(\)]+$/', 'unique:'.User::class.',whatsapp'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.unique' => 'Nomor WhatsApp sudah terdaftar.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid.',
        ]);

        $whatsapp = User::normalizeWhatsapp($validated['whatsapp']);
        $name = User::deriveNameFromEmail($validated['email'], $whatsapp);

        $user = DB::transaction(function () use ($validated, $name, $whatsapp): User {
            $user = User::create([
                'name' => $name,
                'email' => $validated['email'],
                'whatsapp' => $whatsapp,
                'password' => $validated['password'],
                'approved_at' => null,
                'is_active' => true,
            ]);

            $user->assignRole('user');

            return $user;
        });

        event(new Registered($user));

        return redirect()->to(route('login', absolute: false))
            ->with('status', 'Registrasi berhasil. Akun Anda sedang menunggu persetujuan admin.');
    }
}
