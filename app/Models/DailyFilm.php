<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyFilm extends Model
{
    protected $fillable = [
        'title', 'original_title', 'year', 'director', 'genre',
        'cover', 'cover_url', 'description', 'link', 'show_date', 'is_active',
    ];

    protected $casts = [
        'show_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * آدرس نهایی کاور (آپلودی یا لینک)
     */
    public function getCoverSrcAttribute(): ?string
    {
        if ($this->cover) {
            return \Illuminate\Support\Facades\Storage::url($this->cover);
        }
        return $this->cover_url ?: null;
    }
}
