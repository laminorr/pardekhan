<?php

namespace App\Services;

use App\Models\Layer;
use App\Models\Member;
use App\Models\ScoreLog;
use App\Models\ScoreSetting;

class ScoreService
{
    /**
     * اضافه کردن امتیاز بر اساس کلید رفتار
     */
    public function addByKey(
        Member $member,
        string $key,
        ?int $adminId = null,
        ?string $adminNote = null,
        mixed $related = null
    ): void {
        $setting = ScoreSetting::where('key', $key)->first();
        if (! $setting) return;

        $this->apply($member, $setting->points, $setting->label, $key, $adminId, $adminNote, $related);
    }

    /**
     * اضافه کردن امتیاز دستی
     */
    public function addManual(
        Member $member,
        int $points,
        string $label,
        int $adminId,
        ?string $adminNote = null
    ): void {
        $this->apply($member, $points, $label, 'manual_adjust', $adminId, $adminNote);
    }

    /**
     * اعمال امتیاز و بررسی ارتقا/تنزل لایه
     */
    private function apply(
        Member $member,
        int $points,
        string $label,
        string $key,
        ?int $adminId = null,
        ?string $adminNote = null,
        mixed $related = null
    ): void {
        // آپدیت امتیاز
        $newScore = $member->score + $points;
        $member->update(['score' => $newScore]);

        // ثبت لاگ
        $log = [
            'member_id'    => $member->id,
            'reason_key'   => $key,
            'reason_label' => $label,
            'points'       => $points,
            'score_after'  => $newScore,
            'admin_id'     => $adminId,
            'admin_note'   => $adminNote,
        ];

        if ($related) {
            $log['related_type'] = get_class($related);
            $log['related_id']   = $related->id;
        }

        ScoreLog::create($log);

        // بررسی ارتقا/تنزل لایه
        $this->checkLayerChange($member->fresh());
    }

    /**
     * بررسی و اعمال تغییر لایه بر اساس امتیاز
     */
    public function checkLayerChange(Member $member): void
    {
        if ($member->status !== 'approved') return;

        $appropriateLayer = Layer::active()
            ->where('min_score', '<=', $member->score)
            ->orderByDesc('min_score')
            ->first();

        $newLayerId = $appropriateLayer?->id;

        if ($member->layer_id !== $newLayerId) {
            $member->update(['layer_id' => $newLayerId]);
        }
    }
}
