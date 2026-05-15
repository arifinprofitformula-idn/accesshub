<?php

namespace App\Filament\Resources\AccessItems\Pages;

use App\Filament\Resources\AccessItems\AccessItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAccessItems extends ListRecords
{
    protected static string $resource = AccessItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
