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
                            <article class="blog-comment-item">
                                <div class="blog-comment-meta">
                                    <strong>{{ $comment->author->name }}</strong>
                                    <span>{{ $comment->created_at->format('M j, Y H:i') }}</span>
                                </div>
                                <p>{!! nl2br(e($comment->body)) !!}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
