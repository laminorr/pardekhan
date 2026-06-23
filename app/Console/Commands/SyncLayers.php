<?php

namespace App\Console\Commands;

use App\Models\Layer;
use App\Models\Member;
use Illuminate\Console\Command;

class SyncLayers extends Command
{
    protected $signature = 'pardekhan:sync-layers';
    protected $description = 'همگام‌سازی لایهٔ همهٔ اعضا با امتیازشان';

    public function handle(): int
    {
        $layers = Layer::active()->get();
        $fixed = 0;

        Member::where('status', 'approved')->chunk(100, function ($members) use ($layers, &$fixed) {
            foreach ($members as $member) {
                // بزرگ‌ترین لایه‌ای که آستانه‌اش ≤ امتیاز عضو است (مستقل از ترتیب کالکشن)
                $correct = $layers
                    ->filter(fn ($l) => $l->min_score <= $member->score)
                    ->sortByDesc('min_score')
                    ->first();

                if ($correct && $member->layer_id !== $correct->id) {
                    $member->updateQuietly(['layer_id' => $correct->id]);
                    $this->line("«{$member->full_name}»: امتیاز {$member->score} → لایهٔ {$correct->name}");
                    $fixed++;
                }
            }
        });

        $this->info("تمام شد. {$fixed} عضو اصلاح شد.");
        return self::SUCCESS;
    }
}
