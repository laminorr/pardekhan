<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    protected $hidden = ['password', 'remember_token', 'otp_code'];

    protected function casts(): array
    {
        return [
            'password'         => 'hashed',
            'otp_expires_at'   => 'datetime',
            'otp_locked_until' => 'datetime',
            'birth_date'       => 'date',
            'avatar_approved'  => 'boolean',
            'profile_completed'=> 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────
    public function layer(): BelongsTo
    {
        return $this->belongsTo(Layer::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MemberMessage::class)->latest();
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function hasWalletDebt(): bool
    {
        return $this->wallet_balance < 0;
    }

    // ── Helpers ────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending_review', 'needs_more_info']);
    }

    // ── OTP ────────────────────────────────────────────
    public function isOtpValid(string $code): bool
    {
        return $this->otp_code
            && \Illuminate\Support\Facades\Hash::check($code, $this->otp_code)
            && $this->otp_expires_at
            && $this->otp_expires_at->isFuture();
    }

    public function isOtpLocked(): bool
    {
        return $this->otp_locked_until && $this->otp_locked_until->isFuture();
    }
}
