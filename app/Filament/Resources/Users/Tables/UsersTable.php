<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
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
                    ->searchable(['name'])
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('roles_label')
                    ->label('Role')
                    ->state(fn (User $record): string => $record->getRoleNames()->implode(', '))
                    ->badge(),
                TextColumn::make('user_status')
                    ->label('Status')
                    ->state(function (User $record): string {
                        if (! $record->is_active) {
                            return 'Inactive';
                        }

                        return $record->approved_at ? 'Approved' : 'Pending';
                    })
                    ->badge()
                    ->color(function (User $record): string {
                        if (! $record->is_active) {
                            return 'danger';
                        }

                        return $record->approved_at ? 'success' : 'warning';
                    }),
                TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime('d M Y H:i')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending approval',
                        'approved' => 'Approved',
                        'inactive' => 'Inactive',
                    ])
                    ->query(function ($query, array $data) {
                        match ($data['value'] ?? null) {
                            'pending' => $query->whereNull('approved_at')->where('is_active', true),
                            'approved' => $query->whereNotNull('approved_at')->where('is_active', true),
                            'inactive' => $query->where('is_active', false),
                            default => null,
                        };
                    }),
                SelectFilter::make('role')
                    ->label('Role')
                    ->relationship('roles', 'name'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->iconButton()
                    ->hiddenLabel()
                    ->size('sm')
                    ->tooltip('Edit')
                    ->visible(fn (User $record): bool => static::canManageRecord($record)),

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
                    ->visible(fn (User $record): bool => static::canApprove($record))
                    ->action(function (User $record): void {
                        $record->update([
                            'approved_at' => now(),
                            'is_active' => true,
                        ]);

                        if (! $record->hasAnyRole(['user', 'staff', 'admin', 'super_admin'])) {
                            $record->assignRole('user');
                        }

                        Notification::make()
                            ->title('User berhasil di-approve.')
                            ->success()
                            ->send();
                    }),

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
                    ->visible(fn (User $record): bool => static::canActivate($record))
                    ->action(function (User $record): void {
                        $record->update(['is_active' => true]);

                        Notification::make()
                            ->title('User berhasil diaktifkan.')
                            ->success()
                            ->send();
                    }),

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
                    ->visible(fn (User $record): bool => static::canDeactivate($record))
                    ->action(function (User $record): void {
                        $record->update(['is_active' => false]);

                        Notification::make()
                            ->title('User berhasil dinonaktifkan.')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActionsColumnLabel('Action')
            ->toolbarActions([]);
    }

    protected static function canManageRecord(User $record): bool
    {
        $currentUser = Auth::user();

        if (! $currentUser?->can('users.update')) {
            return false;
        }

        if ($record->hasRole('super_admin') && ! $currentUser->hasRole('super_admin')) {
            return false;
        }

        return true;
    }

    protected static function canApprove(User $record): bool
    {
        return static::canManageRecord($record)
            && blank($record->approved_at)
            && $record->id !== Auth::id();
    }

    protected static function canActivate(User $record): bool
    {
        return static::canManageRecord($record)
            && ! $record->is_active
            && $record->id !== Auth::id();
    }

    protected static function canDeactivate(User $record): bool
    {
        return static::canManageRecord($record)
            && $record->is_active
            && $record->id !== Auth::id();
    }
}
