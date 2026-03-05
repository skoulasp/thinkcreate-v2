<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        abort_unless(
            Post::query()->published()->whereKey($post->getKey())->exists(),
            404
        );

        abort_unless($post->comments_enabled, 404);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return back()->with('success', 'Comment posted successfully.');
    }
}
