<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        activity()
            ->causedBy($event->user)
            ->event('logout')
            ->withProperties([
                'ip_address' => Request::ip(),
            ])
            ->log('User logged out');
    }
}
