<?php

namespace App\Filament\Resources\AccessItems\Tables;

use App\Models\AccessItem;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccessItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('platform_name')
                    ->label('Platform')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('login_url')
                    ->label('Login URL')
                    ->limit(35)
                    ->tooltip(fn (AccessItem $record): ?string => $record->login_url)
                    ->searchable(),
                TextColumn::make('username')
                    ->label('Username')
                    ->copyable()
                    ->copyMessage('Username berhasil disalin')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),
                TextColumn::make('pic_name')
                    ->label('PIC')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sensitivity_level')
                    ->label('Sensitivitas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'needs_review' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('password_location')
                    ->label('Password Location')
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('last_checked_at')
                    ->label('Dicek')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'needs_review' => 'Perlu Dicek',
                        'archived' => 'Arsip',
                    ]),
                SelectFilter::make('sensitivity_level')
                    ->label('Sensitivitas')
                    ->options([
                        'low' => 'Rendah',
                        'medium' => 'Sedang',
                        'high' => 'Tinggi',
                    ]),
                SelectFilter::make('pic_name')
                    ->label('PIC')
                    ->options(fn (): array => AccessItem::query()
                        ->whereNotNull('pic_name')
                        ->where('pic_name', '!=', '')
                        ->distinct()
                        ->orderBy('pic_name')
                        ->pluck('pic_name', 'pic_name')
                        ->all()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('open_login')
                    ->label('Open Login')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (AccessItem $record): string => route('app.access-items.open', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (AccessItem $record): bool => filled($record->login_url))
                    ->authorize(fn (AccessItem $record): bool => auth()->user()->can('open', $record)),
                Action::make('copy_username')
                    ->label('Copy Username')
                    ->icon('heroicon-o-document-duplicate')
                    ->visible(fn (AccessItem $record): bool => filled($record->username))
                    ->action(function (): void {
                        Notification::make()
                            ->title('Gunakan tombol copy di kolom username untuk menyalin nilai.')
                            ->body('Username tersimpan sebagai metadata akses, bukan password.')
                            ->success()
                            ->send();
                    }),
                Action::make('archive')
                    ->label('Arsipkan')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (AccessItem $record): bool => $record->status !== 'archived')
                    ->authorize(fn (AccessItem $record): bool => auth()->user()->can('archive', $record))
                    ->action(fn (AccessItem $record) => $record->update(['status' => 'archived'])),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['category', 'creator', 'visibleToRoles']));
    }
}
