<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles_label')
                    ->label('Role')
                    ->state(fn (User $record): string => $record->getRoleNames()->implode(', '))
                    ->badge(),
                TextColumn::make('approved_at')
                    ->label('Approved')
                    ->dateTime('d M Y')
                    ->placeholder('Pending')
                    ->sortable()
                    ->color(fn (User $record): string => $record->approved_at ? 'success' : 'warning'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_links_count')
                    ->label('Links')
                    ->counts('createdLinks')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('approval')
                    ->label('Status Approval')
                    ->options([
                        'approved' => 'Sudah Diapprove',
                        'pending' => 'Menunggu Approval',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'approved') {
                            $query->whereNotNull('approved_at');
                        } elseif ($data['value'] === 'pending') {
                            $query->whereNull('approved_at');
                        }
                    }),
                SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Edit'),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve pengguna ini?')
                        ->modalDescription('Pengguna akan dapat login dan menggunakan aplikasi.')
                        ->visible(fn (User $record): bool => blank($record->approved_at))
                        ->action(fn (User $record) => $record->update(['approved_at' => now()])),

                    Action::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan akun ini?')
                        ->visible(fn (User $record): bool => ! $record->is_active)
                        ->action(fn (User $record) => $record->update(['is_active' => true])),

                    Action::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-pause')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Nonaktifkan akun ini?')
                        ->modalDescription('Pengguna tidak akan bisa login.')
                        ->visible(fn (User $record): bool => $record->is_active && $record->id !== Auth::id())
                        ->action(fn (User $record) => $record->update(['is_active' => false])),
                ]),
            ])
            ->toolbarActions([]);
    }
}
