<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Models\Role;

class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected const ACTIVITY_LOG_ATTRIBUTES = [
        'title',
        'url',
        'category_id',
        'platform',
        'priority',
        'status',
        'visibility',
        'owner_name',
        'created_by',
        'last_checked_at',
    ];

    protected static array $recordEvents = [
        'created',
        'deleted',
        'restored',
    ];

    protected $fillable = [
        'title',
        'url',
        'description',
        'category_id',
        'platform',
        'priority',
        'status',
        'visibility',
        'owner_name',
        'created_by',
        'last_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'last_checked_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Link $link): void {
            if (blank($link->created_by) && auth()->check()) {
                $link->created_by = auth()->id();
            }
        });

        static::saving(function (Link $link): void {
            Validator::make($link->attributesToArray(), [
                'title' => ['required', 'string', 'max:150'],
                'url' => ['required', 'url:http,https', 'max:2048'],
                'description' => ['nullable', 'string', 'max:1000'],
                'category_id' => ['nullable', 'integer'],
                'platform' => ['nullable', 'string', 'max:255'],
                'priority' => ['required', 'in:normal,important,critical'],
                'status' => ['required', 'in:active,needs_review,archived'],
                'visibility' => ['required', 'in:internal,role,private'],
                'owner_name' => ['nullable', 'string', 'max:255'],
            ])->validate();
        });

        static::updated(function (Link $link): void {
            $changedFields = array_values(array_intersect(array_keys($link->getChanges()), self::ACTIVITY_LOG_ATTRIBUTES));

            if ($changedFields === []) {
                return;
            }

            activity('links')
                ->causedBy(auth()->user())
                ->performedOn($link)
                ->event($link->wasChanged('status') && $link->status === 'archived' ? 'archived' : 'updated')
                ->withProperties([
                    'changed_fields' => $changedFields,
                    'status_transition' => $link->wasChanged('status')
                        ? [
                            'from' => $link->getOriginal('status'),
                            'to' => $link->status,
                        ]
                        : null,
                ])
                ->log($link->wasChanged('status') && $link->status === 'archived' ? 'Link archived' : 'Link updated');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('links')
            ->logOnly(self::ACTIVITY_LOG_ATTRIBUTES)
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName): string => 'Link '.$eventName);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function visibleToRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'link_role_visibility');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($search): void {
            $builder
                ->where('title', 'like', "%{$search}%")
                ->orWhere('url', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('platform', 'like', "%{$search}%")
                ->orWhere('owner_name', 'like', "%{$search}%")
                ->orWhereHas('tags', fn (Builder $tagQuery) => $tagQuery->where('name', 'like', "%{$search}%"))
                ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"));
        });
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // All other users (admin, staff, user) only see their own links.
        return $query->where('created_by', $user->id);
    }

    public function isVisibleTo(User $user): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $this->created_by === $user->id;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->created_by === $user->id;
    }
}
