<?php
// Add this to the END of routes/web.php

use App\Models\Comment;
use Illuminate\Http\Request;

Route::post('/comment', function (Request $request) {
    $validated = $request->validate([
        'episode_id' => 'required|exists:episodes,id',
        'name' => 'required|string|max:100',
        'body' => 'required|string|max:2000',
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
