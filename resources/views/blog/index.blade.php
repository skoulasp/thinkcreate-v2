@extends('layouts.public')

@section('title', 'Blog - ' . config('app.name', 'Laravel'))

@section('content')
    <section class="public-blog">
        <header class="blog-header">
            <h1>Blog</h1>
            <p>Recent published posts.</p>
        </header>

        @if ($posts->isEmpty())
            <p class="blog-empty">No published posts yet.</p>
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
