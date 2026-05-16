<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'whatsapp',
        'password',
        'avatar',
        'is_active',
        'last_login_at',
        'approved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && filled($this->approved_at) && $this->hasAnyRole(['super_admin', 'admin']);
    }

    public function isApproved(): bool
    {
        return filled($this->approved_at);
    }

    public static function normalizeWhatsapp(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        if ($normalized === '') {
            return null;
        }

        $normalized = preg_replace('/[\s\-\(\)]+/', '', $normalized);

        return $normalized !== '' ? $normalized : null;
    }

    public static function deriveNameFromEmail(string $email, ?string $fallback = null): string
    {
        $localPart = trim((string) str($email)->before('@'));

        if ($localPart !== '') {
            return $localPart;
        }

        return $fallback ?: 'user';
    }

    public function createdLinks(): HasMany
    {
        return $this->hasMany(Link::class, 'created_by');
    }

    public function createdAccessItems(): HasMany
    {
        return $this->hasMany(AccessItem::class, 'created_by');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteLinks(): BelongsToMany
    {
        return $this->belongsToMany(Link::class, 'favorites')
            ->withTimestamps();
    }
}
