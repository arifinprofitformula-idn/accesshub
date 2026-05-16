<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['whatsapp'] = \App\Models\User::normalizeWhatsapp($data['whatsapp'] ?? null);

        return $data;
    }
}
