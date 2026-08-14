<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TheaterReview extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'cover',
        'body',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // ساخت خودکار اسلاگ از عنوان؛ برای فارسی که Str::slug خالی برمی‌گرداند،
        // به یک اسلاگ تصادفی برمی‌گردیم و یکتایی را تضمین می‌کنیم.
        static::saving(function (TheaterReview $review) {
            if (! empty($review->slug)) {
                return;
            }

            $base = Str::slug($review->title);
            if ($base === '') {
                $base = 'theater-' . Str::random(8);
            }

            $slug = $base;
            $i = 1;
            while (static::where('slug', $slug)
                ->where('id', '!=', $review->id)
                ->exists()) {
                $slug = $base . '-' . $i++;
            }

            $review->slug = $slug;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
