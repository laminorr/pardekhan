<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layer extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'has_exclusive_events'     => 'boolean',
            'has_special_invitations'  => 'boolean',
            'is_active'                => 'boolean',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
