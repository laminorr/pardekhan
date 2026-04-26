<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function getInitialAttribute(): string
    {
        return mb_substr($this->name, 0, 1);
    }

public function getTimeAgoAttribute(): string
    {
        $diff = (int) $this->created_at->diffInMinutes(now());
        if ($diff < 1) return 'همین الان';
        if ($diff < 60) return $diff . ' دقیقه پیش';
        if ($diff < 1440) return (int) floor($diff / 60) . ' ساعت پیش';
        if ($diff < 43200) return (int) floor($diff / 1440) . ' روز پیش';
        return (int) floor($diff / 43200) . ' ماه پیش';
    }
}
