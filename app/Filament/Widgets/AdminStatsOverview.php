<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    protected ?string $heading = 'Ringkasan Pengguna';

    protected ?string $description = 'Status pengguna secara keseluruhan.';

    protected function getStats(): array
    {
        $total = User::query()->count();
        $pending = User::query()->whereNull('approved_at')->count();
        $active = User::query()->where('is_active', true)->whereNotNull('approved_at')->count();
        $inactive = User::query()->where('is_active', false)->count();

        return [
            Stat::make('Total Pengguna', $total)
                ->description('Semua akun terdaftar')
                ->descriptionIcon('heroicon-m-users', IconPosition::Before)
                ->icon('heroicon-o-users')
                ->color('primary')
                ->url(UserResource::getUrl()),

            Stat::make('Menunggu Approval', $pending)
                ->description('Perlu disetujui')
                ->descriptionIcon('heroicon-m-clock', IconPosition::Before)
                ->icon('heroicon-o-clock')
                ->color($pending > 0 ? 'warning' : 'gray')
                ->url(UserResource::getUrl()),

            Stat::make('Pengguna Aktif', $active)
                ->description('Dapat login & akses')
                ->descriptionIcon('heroicon-m-check-circle', IconPosition::Before)
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->url(UserResource::getUrl()),

            Stat::make('Pengguna Nonaktif', $inactive)
                ->description('Akses diblokir')
                ->descriptionIcon('heroicon-m-x-circle', IconPosition::Before)
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->url(UserResource::getUrl()),
        ];
    }
}
