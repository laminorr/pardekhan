<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\PodcastService;

class PodcastController extends Controller
{
    /**
     * صفحه‌ی فهرست — دو بنر پادکست
     */
    public function index()
    {
        $podcasts = [];
        foreach (PodcastService::PODCASTS as $slug => $meta) {
            $data = PodcastService::episodes($slug, 50);
            $podcasts[] = [
                'slug'          => $slug,
                'default_title' => $meta['default_title'],
                'show'          => $data['show'],
                'count'         => count($data['episodes']),
            ];
        }

        return view('panel.podcast.index', [
            'podcasts' => $podcasts,
        ]);
    }

    /**
     * صفحه‌ی قسمت‌های یک پادکست مشخص
     */
    public function show(string $slug)
    {
        if (! PodcastService::isValidSlug($slug)) {
            return redirect()->route('panel.podcast');
        }

        $data = PodcastService::episodes($slug, 50);

        return view('panel.podcast.show', [
            'slug'         => $slug,
            'defaultTitle' => PodcastService::PODCASTS[$slug]['default_title'],
            'show'         => $data['show'],
            'episodes'     => $data['episodes'],
        ]);
    }
}
