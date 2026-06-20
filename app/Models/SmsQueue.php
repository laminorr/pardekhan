<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsQueue extends Model
{
    protected $table = 'sms_queue';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'params'  => 'array',
            'sent_at' => 'datetime',
        ];
    }
}
