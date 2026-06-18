<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Role helpers ───────────────────────────────────
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isEventManager(): bool
    {
        return $this->role === 'event_manager';
    }

    public function isReception(): bool
    {
        return $this->role === 'reception';
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'super_admin'   => 'مدیر اصلی',
            'event_manager' => 'مدیر دورهمی',
            'reception'     => 'مسئول پذیرش',
            default         => 'نامشخص',
        };
    }
}
