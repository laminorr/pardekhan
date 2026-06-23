<?php

namespace App\Services;

class ImageCompressor
{
    /**
     * فشرده‌سازی و ریسایز تصویر به زیر حجم هدف (پیش‌فرض ۱۵۰ کیلوبایت)
     * با استفاده از GD (بدون نیاز به پکیج خارجی)
     *
     * @param string $sourcePath مسیر فایل اصلی
     * @param string $destPath   مسیر ذخیره خروجی (jpg)
     * @param int    $maxKb      حداکثر حجم به کیلوبایت
     * @param int    $maxDim     حداکثر ابعاد (عرض/ارتفاع) به پیکسل
     * @return bool
     */
    public static function compress(string $sourcePath, string $destPath, int $maxKb = 150, int $maxDim = 800): bool
    {
        if (! function_exists('imagecreatefromjpeg')) {
            // GD در دسترس نیست — فایل را همان‌طور کپی کن
            return copy($sourcePath, $destPath);
        }

        $info = @getimagesize($sourcePath);
        if (! $info) {
            return copy($sourcePath, $destPath);
        }

        [$width, $height] = $info;
        $mime = $info['mime'];

        // بارگذاری تصویر بر اساس نوع
        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png'  => @imagecreatefrompng($sourcePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null,
            default      => null,
        };

        if (! $src) {
            return copy($sourcePath, $destPath);
        }

        // محاسبه ابعاد جدید (حفظ نسبت)
        $scale = min(1, $maxDim / max($width, $height));
        $newW = (int) round($width * $scale);
        $newH = (int) round($height * $scale);

        $dst = imagecreatetruecolor($newW, $newH);

        // پس‌زمینه سفید (برای png شفاف)
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

        // کاهش تدریجی کیفیت تا رسیدن به حجم هدف
        $quality = 85;
        $maxBytes = $maxKb * 1024;
        do {
            ob_start();
            imagejpeg($dst, null, $quality);
            $data = ob_get_clean();
            $size = strlen($data);
            $quality -= 8;
        } while ($size > $maxBytes && $quality > 25);

        file_put_contents($destPath, $data);

        imagedestroy($src);
        imagedestroy($dst);

        return true;
    }
}
