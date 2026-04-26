<?php

use App\Models\Episode;
use Illuminate\Support\Facades\Route;

// Home — list of episodes
Route::get('/', function () {
    $episodes = Episode::published()
        ->orderBy('episode_number', 'desc')
        ->get();
    return view('home', compact('episodes'));
})->name('home');

// Episode page
Route::get('/{episode:slug}', function (Episode $episode) {
    if (! $episode->is_published) {
        abort(404);
    }
    $episode->load(['themes', 'lessons']);
    return view('episode', compact('episode'));
})->name('episode.show');

// Sitemap
Route::get('/sitemap.xml', function () {
    $episodes = Episode::published()->orderBy('episode_number')->get();
    return response()->view('sitemap', compact('episodes'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

// RSS Feed
Route::get('/feed.xml', function () {
    $episodes = Episode::published()->orderBy('published_at', 'desc')->get();
    return response()->view('feed', compact('episodes'))
        ->header('Content-Type', 'application/xml; charset=utf-8');
})->name('feed');
// Comments
Route::post('/comment', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'episode_id' => 'required|exists:episodes,id',
        'name' => 'required|string|max:100',
        'body' => 'required|string|max:2000',
    ]);

    \App\Models\Comment::create([
        'episode_id' => $validated['episode_id'],
        'name' => $validated['name'],
        'body' => $validated['body'],
        'ip_address' => $request->ip(),
        'is_approved' => true,
    ]);

    return response()->json(['success' => true]);
})->name('comment.store');
// Subscribe phone
Route::post('/subscribe', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'phone' => 'required|string|max:15',
    ]);
    \App\Models\Contact::firstOrCreate(['phone' => $validated['phone']]);
    return response()->json(['success' => true]);
})->name('subscribe');
