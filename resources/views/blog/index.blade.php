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
                @endforeach
            </div>

            <div class="blog-pagination">
                {{ $posts->links() }}
            </div>
        @endif
    </section>
@endsection
