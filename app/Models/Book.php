<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    protected $fillable = [
        'cover', 'buy_url',
    ];

    public function getCoverSrcAttribute(): ?string
    {
        return $this->cover ? Storage::url($this->cover) : null;
    }
}
