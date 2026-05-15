<?php

namespace App\Filament\Resources\Links\Tables;

use App\Models\Link;
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

class LinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(35)
                    ->copyable()
                    ->copyMessage('URL berhasil disalin')
                    ->tooltip(fn (Link $record): string => $record->url)
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),
                TextColumn::make('platform')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'important' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'needs_review' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('visibility')
                    ->label('Visibility')
                    ->badge(),
                TextColumn::make('tags.name')
                    ->label('Tag')
                    ->badge()
                    ->separator(','),
                TextColumn::make('favorites_count')
                    ->label('Favorite')
                    ->badge()
                    ->color('info'),
                TextColumn::make('owner_name')
                    ->label('PIC')
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
                SelectFilter::make('priority')
                    ->options([
                        'normal' => 'Biasa',
                        'important' => 'Penting',
                        'critical' => 'Sangat Penting',
                    ]),
                SelectFilter::make('platform')
                    ->options(fn (): array => Link::query()
                        ->whereNotNull('platform')
                        ->where('platform', '!=', '')
                        ->distinct()
                        ->orderBy('platform')
                        ->pluck('platform', 'platform')
                        ->all()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('open_link')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Link $record): string => route('app.links.open', $record))
                    ->openUrlInNewTab()
                    ->authorize(fn (Link $record): bool => auth()->user()->can('open', $record)),
                Action::make('favorite')
                    ->label(fn (Link $record): string => $record->favorites()->where('user_id', auth()->id())->exists() ? 'Unpin' : 'Pin')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->authorize(fn (Link $record): bool => auth()->user()->can('favorite', $record))
                    ->action(function (Link $record): void {
                        $favorite = $record->favorites()->where('user_id', auth()->id())->first();

                        if ($favorite) {
                            $favorite->delete();
                            Notification::make()->title('Link dihapus dari favorit')->success()->send();

                            return;
                        }

                        $record->favorites()->create([
                            'user_id' => auth()->id(),
                        ]);

                        Notification::make()->title('Link ditandai sebagai favorit')->success()->send();
                    }),
                Action::make('archive')
                    ->label('Arsipkan')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (Link $record): bool => $record->status !== 'archived')
                    ->authorize(fn (Link $record): bool => auth()->user()->can('archive', $record))
                    ->action(fn (Link $record) => $record->update(['status' => 'archived'])),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['category', 'tags']));
    }
}
