<?php

namespace App\Filament\Resources\AccessItems\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AccessItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Metadata Akses')
                    ->schema([
                        TextInput::make('platform_name')
                            ->label('Nama Platform')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('login_url')
                            ->label('Login URL')
                            ->maxLength(2048)
                            ->rule('nullable|url:http,https')
                            ->placeholder('https://accounts.google.com'),
                        TextInput::make('username')
                            ->label('Username / Email Login')
                            ->maxLength(255),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name', fn (Builder $query) => $query->orderBy('name'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('pic_name')
                            ->label('PIC')
                            ->required()
                            ->maxLength(255),
                        Select::make('sensitivity_level')
                            ->label('Sensitivity Level')
                            ->options([
                                'low' => 'Rendah',
                                'medium' => 'Sedang',
                                'high' => 'Tinggi',
                            ])
                            ->default('medium')
                            ->required(),
                        TextInput::make('password_location')
                            ->label('Lokasi Password Eksternal')
                            ->helperText('Contoh: Bitwarden - Folder Marketing atau Google Password Manager')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('note')
                            ->label('Catatan Akses')
                            ->rows(4)
                            ->maxLength(1500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Status & Izin Lihat')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'needs_review' => 'Perlu Dicek',
                                'archived' => 'Arsip',
                            ])
                            ->default('active')
                            ->required(),
                        DateTimePicker::make('last_checked_at')
                            ->label('Terakhir Dicek')
                            ->seconds(false),
                        Select::make('visibleToRoles')
                            ->label('Role yang Boleh Melihat')
                            ->relationship(
                                'visibleToRoles',
                                'name',
                                fn (Builder $query) => $query->whereIn('name', ['super_admin', 'admin', 'staff'])->orderBy('name')
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Jika kosong, item hanya terlihat oleh admin/super admin dan pembuatnya.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
