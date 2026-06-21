<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Member;
use App\Models\Layer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // همگام‌سازی خودکار لایه با امتیاز (روی هر مسیری: فرم، کد، tinker)
        Member::saving(function (Member $member) {
            if ($member->status !== 'approved') return;
            if (! $member->isDirty('score')) return;

            $correct = Layer::where('is_active', true)
                ->where('min_score', '<=', (int) $member->score)
                ->orderByDesc('min_score')
                ->first();

            if ($correct) {
                $member->layer_id = $correct->id;
            }
        });
    }
}
