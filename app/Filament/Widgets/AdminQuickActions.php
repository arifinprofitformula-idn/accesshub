<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AccessItems\AccessItemResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Links\LinkResource;
use App\Filament\Resources\Users\UserResource;
use Filament\Widgets\Widget;

class AdminQuickActions extends Widget
{
    protected static ?int $sort = -3;

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.admin-quick-actions';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'items' => [
                [
                    'label' => 'Links',
                    'hint' => 'Kelola link',
                    'url' => LinkResource::getUrl(),
                    'theme' => 'cyan',
                    'icon' => 'link',
                ],
                [
                    'label' => 'Access',
                    'hint' => 'Data akses',
                    'url' => AccessItemResource::getUrl(),
                    'theme' => 'amber',
                    'icon' => 'shield',
                ],
                [
                    'label' => 'Kategori',
                    'hint' => 'Struktur konten',
                    'url' => CategoryResource::getUrl(),
                    'theme' => 'emerald',
                    'icon' => 'grid',
                ],
                [
                    'label' => 'Users',
                    'hint' => 'Hak akses user',
                    'url' => UserResource::getUrl(),
                    'theme' => 'fuchsia',
                    'icon' => 'users',
                ],
            ],
        ];
    }
}
