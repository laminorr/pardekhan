<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Theme extends Model
{
    protected $guarded = [];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
