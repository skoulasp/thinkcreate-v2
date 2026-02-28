<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->with(['author', 'categories', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }

    public function show(Post $post): View
    {
        abort_unless(
            Post::query()->published()->whereKey($post->getKey())->exists(),
            404
        );

        $post->loadMissing(['author', 'categories', 'tags']);

        return view('blog.show', compact('post'));
    }
}
