<?php

namespace App\Models;

use Database\Factories\AccessItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Models\Role;

class AccessItem extends Model
{
    /** @use HasFactory<AccessItemFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected const ACTIVITY_LOG_ATTRIBUTES = [
        'platform_name',
        'category_id',
        'pic_name',
        'sensitivity_level',
        'status',
        'created_by',
        'last_checked_at',
    ];

    protected static array $recordEvents = [
        'created',
        'deleted',
        'restored',
    ];

    protected $fillable = [
        'platform_name',
        'login_url',
        'username',
        'category_id',
        'pic_name',
        'sensitivity_level',
        'password_location',
        'note',
        'status',
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
        static::creating(function (AccessItem $accessItem): void {
            if (blank($accessItem->created_by) && auth()->check()) {
                $accessItem->created_by = auth()->id();
            }
        });

        static::saving(function (AccessItem $accessItem): void {
            Validator::make($accessItem->attributesToArray(), [
                'platform_name' => ['required', 'string', 'max:255'],
                'login_url' => ['nullable', 'url:http,https', 'max:2048'],
                'username' => ['nullable', 'string', 'max:255'],
                'category_id' => ['nullable', 'integer'],
                'pic_name' => ['nullable', 'string', 'max:255'],
                'sensitivity_level' => ['required', 'in:low,medium,high'],
                'password_location' => ['nullable', 'string', 'max:255'],
                'note' => ['nullable', 'string', 'max:1500'],
                'status' => ['required', 'in:active,needs_review,archived'],
            ])->validate();
        });

        static::updated(function (AccessItem $accessItem): void {
            $changedFields = array_values(array_intersect(array_keys($accessItem->getChanges()), self::ACTIVITY_LOG_ATTRIBUTES));

            if ($changedFields === []) {
                return;
            }

            activity('access_items')
                ->causedBy(auth()->user())
                ->performedOn($accessItem)
                ->event($accessItem->wasChanged('status') && $accessItem->status === 'archived' ? 'archived' : 'updated')
                ->withProperties([
                    'changed_fields' => $changedFields,
                    'status_transition' => $accessItem->wasChanged('status')
                        ? [
                            'from' => $accessItem->getOriginal('status'),
                            'to' => $accessItem->status,
                        ]
                        : null,
                ])
                ->log($accessItem->wasChanged('status') && $accessItem->status === 'archived' ? 'Access item archived' : 'Access item updated');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('access_items')
            ->logOnly(self::ACTIVITY_LOG_ATTRIBUTES)
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName): string => 'Access item '.$eventName);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visibleToRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'access_item_role_visibility');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($search): void {
            $builder
                ->where('platform_name', 'like', "%{$search}%")
                ->orWhere('login_url', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('pic_name', 'like', "%{$search}%")
                ->orWhere('password_location', 'like', "%{$search}%")
                ->orWhere('note', 'like', "%{$search}%")
                ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"));
        });
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $query;
        }

        $roleIds = $user->roles->modelKeys();

        return $query->where(function (Builder $builder) use ($roleIds, $user): void {
            $builder
                ->where('created_by', $user->id)
                ->orWhereHas('visibleToRoles', fn (Builder $rolesQuery) => $rolesQuery->whereIn('roles.id', $roleIds));
        });
    }

    public function isVisibleTo(User $user): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        return $this->created_by === $user->id
            || $this->visibleToRoles()->whereIn('roles.id', $user->roles->modelKeys())->exists();
    }
}
