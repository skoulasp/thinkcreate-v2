<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentVote;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentVoteController extends Controller
{
    public function store(Request $request, Comment $comment): JsonResponse
    {
        $validated = $request->validate([
            'vote' => ['required', 'in:like,dislike'],
        ]);

        abort_unless(
            Post::query()
                ->published()
                ->whereKey($comment->post_id)
                ->exists(),
            404
        );

        $userId = $request->user()->id;
        $requestedValue = $validated['vote'] === 'like' ? 1 : -1;

        $currentVoteValue = DB::transaction(function () use ($comment, $userId, $requestedValue) {
            $existingVote = CommentVote::query()
                ->where('comment_id', $comment->id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($existingVote && $existingVote->value === $requestedValue) {
                $existingVote->delete();

                return 0;
            }

            CommentVote::query()->updateOrCreate(
                [
                    'comment_id' => $comment->id,
                    'user_id' => $userId,
                ],
                [
                    'value' => $requestedValue,
                ]
            );

            return $requestedValue;
        });

        $likesCount = CommentVote::query()
            ->where('comment_id', $comment->id)
            ->where('value', 1)
            ->count();

        $dislikesCount = CommentVote::query()
            ->where('comment_id', $comment->id)
            ->where('value', -1)
            ->count();

        return response()->json([
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
            'current_vote' => $currentVoteValue === 1 ? 'like' : ($currentVoteValue === -1 ? 'dislike' : null),
        ]);
    }
}
