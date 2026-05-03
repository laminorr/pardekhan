<?php

use App\Models\Comment;
use App\Models\Contact;
use App\Models\Episode;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

// Home — list of published episodes
Route::get('/', function () {
    $episodes = Episode::published()
        ->orderBy('episode_number', 'desc')
        ->get();

    return view('home', compact('episodes'));
})->name('home');




// Episodes archive
Route::get('/episodes', function (Request $request) {
    $allEpisodes = Episode::published()
        ->orderBy('episode_number', 'desc')
        ->get();

    $tags = $allEpisodes
        ->flatMap(fn ($episode) => $episode->meta_tags ?? [])
        ->filter()
        ->unique()
        ->values();

    $directors = $allEpisodes
        ->pluck('director')
        ->filter()
        ->unique()
        ->values();

    $levels = $allEpisodes
        ->pluck('meta_level')
        ->filter()
        ->unique()
        ->values();

    $q = trim((string) $request->query('q', ''));
    $director = trim((string) $request->query('director', ''));
    $level = trim((string) $request->query('level', ''));
    $selectedTags = array_values(array_filter((array) $request->query('tags', [])));

    $filteredEpisodes = $allEpisodes->filter(function ($episode) use ($q, $director, $level, $selectedTags) {
        $episodeTags = $episode->meta_tags ?? [];

        $matchesSearch = true;

        if ($q !== '') {
            $haystack = implode(' ', [
                $episode->title_fa,
                $episode->title_en,
                $episode->director,
                $episode->hero_lead,
                $episode->seo_description,
                implode(' ', $episodeTags),
            ]);

            $matchesSearch = mb_stripos($haystack, $q) !== false;
        }

        $matchesDirector = $director === '' || $episode->director === $director;
        $matchesLevel = $level === '' || $episode->meta_level === $level;
        $matchesTags = empty($selectedTags) || count(array_intersect($selectedTags, $episodeTags)) > 0;

        return $matchesSearch && $matchesDirector && $matchesLevel && $matchesTags;
    })->values();

    $page = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
    $perPage = 8;

    $episodes = new \Illuminate\Pagination\LengthAwarePaginator(
        $filteredEpisodes->forPage($page, $perPage)->values(),
        $filteredEpisodes->count(),
        $perPage,
        $page,
        [
            'path' => route('episodes.index'),
            'query' => $request->query(),
        ]
    );

    return view('episodes.index', compact(
        'episodes',
        'tags',
        'directors',
        'levels',
        'q',
        'director',
        'level',
        'selectedTags'
    ));
})->name('episodes.index');


// Tag page
Route::get('/tag/{tag}', function (string $tag) {
    $normalizedTag = str_replace('-', ' ', urldecode($tag));

    $allEpisodes = Episode::published()
        ->orderBy('episode_number', 'desc')
        ->get();

    $episodes = $allEpisodes->filter(function ($episode) use ($normalizedTag) {
        $episodeTags = $episode->meta_tags ?? [];

        return collect($episodeTags)->contains(function ($episodeTag) use ($normalizedTag) {
            $a = str_replace(['-', '‌', ' '], '', $episodeTag);
            $b = str_replace(['-', '‌', ' '], '', $normalizedTag);

            return $a === $b;
        });
    })->values();

    abort_if($episodes->isEmpty(), 404);

    $displayTag = $episodes
        ->flatMap(fn ($episode) => $episode->meta_tags ?? [])
        ->first(function ($episodeTag) use ($normalizedTag) {
            $a = str_replace(['-', '‌', ' '], '', $episodeTag);
            $b = str_replace(['-', '‌', ' '], '', $normalizedTag);

            return $a === $b;
        }) ?? $normalizedTag;

    return view('tags.show', compact('episodes', 'displayTag'));
})->name('tag.show');



