<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShowcaseCover extends Model
{
    protected $fillable = [
        'image', 'type', 'imdb_url', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getImageSrcAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}
