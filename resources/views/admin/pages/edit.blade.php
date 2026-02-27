@extends('layouts.admin')

@section('title', 'Edit Page - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Edit Page</h1>
            <p><a href="{{ route('admin.pages.index') }}">Back to pages</a></p>
        </header>

        <form method="POST" action="{{ route('admin.pages.update', $page) }}" novalidate>
            @csrf
            @method('PUT')

            <div>
                <label for="title">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $page->title) }}" required>
                @error('title')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $page->slug) }}">
                @error('slug')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="body">Body</label>
                <textarea id="body" name="body" rows="12" required>{{ old('body', $page->body) }}</textarea>
                @error('body')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="draft" @selected(old('status', $page->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $page->status) === 'published')>Published</option>
                </select>
                @error('status')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="published_at">Published at</label>
                <input
                    id="published_at"
                    name="published_at"
                    type="text"
                    value="{{ old('published_at', $page->published_at) }}"
                    placeholder="YYYY-MM-DD HH:MM:SS"
                >
                @error('published_at')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Update page</button>
        </form>
    </section>
@endsection
