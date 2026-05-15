<?php

namespace App\Filament\Resources\AccessItems\Pages;

use App\Filament\Resources\AccessItems\AccessItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccessItem extends CreateRecord
{
    protected static string $resource = AccessItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
