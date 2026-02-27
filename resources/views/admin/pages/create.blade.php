@extends('layouts.admin')

@section('title', 'Create Page - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Create Page</h1>
            <p><a href="{{ route('admin.pages.index') }}">Back to pages</a></p>
        </header>

        <form method="POST" action="{{ route('admin.pages.store') }}" novalidate>
            @csrf

            <div>
                <label for="title">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required>
                @error('title')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug') }}">
                @error('slug')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="body">Body</label>
                <textarea id="body" name="body" rows="12" required>{{ old('body') }}</textarea>
                @error('body')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                    <option value="published" @selected(old('status') === 'published')>Published</option>
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
                    value="{{ old('published_at') }}"
                    placeholder="YYYY-MM-DD HH:MM:SS"
                >
                @error('published_at')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Create page</button>
        </form>
    </section>
@endsection
