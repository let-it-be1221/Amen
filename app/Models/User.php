<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_WAITER = 'waiter';
    const ROLE_COOKER = 'cooker';
    const ROLE_CASHIER = 'cashier';

    const ROLES = [
        self::ROLE_ADMIN => 'Administrator',
        self::ROLE_SUPERVISOR => 'Supervisor',
        self::ROLE_WAITER => 'Waiter',
        self::ROLE_COOKER => 'Cooker',
        self::ROLE_CASHIER => 'Cashier',
    ];

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
        'avatar',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSupervisor(): bool
    {
        return $this->role === self::ROLE_SUPERVISOR;
    }

    public function isWaiter(): bool
    {
        return $this->role === self::ROLE_WAITER;
    }

    public function isCooker(): bool
    {
        return $this->role === self::ROLE_COOKER;
    }

    public function isCashier(): bool
    {
        return $this->role === self::ROLE_CASHIER;
    }

    // Orders submitted by this waiter
    public function ordersAsWaiter(): HasMany
    {
        return $this->hasMany(Order::class, 'waiter_id');
    }

    // Orders cooked by this cooker
    public function ordersAsCooker(): HasMany
    {
        return $this->hasMany(Order::class, 'cooker_id');
    }

    // Payments processed by this cashier
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'cashier_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
