<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $guarded = [];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'recharge'   => 'شارژ',
            'payment'    => 'پرداخت',
            'refund'     => 'بازگشت وجه',
            'adjustment' => 'اصلاح',
            default      => $this->type,
        };
    }
}
