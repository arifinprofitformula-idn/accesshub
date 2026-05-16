<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['whatsapp'] = \App\Models\User::normalizeWhatsapp($data['whatsapp'] ?? null);
        $data['approved_at'] = now();
        $data['is_active'] = true;

        return $data;
    }
}
