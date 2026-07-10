<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\UserRole;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'status',
        'pharmacy_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'role' => UserRole::class,
        ];
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasUserRole(UserRole::SUPER_ADMIN);
    }

    /**
     * Central capability check for tenant operations. The pharmacy owner (and the
     * platform owner) implicitly hold every capability within their scope; staff
     * are granted specific abilities through their Spatie job-role permissions
     * (e.g. 'create sale', 'view medicines'). Policies delegate here so the
     * owner-vs-staff rule lives in exactly one place.
     */
    public function canDo(string $permission): bool
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        return $this->hasPermissionTo($permission);
    }

    public function isAdmin(): bool
    {
        return $this->hasUserRole(UserRole::ADMIN);
    }

    public function isStaff(): bool
    {
        return $this->hasUserRole(UserRole::STAFF);
    }

    public function isClient(): bool
    {
        return $this->hasUserRole(UserRole::CLIENT);
    }

    /**
     * Robust role check that works whether `role` is stored as the UserRole enum
     * (via cast), a raw string, or granted through a Spatie role. Keeps the three
     * boolean helpers above tolerant of legacy rows and mixed sources.
     */
    private function hasUserRole(UserRole $role): bool
    {
        $current = $this->role instanceof UserRole
            ? $this->role
            : (is_string($this->role) ? UserRole::tryFrom($this->role) : null);

        return $current === $role || $this->hasRole($role->value);
    }
}
