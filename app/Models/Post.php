<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $fillable = [
        'title', 'author', 'excerpt', 'body', 'cover',
        'is_published', 'published_at', 'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getCoverSrcAttribute(): ?string
    {
        return $this->cover ? Storage::url($this->cover) : null;
    }

    // خلاصه‌ی خودکار اگر خالی باشد
    public function getSummaryAttribute(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }
        return \Illuminate\Support\Str::limit(trim(strip_tags($this->body)), 120);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
