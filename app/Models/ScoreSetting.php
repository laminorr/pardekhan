<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreSetting extends Model
{
    protected $guarded = [];

    public static function getPoints(string $key): int
    {
        return static::where('key', $key)->value('points') ?? 0;
    }
}
