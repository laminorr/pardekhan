<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'gateway'      => 'درگاه بانکی',
            'card_to_card' => 'کارت به کارت',
            'wallet'       => 'کیف پول',
            default        => $this->method,
        };
    }
}
