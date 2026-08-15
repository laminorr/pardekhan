<?php

namespace App\Filament\Resources\ShowcaseCoverResource\Concerns;

use App\Services\ImageCompressor;
use Illuminate\Support\Facades\Storage;

class CompressesCover
{
    /**
     * کاور ویترین آپلودشده را در همان مسیر فشرده و بهینه می‌کند (مسیر بدون تغییر می‌ماند).
     * این‌ها تصاویر کوچک تزئینی هستند؛ پس سبک نگه داشته می‌شوند (~۱۲۰KB / ~۶۰۰px).
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
            ImageCompressor::compress($full, $full, 120, 600);
        } catch (\Throwable $e) {
            // در صورت خطا، فایل اصلی حفظ می‌شود
        }

        return $path;
    }
}
