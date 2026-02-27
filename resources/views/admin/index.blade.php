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
@endsection
