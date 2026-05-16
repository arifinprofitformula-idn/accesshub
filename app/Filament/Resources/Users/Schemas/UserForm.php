<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi User')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(30)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->unique(ignoreRecord: true)
                            ->placeholder('08xxxxxxxxxx atau +62xxxxxxxxxx')
                            ->helperText('Dipakai untuk kontak admin dan registrasi mandiri.'),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('Kosongkan jika tidak ingin mengganti password.')
                            ->minLength(8),
                        Select::make('roles')
                            ->label('Role')
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => Auth::user()?->hasRole('super_admin')
                                    ? $query
                                    : $query->where('name', '!=', 'super_admin'),
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        DateTimePicker::make('approved_at')
                            ->label('Tanggal Approval')
                            ->nullable()
                            ->helperText('Kosongkan untuk menangguhkan approval. Isi untuk menyetujui akun.')
                            ->default(fn (string $operation) => $operation === 'create' ? now() : null),
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
