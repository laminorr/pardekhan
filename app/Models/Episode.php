<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Episode extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'essay_paragraphs' => 'array',
            'essay_after_paragraphs' => 'array',
            'meta_tags' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class)->orderBy('sort_order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }
    
    public function comments(): HasMany
    {
        return $this->hasMany(\App\Models\Comment::class)->where('is_approved', true)->latest();
    }
    
    

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getUrlAttribute(): string
    {
        return url($this->slug);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
