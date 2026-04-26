#!/bin/bash
cd ~/pardekhan

echo "=== ۱. کپی فایل‌ها ==="
cp database/migrations/2026_04_19_000002_add_cover_image_to_episodes.php database/migrations/ 2>/dev/null
cp resources/views/home.blade.php resources/views/
echo "  done"

echo "=== ۲. اضافه کردن CSS ==="
cat home.css >> public/css/pardekhan.css
echo "  done"

echo "=== ۳. اضافه کردن فیلد cover_image به پنل ==="
if ! grep -q "cover_image" app/Filament/Resources/EpisodeResource.php; then
sed -i "/aparat_hash/a\\
                        Forms\\\Components\\\FileUpload::make('cover_image')\\
                            ->label('تصویر کاور')\\
                            ->image()\\
                            ->directory('covers')\\
                            ->helperText('تصویر عمودی با نسبت 3:4 — پیشنهاد: 600x800 پیکسل')," app/Filament/Resources/EpisodeResource.php
echo "  done"
else
echo "  skip - already exists"
fi

echo "=== ۴. لینک storage ==="
php artisan storage:link 2>/dev/null
echo "  done"

echo "=== ۵. Migration ==="
php artisan migrate --force
echo "  done"

echo "=== ۶. پاکسازی ==="
php artisan optimize:clear

echo ""
echo "========================================="
echo "  done! home page ready"
echo "  pardekhan.ir"
echo "  pardekhan.ir/admin/episodes  ->  edit cover"
echo "========================================="
