@extends('layouts.admin')

@section('title', 'Edit Post - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Edit Post</h1>
            <p><a href="{{ route('admin.posts.index') }}">Back to posts</a></p>
        </header>

        <form method="POST" action="{{ route('admin.posts.update', $post) }}" novalidate>
            @csrf
            @method('PUT')

            <div>
                <label for="title">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $post->title) }}" required>
                @error('title')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $post->slug) }}">
                @error('slug')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
                @error('excerpt')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="body">Body</label>
                <textarea id="body" name="body" rows="10" required>{{ old('body', $post->body) }}</textarea>
                @error('body')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
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
                    value="{{ old('published_at', $post->published_at) }}"
                    placeholder="YYYY-MM-DD HH:MM:SS"
                >
                @error('published_at')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <fieldset>
                <legend>Categories</legend>
                @php
                    $selectedCategories = old('categories', $post->categories->pluck('id')->all());
                @endphp
                @forelse ($categories as $category)
                    <label>
                        <input
                            type="checkbox"
                            name="categories[]"
                            value="{{ $category->id }}"
                            @checked(in_array($category->id, $selectedCategories))
                        >
                        {{ $category->name }}
                    </label>
                @empty
                    <p>No categories available.</p>
                @endforelse
                @error('categories')
                    <p>{{ $message }}</p>
                @enderror
                @error('categories.*')
                    <p>{{ $message }}</p>
                @enderror
            </fieldset>

            <fieldset>
                <legend>Tags</legend>
                @php
                    $selectedTags = old('tags', $post->tags->pluck('id')->all());
                @endphp
                @forelse ($tags as $tag)
                    <label>
                        <input
                            type="checkbox"
                            name="tags[]"
                            value="{{ $tag->id }}"
                            @checked(in_array($tag->id, $selectedTags))
                        >
                        {{ $tag->name }}
                    </label>
                @empty
                    <p>No tags available.</p>
                @endforelse
                @error('tags')
                    <p>{{ $message }}</p>
                @enderror
                @error('tags.*')
                    <p>{{ $message }}</p>
                @enderror
            </fieldset>

            <button type="submit">Update post</button>
        </form>
    </section>
@endsection
