@extends('layouts.public')

@section('title', 'Blog - ' . config('app.name', 'Laravel'))

@section('content')
    <form class="blog-search" method="GET" action="{{ route('blog.index') }}">
        <label class="blog-search-label" for="blog-search-input">Search posts</label>
        <div class="blog-search-controls">
            <input
                id="blog-search-input"
                name="q"
                type="search"
                value="{{ $search }}"
                placeholder="Search by title or content"
                autocomplete="off"
            >
            <button type="submit">Search</button>
            @if ($search !== '')
                <a class="blog-search-reset" href="{{ route('blog.index') }}">Clear</a>
            @endif
        </div>
    </form>

    <section class="public-blog">
        <header class="blog-header">
            <h1>Blog</h1>
            @if ($search !== '')
                <p class="results">Results for "{{ $search }}".</p>
            @else
                <p>Recent published posts.</p>
            @endif
        </header>

        @if ($posts->isEmpty())
            @if ($search !== '')
                <p class="blog-empty">No posts found for "{{ $search }}".</p>
            @else
                <p class="blog-empty">No published posts yet.</p>
            @endif
        @else
            <div class="blog-list">
                @foreach ($posts as $post)
                    <article class="blog-card">
                        <div class="blog-meta blog-meta-top">
                            <span class="blog-meta-left blog-meta-item">
                                <svg class="blog-meta-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.42 0-8 2.01-8 4.5V20h16v-1.5c0-2.49-3.58-4.5-8-4.5Z" fill="currentColor"/>
                                </svg>
                                {{ $post->author->name ?? $post->author->email ?? 'Unknown author' }}
                            </span>
                            <span class="blog-meta-right blog-meta-inline">
                                @if ($post->comments_enabled)
                                    <span class="blog-meta-item" title="{{ $post->comments_count }} {{ \Illuminate\Support\Str::plural('comment', $post->comments_count) }}">
                                        <svg class="blog-meta-icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 4c-4.97 0-9 3.13-9 7s4.03 7 9 7c.73 0 1.44-.07 2.12-.2L19.6 20.8l-1.92-3.72C19.81 15.73 21 13.54 21 11c0-3.87-4.03-7-9-7Z" fill="currentColor"/>
                                        </svg>
                                        {{ $post->comments_count }}
                                    </span>
                                @endif
                                <span class="blog-meta-item">
                                    <svg class="blog-meta-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 11h-5V7h2v4h4Z" fill="currentColor"/>
                                    </svg>
                                    @if ($post->published_at)
                                        {{ \Illuminate\Support\Carbon::parse($post->published_at)->format('M j, Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </span>
                        </div>

                        <h2 class="blog-title">
                            <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                        </h2>

                        <p class="blog-excerpt">
                            @if ($post->excerpt)
                                {{ $post->excerpt }}
                            @else
                                {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 220) }}
                            @endif
                        </p>

                        <div class="blog-meta blog-meta-bottom">
                            <span class="blog-meta-left blog-meta-item">
                                <svg class="blog-meta-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M10 3H3v7h7Zm0 11H3v7h7ZM21 3h-7v7h7Zm0 11h-7v7h7Z" fill="currentColor"/>
                                </svg>
                                @if ($post->categories->isNotEmpty())
                                    {{ $post->categories->pluck('name')->implode(', ') }}
                                @else
                                    -
                                @endif
                            </span>
                            <span class="blog-meta-right">
                                @if ($post->tags->isNotEmpty())
                                    {{ $post->tags->pluck('name')->map(fn ($name) => "#{$name}")->implode(' ') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>


        @endif
    </section>
    
    <div class="blog-pagination">
       {{ $posts->links() }}
    </div>
@endsection
