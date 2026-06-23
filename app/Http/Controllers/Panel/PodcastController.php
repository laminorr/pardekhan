<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\PodcastService;

class PodcastController extends Controller
{
    public function index()
    {
        $data = PodcastService::episodes(50);

        return view('panel.podcast.index', [
            'show'     => $data['show'],
            'episodes' => $data['episodes'],
        ]);
    }
}
