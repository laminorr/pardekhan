<?php

use App\Models\Comment;
use App\Models\Contact;
use App\Models\Episode;
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

    return response()
        ->view('sitemap', compact('episodes'))
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