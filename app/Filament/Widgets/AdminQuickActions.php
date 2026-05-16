<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Categories\CategoryResource;
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
        $items = [
            [
                'label' => 'Pengguna',
                'hint' => 'Kelola akun user',
                'url' => UserResource::getUrl(),
                'theme' => 'fuchsia',
                'icon' => 'users',
            ],
        ];

        // Super admin gets extra quick access to categories.
        if (auth()->user()?->hasRole('super_admin')) {
            $items[] = [
                'label' => 'Kategori',
                'hint' => 'Struktur konten',
                'url' => CategoryResource::getUrl(),
                'theme' => 'emerald',
                'icon' => 'grid',
            ];
        }

        return ['items' => $items];
    }
}
