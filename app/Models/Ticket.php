<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['used_at' => 'datetime'];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'PK-' . strtoupper(Str::random(10));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'active'    => 'معتبر',
            'used'      => 'استفاده شده',
            'cancelled' => 'باطل شده',
            default     => $this->status,
        };
    }
}
