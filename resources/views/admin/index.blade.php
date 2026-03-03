@extends('layouts.admin')

@section('title', 'Admin Dashboard - ' . config('app.name'))

@section('content')
    <section class="admin-page">
        <header class="admin-page-header">
            <h1>Dashboard</h1>
            <p>Here you can create, edit or delete posts, pages, categories, and tags.</p>
        </header>

        <section class="admin-dashboard" aria-label="Admin sections">
            <article class="admin-card">
                <h2>Posts</h2>
                <div class="admin-actions">
                    <a href="{{ route('admin.posts.create') }}">Create new</a>
                    <a href="{{ route('admin.posts.index') }}">View all</a>
                </div>
            </article>

            <article class="admin-card">
                <h2>Pages</h2>
                <div class="admin-actions">
                    <a href="{{ route('admin.pages.create') }}">Create new</a>
                    <a href="{{ route('admin.pages.index') }}">View all</a>
                </div>
            </article>

            <article class="admin-card">
                <h2>Categories</h2>
                <div class="admin-actions">
                    <a href="{{ route('admin.categories.create') }}">Create new</a>
                    <a href="{{ route('admin.categories.index') }}">View all</a>
                </div>
            </article>

            <article class="admin-card">
                <h2>Tags</h2>
                <div class="admin-actions">
                    <a href="{{ route('admin.tags.create') }}">Create new</a>
                    <a href="{{ route('admin.tags.index') }}">View all</a>
                </div>
            </article>
        </section>
    </section>

    <section class="admin-page admin-stats-section">
        <header class="admin-page-header">
            <h2>Website Stats</h2>
            <p>Quick overview of your current content and registered members.</p>
        </header>

        <section class="admin-stats-grid" aria-label="Website statistics">
            <article class="admin-stat-card">
                <p class="admin-stat-label">Total Posts</p>
                <p class="admin-stat-value">{{ number_format($stats['posts']) }}</p>
            </article>

            <article class="admin-stat-card">
                <p class="admin-stat-label">Total Pages</p>
                <p class="admin-stat-value">{{ number_format($stats['pages']) }}</p>
            </article>

            <article class="admin-stat-card">
                <p class="admin-stat-label">Registered Users</p>
                <p class="admin-stat-value">{{ number_format($stats['users']) }}</p>
            </article>
        </section>
    </section>
@endsection
