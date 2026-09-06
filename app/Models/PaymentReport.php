<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReport extends Model
{
    protected $guarded = [];

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    /** برچسب‌های فارسی وضعیت */
    public const STATUS_LABELS = [
        self::STATUS_PENDING  => 'در انتظار',
        self::STATUS_APPROVED => 'تأیید شده',
        self::STATUS_REJECTED => 'رد شده',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    // ── Relations ──────────────────────────────────────
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    // ── Scopes ─────────────────────────────────────────
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // ── Helpers ────────────────────────────────────────
    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
