<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    protected $fillable = [
        'title', 'cover', 'buy_url',
    ];

    public function getCoverSrcAttribute(): ?string
    {
        return $this->cover ? Storage::url($this->cover) : null;
    }

    // دورهمی‌ای که این کتاب در آن بررسی می‌شود
    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }
}
