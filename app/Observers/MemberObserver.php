<?php

namespace App\Observers;

use App\Models\Layer;
use App\Models\Member;

class MemberObserver
{
    /**
     * قبل از ذخیره: اگر امتیاز عوض شده، لایه را خودکار هماهنگ کن
     */
    public function saving(Member $member): void
    {
        // فقط برای اعضای تاییدشده و وقتی امتیاز تغییر کرده
        if ($member->status !== 'approved') return;
        if (! $member->isDirty('score')) return;

        $appropriateLayer = Layer::active()
            ->where('min_score', '<=', $member->score)
            ->orderByDesc('min_score')
            ->first();

        if ($appropriateLayer && $member->layer_id !== $appropriateLayer->id) {
            $member->layer_id = $appropriateLayer->id;
        }
    }
}
