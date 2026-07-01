<?php

namespace App\Filament\Resources\PostResource\Concerns;

use App\Services\ImageCompressor;
use Illuminate\Support\Facades\Storage;

class CompressesCover
{
    /**
     * عکس آپلودشده را در همان مسیر فشرده و بهینه می‌کند (مسیر بدون تغییر می‌ماند).
     * اگر فشرده‌سازی به هر دلیل شکست بخورد، فایل اصلی دست‌نخورده باقی می‌ماند.
     */
    public static function run(?string $path): ?string
    {
        if (! $path) {
            return $path;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            return $path;
        }

        try {
            $full = $disk->path($path);
            // فشرده‌سازی روی همان فایل (in-place). خروجی jpg در همان مسیر نوشته می‌شود.
            ImageCompressor::compress($full, $full, 250, 1200);
        } catch (\Throwable $e) {
            // در صورت خطا، فایل اصلی حفظ می‌شود
        }

        return $path;
    }
}
