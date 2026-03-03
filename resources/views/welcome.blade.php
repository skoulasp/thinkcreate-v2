@extends('layouts.public')

@section('title', 'Welcome | ' . config('app.name', 'Laravel'))

@section('content')
    <section class="public-welcome">
        <header>
            <h1>{{ config('app.name', 'Laravel') }} CMS</h1>
            <p>
                A streamlined content management system for publishing pages, blog posts, and navigation from a single admin area.
            </p>
        </header>

        <section aria-labelledby="welcome-features-heading">
            <h2 id="welcome-features-heading">What You Can Manage</h2>
            <p>
                Create and edit static pages, organize blog posts with categories and tags, and control public navigation through menu locations.
                Website behavior like homepage source and blog pagination is configurable from admin settings.
            </p>
        </section>

        <section aria-labelledby="welcome-tech-heading">
            <h2 id="welcome-tech-heading">Technology Stack</h2>
            <p>
                Built with Laravel 12, Blade templates, Eloquent models, policies for admin authorization, and Vite-powered asset compilation.
                Styling is handled with SCSS, and the architecture keeps admin and public concerns clearly separated.
            </p>
        </section>
    </section>
@endsection
