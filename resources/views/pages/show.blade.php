@extends('layouts.public')

@section('title', $page->title . ' - ' . config('app.name', 'Laravel'))

@section('content')
    <section class="public-blog">
        <article class="blog-card blog-single">
            <h1 class="blog-title">{{ $page->title }}</h1>

            <div class="blog-content">
                {!! nl2br(e($page->body)) !!}
            </div>
        </article>
    </section>
@endsection
