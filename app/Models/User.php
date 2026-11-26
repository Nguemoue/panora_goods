<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRoleEnum;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Guarded([])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'user_zone', 'user_id', 'zone_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'seller_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRoleEnum::class,
        ];
    }

    public static function generateUniqueReferenceNumber(): string
    {
        do {
            $reference = 'PG-'.now()->format('y').'-'.strtoupper(Str::random(4));
        } while (self::query()->where('reference_number', $reference)->exists());

        return $reference;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/'.$this->photo);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name);
    }

    #[Scope]
    protected function seller(Builder $query): Builder
    {
        return $query->where('role', UserRoleEnum::SELLER);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->role === UserRoleEnum::ADMIN && $this->status === 'active';
        }

        if ($panel->getId() === 'seller') {
            return $this->role === UserRoleEnum::SELLER && $this->status === 'active';
        }

        return false;
    }
}
