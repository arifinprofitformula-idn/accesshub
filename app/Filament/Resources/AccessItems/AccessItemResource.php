<?php

namespace App\Filament\Resources\AccessItems;

use App\Filament\Resources\AccessItems\Pages\CreateAccessItem;
use App\Filament\Resources\AccessItems\Pages\EditAccessItem;
use App\Filament\Resources\AccessItems\Pages\ListAccessItems;
use App\Filament\Resources\AccessItems\Schemas\AccessItemForm;
use App\Filament\Resources\AccessItems\Tables\AccessItemsTable;
use App\Models\AccessItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccessItemResource extends Resource
{
    protected static ?string $model = AccessItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-shield-check';

    protected static ?string $recordTitleAttribute = 'platform_name';

    protected static string|\UnitEnum|null $navigationGroup = 'Workspace';

    protected static ?string $navigationLabel = 'Access';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return AccessItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['category', 'creator', 'visibleToRoles']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccessItems::route('/'),
            'create' => CreateAccessItem::route('/create'),
            'edit' => EditAccessItem::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()->where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
