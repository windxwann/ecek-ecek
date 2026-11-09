<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PEMILIK = 'pemilik';
    public const ROLE_PENYEWA = 'penyewa';

    protected $fillable = [
        'nama',
        'email',
        'no_tlpn',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function motors(): HasMany
    {
        return $this->hasMany(Motor::class, 'pemilik_id');
    }

    public function penyewaans(): HasMany
    {
        return $this->hasMany(Penyewaan::class, 'penyewa_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPemilik(): bool
    {
        return $this->role === self::ROLE_PEMILIK;
    }

    public function isPenyewa(): bool
    {
        return $this->role === self::ROLE_PENYEWA;
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => route('admin.dashboard'),
            self::ROLE_PEMILIK => route('owner.dashboard'),
            default => route('tenant.dashboard'),
        };
    }
}
