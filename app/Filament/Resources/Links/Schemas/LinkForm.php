<?php

namespace App\Filament\Resources\Links\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class LinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Link')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Link')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->maxLength(2048)
                            ->rule('url:http,https')
                            ->placeholder('https://example.com/login'),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Klasifikasi & Akses')
                    ->schema([
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name', fn (Builder $query) => $query->orderBy('name'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('platform')
                            ->label('Platform')
                            ->required()
                            ->maxLength(255),
                        Select::make('tags')
                            ->label('Tag')
                            ->relationship('tags', 'name', fn (Builder $query) => $query->orderBy('name'))
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Tag')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->alphaDash()
                                    ->maxLength(120),
                            ])
                            ->columnSpanFull(),
                        Select::make('priority')
                            ->label('Prioritas')
                            ->options([
                                'normal' => 'Biasa',
                                'important' => 'Penting',
                                'critical' => 'Sangat Penting',
                            ])
                            ->default('normal')
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'needs_review' => 'Perlu Dicek',
                                'archived' => 'Arsip',
                            ])
                            ->default('active')
                            ->required(),
                        Select::make('visibility')
                            ->label('Visibility')
                            ->options([
                                'internal' => 'Publik Internal',
                                'role' => 'Role Tertentu',
                                'private' => 'Pribadi',
                            ])
                            ->default('internal')
                            ->live()
                            ->required(),
                        Select::make('visibleToRoles')
                            ->label('Role yang Diizinkan')
                            ->relationship(
                                'visibleToRoles',
                                'name',
                                fn (Builder $query) => $query->whereIn('name', ['super_admin', 'admin', 'staff'])->orderBy('name')
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(fn (Get $get): bool => $get('visibility') === 'role')
                            ->helperText('Hanya dipakai saat visibility menggunakan role tertentu.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Pemilik & Maintenance')
                    ->schema([
                        TextInput::make('owner_name')
                            ->label('PIC / Pemilik')
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('last_checked_at')
                            ->label('Terakhir Dicek')
                            ->seconds(false),
                    ])
                    ->columns(2),
            ]);
    }
}
