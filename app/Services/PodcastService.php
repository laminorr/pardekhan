<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class PodcastService
{
    /**
     * خواندن فید RSS و استخراج قسمت‌ها (با کش ۳۰ دقیقه‌ای)
     */
    public static function episodes(int $limit = 50): array
    {
        $feedUrl = Setting::get('podcast_rss_url', '');
        if (! $feedUrl) {
            return ['show' => null, 'episodes' => []];
        }

        return Cache::remember('podcast_feed', now()->addMinutes(30), function () use ($feedUrl, $limit) {
            return self::parse($feedUrl, $limit);
        });
    }

    /**
     * پاک کردن کش (برای دکمه «به‌روزرسانی» در ادمین)
     */
    public static function clearCache(): void
    {
        Cache::forget('podcast_feed');
    }

    private static function parse(string $feedUrl, int $limit): array
    {
        try {
            $context = stream_context_create([
                'http' => ['timeout' => 8, 'user_agent' => 'PardekhanBot/1.0'],
                'https' => ['timeout' => 8],
            ]);
            $raw = @file_get_contents($feedUrl, false, $context);
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

                // کاور قسمت (اگر نبود، کاور پادکست)
                $epImage = $showImage;
                if (isset($itItunes->image)) {
                    $imgAttrs = $itItunes->image->attributes();
                    $epImage = (string) ($imgAttrs['href'] ?? $showImage);
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
