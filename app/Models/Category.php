<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public const ACTIVE_OPTIONS_CACHE_KEY = 'categories.active.options';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::saved(fn (): bool => Cache::forget(self::ACTIVE_OPTIONS_CACHE_KEY));
        static::deleted(fn (): bool => Cache::forget(self::ACTIVE_OPTIONS_CACHE_KEY));
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }

    public function accessItems(): HasMany
    {
        return $this->hasMany(AccessItem::class);
    }

    public static function activeOptions(): Collection
    {
        return Cache::rememberForever(self::ACTIVE_OPTIONS_CACHE_KEY, static fn (): Collection => self::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']));
    }
}
