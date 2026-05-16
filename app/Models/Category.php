<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

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
        $cached = Cache::get(self::ACTIVE_OPTIONS_CACHE_KEY);

        if ($cached instanceof Collection) {
            return $cached;
        }

        if (is_array($cached)) {
            return collect($cached)
                ->map(static fn (array|object $category): object => (object) [
                    'id' => data_get($category, 'id'),
                    'name' => data_get($category, 'name'),
                ]);
        }

        if ($cached !== null) {
            Cache::forget(self::ACTIVE_OPTIONS_CACHE_KEY);
        }

        return self::refreshActiveOptionsCache();
    }

    protected static function refreshActiveOptionsCache(): Collection
    {
        $options = self::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        try {
            Cache::forever(
                self::ACTIVE_OPTIONS_CACHE_KEY,
                $options
                    ->map(static fn (Category $category): array => [
                        'id' => $category->id,
                        'name' => $category->name,
                    ])
                    ->values()
                    ->all()
            );
        } catch (Throwable) {
            // If the cache store is unavailable, continue serving fresh data.
        }

        return $options;
    }
}
