<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AccessItems\AccessItemResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Links\LinkResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\AccessItem;
use App\Models\Category;
use App\Models\Link;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    protected ?string $heading = 'Overview Singkat';

    protected ?string $description = 'Akses utama dan status penting dalam satu layar.';

    protected function getStats(): array
    {
        return [
            Stat::make('Links', Link::query()->where('status', 'active')->count())
                ->description('Siap dipakai')
                ->descriptionIcon('heroicon-m-arrow-up-right', IconPosition::Before)
                ->icon('heroicon-o-link')
                ->color('info')
                ->url(LinkResource::getUrl()),

            Stat::make('Access', AccessItem::query()->where('status', 'active')->count())
                ->description('Metadata aktif')
                ->descriptionIcon('heroicon-m-shield-check', IconPosition::Before)
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->url(AccessItemResource::getUrl()),

            Stat::make('Kategori', Category::query()->where('is_active', true)->count())
                ->description('Struktur aktif')
                ->descriptionIcon('heroicon-m-squares-2x2', IconPosition::Before)
                ->icon('heroicon-o-squares-2x2')
                ->color('success')
                ->url(CategoryResource::getUrl()),

            Stat::make('Users', User::query()->where('is_active', true)->count())
                ->description('Akun aktif')
                ->descriptionIcon('heroicon-m-users', IconPosition::Before)
                ->icon('heroicon-o-users')
                ->color('primary')
                ->url(UserResource::getUrl()),
        ];
    }
}
