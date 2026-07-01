<?php

namespace App\Filament\Resources\PostResource\Concerns;

use App\Services\ImageCompressor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompressesCover
{
    /**
     * عکس آپلودشده را فشرده و بهینه می‌کند (زیر ۲۵۰ کیلوبایت، حداکثر ۱۲۰۰ پیکسل)
     * و مسیر جدید jpg را برمی‌گرداند.
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

        $source = $disk->path($path);
        $newPath = 'posts/' . Str::random(40) . '.jpg';
        $dest = $disk->path($newPath);

        if (! is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }

        ImageCompressor::compress($source, $dest, 250, 1200);

        // حذف فایل اصلی آپلودشده (اگر متفاوت است)
        if ($path !== $newPath) {
            $disk->delete($path);
        }

        return $newPath;
    }
}
