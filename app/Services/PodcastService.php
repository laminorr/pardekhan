<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class PodcastService
{
    /**
     * پادکست‌های تعریف‌شده — slug ⇐⇒ کلید تنظیمات فید و عنوان پیش‌فرض
     */
    public const PODCASTS = [
        'uncertainty' => [
            'setting_key'   => 'podcast_rss_url',           // سازگاری با گذشته — تغییرش نده
            'default_title' => 'عدم قطعیت',
        ],
        'hegemony' => [
            'setting_key'   => 'podcast_rss_url_hegemony',
            'default_title' => 'هژمونی',
        ],
    ];

    /**
     * بررسی معتبر بودن slug پادکست
     */
    public static function isValidSlug(string $slug): bool
    {
        return array_key_exists($slug, self::PODCASTS);
    }

    /**
     * تبدیل مدت‌زمان (ثانیه یا HH:MM:SS) به فرمت خوانا
     */
    public static function humanDuration(string $duration): string
    {
        $duration = trim($duration);
        if ($duration === '') return '';

        // اگر عدد خالص بود (ثانیه)
        if (is_numeric($duration)) {
            $sec = (int) $duration;
            $m = intdiv($sec, 60);
            $s = $sec % 60;
            return $m . ':' . str_pad((string) $s, 2, '0', STR_PAD_LEFT);
        }

        // اگر HH:MM:SS بود، ساعت صفر را حذف کن
        $parts = explode(':', $duration);
        if (count($parts) === 3 && (int) $parts[0] === 0) {
            return ltrim($parts[1], '0') ?: '0' . ':' . $parts[2];
        }
        return $duration;
    }

    /**
     * خواندن فید RSS و استخراج قسمت‌ها (با کش ۳۰ دقیقه‌ای) — به‌ازای هر پادکست
     */
    public static function episodes(string $slug = 'uncertainty', int $limit = 50): array
    {
        if (! self::isValidSlug($slug)) {
            return ['show' => null, 'episodes' => []];
        }

        $feedUrl = Setting::get(self::PODCASTS[$slug]['setting_key'], '');
        if (! $feedUrl) {
            return ['show' => null, 'episodes' => []];
        }

        // کش به‌ازای هر پادکست تا فیدها روی هم بازنویسی نشوند
        return Cache::remember("podcast_feed_{$slug}", now()->addMinutes(30), function () use ($feedUrl, $limit) {
            return self::parse($feedUrl, $limit);
        });
    }

    /**
     * پاک کردن کش هر دو پادکست (برای دکمه «به‌روزرسانی» در ادمین)
     */
    public static function clearCache(): void
    {
        foreach (array_keys(self::PODCASTS) as $slug) {
            Cache::forget("podcast_feed_{$slug}");
        }
    }

    /**
     * دانلود محتوای فید — اول cURL (مطمئن‌تر روی هاست اشتراکی)، بعد file_get_contents
     */
    private static function fetch(string $url): ?string
    {
        // روش ۱: cURL
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_TIMEOUT        => 12,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; PardekhanBot/1.0)',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_ENCODING       => '',
            ]);
            $data = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($data !== false && $code >= 200 && $code < 400 && strlen($data) > 0) {
                return $data;
            }
        }

        // روش ۲: file_get_contents (پشتیبان)
        $context = stream_context_create([
            'http'  => ['timeout' => 10, 'user_agent' => 'Mozilla/5.0 (compatible; PardekhanBot/1.0)', 'follow_location' => 1],
            'https' => ['timeout' => 10],
            'ssl'   => ['verify_peer' => false, 'verify_peer_name' => false],
        ]);
        $data = @file_get_contents($url, false, $context);

        return $data !== false ? $data : null;
    }

    private static function parse(string $feedUrl, int $limit): array
    {
        try {
            $raw = self::fetch($feedUrl);
            if (! $raw) {
                return ['show' => null, 'episodes' => []];
            }

            $xml = @simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (! $xml || ! isset($xml->channel)) {
                return ['show' => null, 'episodes' => []];
            }

            $channel = $xml->channel;
            $itunes = $channel->children('http://www.itunes.com/dtds/podcast-1.0.dtd');

            // کاور کلی پادکست
            $showImage = '';
            if (isset($itunes->image)) {
                $attrs = $itunes->image->attributes();
                $showImage = (string) ($attrs['href'] ?? '');
            }
            if (! $showImage && isset($channel->image->url)) {
                $showImage = (string) $channel->image->url;
            }

            $show = [
                'title'       => (string) $channel->title,
                'description' => (string) $channel->description,
                'image'       => $showImage,
                'link'        => (string) $channel->link,
            ];

            $episodes = [];
            $count = 0;
            foreach ($channel->item as $item) {
                if ($count >= $limit) break;

                $itItunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');

                // فایل صوتی
                $audioUrl = '';
                if (isset($item->enclosure)) {
                    $encAttrs = $item->enclosure->attributes();
                    $audioUrl = (string) ($encAttrs['url'] ?? '');
                }

                // کاور قسمت — از چند منبع ممکن (در نهایت کاور پادکست)
                $epImage = '';
                // ۱) itunes:image href
                if (isset($itItunes->image)) {
                    $imgAttrs = $itItunes->image->attributes();
                    $epImage = (string) ($imgAttrs['href'] ?? '');
                }
                // ۲) تگ image معمولی (متن یا url)
                if (! $epImage && isset($item->image)) {
                    if (isset($item->image->url)) {
                        $epImage = (string) $item->image->url;
                    } else {
                        $imgAttrs = $item->image->attributes();
                        $epImage = (string) ($imgAttrs['href'] ?? trim((string) $item->image));
                    }
                }
                // ۳) media:thumbnail یا media:content
                if (! $epImage) {
                    $media = $item->children('http://search.yahoo.com/mrss/');
                    if (isset($media->thumbnail)) {
                        $epImage = (string) $media->thumbnail->attributes()['url'];
                    } elseif (isset($media->content)) {
                        $epImage = (string) $media->content->attributes()['url'];
                    }
                }
                // ۴) در نهایت، کاور پادکست
                if (! $epImage) {
                    $epImage = $showImage;
                }

                $episodes[] = [
                    'title'       => (string) $item->title,
                    'description' => trim(strip_tags((string) $item->description)),
                    'audio'       => $audioUrl,
                    'image'       => $epImage,
                    'link'        => (string) $item->link,
                    'duration'    => (string) ($itItunes->duration ?? ''),
                    'pubDate'     => (string) $item->pubDate,
                ];
                $count++;
            }

            return ['show' => $show, 'episodes' => $episodes];
        } catch (\Throwable $e) {
            return ['show' => null, 'episodes' => []];
        }
    }
}
