<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role',
        'username',
        'email',
        'password',
        'credit_balance',
        'is_blocked',
        'is_deleted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'credit_balance' => 'decimal:2',
            'is_blocked' => 'boolean',
            'is_deleted' => 'boolean',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the hashed password for authentication.
     */
    public function getAuthPassword(): string
    {
        return $this->password;
    }

    /**
     * Determine whether the user can access the given Filament panel.
     *
     * When Filament is not installed the $panel argument may be any value.
     */
    public function canAccessPanel(mixed $panel): bool
    {
        if (is_object($panel) && method_exists($panel, 'getId')) {
            if ($panel->getId() !== 'admin') {
                return true;
            }

            return $this->isAdmin() && ! $this->is_blocked && ! $this->is_deleted;
        }

        // If Filament isn't available, default to allowing access only for admins
        return $this->isAdmin() && ! $this->is_blocked && ! $this->is_deleted;
    }

    /**
     * Get the user's initials from username.
     */
    public function initials(): string
    {
        return Str::of($this->username)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
}