// Topics archive
Route::get('/topics', function () {
    $topics = Topic::published()
        ->get();

    $episodes = Episode::published()
        ->orderBy('episode_number', 'desc')
        ->get();

    $topicCards = $topics->map(function ($topic) use ($episodes) {
        $featuredSlugs = $topic->featured_episode_slugs ?? [];
        $relatedTags = $topic->related_tags ?? [];

        $relatedEpisodes = $episodes->filter(function ($episode) use ($featuredSlugs, $relatedTags) {
            $episodeTags = $episode->meta_tags ?? [];

            return in_array($episode->slug, $featuredSlugs, true)
                || count(array_intersect($relatedTags, $episodeTags)) > 0;
        })->values();

        $topic->related_episodes_count = $relatedEpisodes->count();

        return $topic;
    });

    return view('topics.index', [
        'topics' => $topicCards,
    ]);
})->name('topics.index');


// Topic page
Route::get('/topic/{topic:slug}', function (Topic $topic) {
    if (! $topic->is_published) {
        abort(404);
    }

    $featuredSlugs = $topic->featured_episode_slugs ?? [];
    $relatedTags = $topic->related_tags ?? [];

    $allEpisodes = Episode::published()
        ->orderBy('episode_number', 'desc')
        ->get();

    $featuredEpisodes = $allEpisodes
        ->filter(fn ($episode) => in_array($episode->slug, $featuredSlugs, true))
        ->values();

    $tagEpisodes = $allEpisodes
        ->filter(function ($episode) use ($relatedTags, $featuredSlugs) {
            $episodeTags = $episode->meta_tags ?? [];

            return ! in_array($episode->slug, $featuredSlugs, true)
                && count(array_intersect($relatedTags, $episodeTags)) > 0;
        })
        ->values();

    $episodes = $featuredEpisodes
        ->merge($tagEpisodes)
        ->take(12)
        ->values();

    return view('topics.show', compact('topic', 'episodes'));
})->name('topics.show');


/*
|--------------------------------------------------------------------------
| SEO / Feeds
|--------------------------------------------------------------------------
| این routeها باید قبل از route داینامیک اپیزود باشند.
| چون /{episode:slug} هر چیزی مثل sitemap.xml را هم می‌گیرد.
*/


// Sitemap
Route::get('/sitemap.xml', function () {
    $episodes = Episode::published()
        ->orderBy('episode_number')
        ->get();

    $tags = $episodes
        ->flatMap(fn ($episode) => $episode->meta_tags ?? [])
        ->filter()
        ->unique()
        ->values();

    $topics = Topic::published()
        ->get();

    return response()
        ->view('sitemap', compact('episodes', 'tags', 'topics'))
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

// RSS Feed
Route::get('/feed.xml', function () {
    $episodes = Episode::published()
        ->orderBy('published_at', 'desc')
        ->get();

    return response()
        ->view('feed', compact('episodes'))
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('feed');

/*
|--------------------------------------------------------------------------
| Form Actions
|--------------------------------------------------------------------------
*/

// Comments
Route::post('/comment', function (Request $request) {
    $validated = $request->validate([
        'episode_id' => ['required', 'exists:episodes,id'],
        'name' => ['required', 'string', 'max:100'],
        'body' => ['required', 'string', 'max:2000'],
    ]);

    Comment::create([
        'episode_id' => $validated['episode_id'],
        'name' => $validated['name'],
        'body' => $validated['body'],
        'ip_address' => $request->ip(),
        'is_approved' => true,
    ]);

    return response()->json(['success' => true]);
})->name('comment.store');

// Subscribe phone
Route::post('/subscribe', function (Request $request) {
    $validated = $request->validate([
        'phone' => ['required', 'string', 'max:15'],
    ]);

    Contact::firstOrCreate([
        'phone' => $validated['phone'],
    ]);

    return response()->json(['success' => true]);
})->name('subscribe');

/*
|--------------------------------------------------------------------------
| Episode Page
|--------------------------------------------------------------------------
| این route باید آخر باشد چون wildcard است.
*/

// Episode page
Route::get('/{episode:slug}', function (Episode $episode) {
    if (! $episode->is_published) {
        abort(404);
    }

    $episode->load(['themes', 'lessons']);

    return view('episode', compact('episode'));
})->name('episode.show');