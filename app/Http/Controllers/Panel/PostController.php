<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('panel.posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        abort_unless($post->is_published, 404);

        // شمارش بازدید
        $post->increment('views');

        return view('panel.posts.show', compact('post'));
    }
}
