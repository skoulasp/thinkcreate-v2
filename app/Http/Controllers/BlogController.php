<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = Setting::getIntValue('website.blog.posts_per_page', 5, 1, 50);
        $search = trim((string) $request->query('q', ''));

        $posts = Post::query()
            ->with(['author', 'categories', 'tags'])
            ->withCount('comments')
            ->published()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'like', '%' . $search . '%')
                        ->orWhere('excerpt', 'like', '%' . $search . '%')
                        ->orWhere('body', 'like', '%' . $search . '%');
                });
            })
            ->latest('published_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('blog.index', compact('posts', 'search'));
    }

    public function show(Request $request, Post $post): View
    {
        abort_unless(
            Post::query()->published()->whereKey($post->getKey())->exists(),
            404
        );

        $currentUserId = $request->user()?->id;

        $post->load([
            'author',
            'categories',
            'tags',
            'comments' => fn ($query) => $query
                ->with('author')
                ->withCount([
                    'votes as likes_count' => fn ($voteQuery) => $voteQuery->where('value', 1),
                    'votes as dislikes_count' => fn ($voteQuery) => $voteQuery->where('value', -1),
                ])
                ->with([
                    'votes' => fn ($voteQuery) => $voteQuery
                        ->when(
                            $currentUserId,
                            fn ($subQuery) => $subQuery->where('user_id', $currentUserId),
                            fn ($subQuery) => $subQuery->whereRaw('1 = 0')
                        ),
                ])
                ->latest(),
        ]);

        return view('blog.show', compact('post'));
    }
}
