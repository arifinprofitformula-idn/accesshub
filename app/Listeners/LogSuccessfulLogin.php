<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $event->user->forceFill([
            'last_login_at' => now(),
        ])->saveQuietly();

        activity()
            ->causedBy($event->user)
            ->event('login')
            ->withProperties([
                'ip_address' => Request::ip(),
            ])
            ->log('User logged in');
    }
}
