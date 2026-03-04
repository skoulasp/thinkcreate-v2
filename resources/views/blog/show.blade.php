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
@endsection
