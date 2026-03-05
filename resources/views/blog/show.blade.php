@extends('layouts.public')

@section('title', $post->title . ' - Blog - ' . config('app.name', 'Laravel'))

@section('content')
    <section class="public-blog">
        <article class="blog-card blog-single">
            <div class="blog-meta blog-meta-top">
                <span class="blog-meta-left">
                    {{ $post->author->name ?? $post->author->email ?? 'Unknown author' }}
                </span>
                <span class="blog-meta-right">
                    @if ($post->published_at)
                        {{ \Illuminate\Support\Carbon::parse($post->published_at)->format('M j, Y') }}
                    @else
                        -
                    @endif
                </span>
            </div>

            <h1 class="blog-title">{{ $post->title }}</h1>

            @if ($post->featured_image_path)
                <figure class="blog-featured-image">
                    <img src="{{  Storage::url($post->featured_image_path) }}" alt="{{ $post->title }}">
                </figure>
            @endif

            <div class="blog-content">
                {!! $post->body !!}
            </div>

            <div class="blog-meta blog-meta-bottom">
                <span class="blog-meta-left">
                    @if ($post->categories->isNotEmpty())
                        Categories: {{ $post->categories->pluck('name')->implode(', ') }}
                    @else
                        Categories: -
                    @endif
                </span>
                <span class="blog-meta-right">
                    @if ($post->tags->isNotEmpty())
                        Tags: {{ $post->tags->pluck('name')->implode(', ') }}
                    @else
                        Tags: -
                    @endif
                </span>
            </div>
        </article>
    </section>

    <section class="blog-comments-section">
        <div class="blog-comments-card">
            <header class="blog-comments-header">
                <h2>Comments</h2>
                @if ($post->comments_enabled)
                    <p>{{ $post->comments->count() }} {{ \Illuminate\Support\Str::plural('comment', $post->comments->count()) }}</p>
                @else
                    <p>Comments are disabled for this post.</p>
                @endif
            </header>

            @if ($post->comments_enabled)
                @auth
                    <form method="POST" action="{{ route('blog.comments.store', $post) }}" class="blog-comment-form">
                        @csrf
                        <label for="comment-body">Add a comment</label>
                        <textarea
                            id="comment-body"
                            name="body"
                            rows="4"
                            required
                            maxlength="2000"
                            placeholder="Write your comment..."
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <p>{{ $message }}</p>
                        @enderror
                        <button type="submit">Post comment</button>
                    </form>
                @else
                    <p class="blog-comments-auth-note">
                        <a href="{{ route('login') }}">Log in</a> to post a comment.
                    </p>
                @endauth

                @if ($post->comments->isEmpty())
                    <p class="blog-comments-empty">No comments yet.</p>
                @else
                    <div class="blog-comments-list">
                        @foreach ($post->comments as $comment)
                            @php
                                $currentVote = null;

                                if (auth()->check()) {
                                    $currentVoteValue = $comment->votes->first()?->value;

                                    if ($currentVoteValue === 1) {
                                        $currentVote = 'like';
                                    } elseif ($currentVoteValue === -1) {
                                        $currentVote = 'dislike';
                                    }
                                }
                            @endphp
                            <article class="blog-comment-item">
                                <div class="blog-comment-meta">
                                    <strong>{{ $comment->author->name }}</strong>
                                    <span>{{ $comment->created_at->format('M j, Y H:i') }}</span>
                                </div>
                                <p>{!! nl2br(e($comment->body)) !!}</p>

                                <div
                                    class="blog-comment-footer"
                                    x-data="commentVote({
                                        canVote: @js(auth()->check()),
                                        voteUrl: @js(route('comments.vote', $comment)),
                                        csrfToken: @js(csrf_token()),
                                        likesCount: @js((int) $comment->likes_count),
                                        dislikesCount: @js((int) $comment->dislikes_count),
                                        currentVote: @js($currentVote),
                                    })"
                                >
                                    <div class="blog-comment-votes">
                                        <button
                                            type="button"
                                            class="blog-comment-vote-button"
                                            :class="{ 'is-active-like': currentVote === 'like' }"
                                            :disabled="!canVote || isSubmitting"
                                            @click="submitVote('like')"
                                            title="Like"
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M2 22h4V10H2v12Zm20-11c0-1.1-.9-2-2-2h-6.3l1-4.6.03-.32c0-.41-.17-.79-.44-1.06L13.17 2 6.59 8.59C6.22 8.95 6 9.45 6 10v10c0 1.1.9 2 2 2h9c.82 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2Z" fill="currentColor"/>
                                            </svg>
                                            <span x-text="likesCount"></span>
                                        </button>

                                        <button
                                            type="button"
                                            class="blog-comment-vote-button"
                                            :class="{ 'is-active-dislike': currentVote === 'dislike' }"
                                            :disabled="!canVote || isSubmitting"
                                            @click="submitVote('dislike')"
                                            title="Dislike"
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M15 3H6c-.82 0-1.54.5-1.84 1.22L1.14 11.27c-.09.23-.14.47-.14.73v2c0 1.1.9 2 2 2h6.3l-1 4.6-.03.32c0 .41.17.79.44 1.06L9.83 22l6.58-6.59c.37-.36.59-.86.59-1.41V4c0-1.1-.9-2-2-2Zm5 0h-4v12h4V3Z" fill="currentColor"/>
                                            </svg>
                                            <span x-text="dislikesCount"></span>
                                        </button>
                                    </div>

                                    <p class="blog-comment-vote-note" x-show="!canVote">
                                        <a href="{{ route('login') }}">Log in</a> to vote.
                                    </p>
                                    <p class="blog-comment-vote-error" x-show="errorMessage" x-text="errorMessage"></p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
