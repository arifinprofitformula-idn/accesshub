<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
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
                    ->label('Nama / Email')
                    ->description(fn (User $record): string => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable(),
                TextColumn::make('roles_label')
                    ->label('Role')
                    ->state(fn (User $record): string => $record->getRoleNames()->implode(', '))
                    ->badge(),
                TextColumn::make('approval_status')
                    ->label('Approval')
                    ->state(fn (User $record): string => $record->approved_at ? 'Approved' : 'Pending')
                    ->badge()
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
                        match ($data['value'] ?? null) {
                            'approved' => $query->whereNotNull('approved_at'),
                            'pending' => $query->whereNull('approved_at'),
                            default => null,
                        };
                    }),
                SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->iconButton()
                    ->hiddenLabel()
                    ->size('sm')
                    ->tooltip('Edit'),

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->iconButton()
                    ->hiddenLabel()
                    ->size('sm')
                    ->tooltip('Approve')
                    ->requiresConfirmation()
                    ->modalHeading('Approve pengguna ini?')
                    ->modalDescription('Pengguna akan dapat login dan menggunakan aplikasi.')
                    ->visible(fn (User $record): bool => blank($record->approved_at))
                    ->action(fn (User $record) => $record->update(['approved_at' => now()])),

                Action::make('activate')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->iconButton()
                    ->hiddenLabel()
                    ->size('sm')
                    ->tooltip('Aktifkan')
                    ->requiresConfirmation()
                    ->modalHeading('Aktifkan akun ini?')
                    ->visible(fn (User $record): bool => ! $record->is_active)
                    ->action(fn (User $record) => $record->update(['is_active' => true])),

                Action::make('deactivate')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-pause')
                    ->color('danger')
                    ->iconButton()
                    ->hiddenLabel()
                    ->size('sm')
                    ->tooltip('Nonaktifkan')
                    ->requiresConfirmation()
                    ->modalHeading('Nonaktifkan akun ini?')
                    ->modalDescription('Pengguna tidak akan bisa login.')
                    ->visible(fn (User $record): bool => $record->is_active && $record->id !== Auth::id())
                    ->action(fn (User $record) => $record->update(['is_active' => false])),
            ])
            ->recordActionsColumnLabel('Action')
            ->toolbarActions([]);
    }
}
