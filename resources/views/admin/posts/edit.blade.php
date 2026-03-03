@extends('layouts.admin')

@section('title', 'Edit Post - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Edit Post</h1>
            <p><a href="{{ route('admin.posts.index') }}">Back to posts</a></p>
        </header>

        @php
            $initialTitle = old('title', $post->title);
            $initialSlug = old('slug', $post->slug);
            $derivedSlug = \Illuminate\Support\Str::slug($initialTitle);
            $initialManual = old('manual_slug') == '1' || $initialSlug !== $derivedSlug;
        @endphp

        <form
            method="POST"
            action="{{ route('admin.posts.update', $post) }}"
            enctype="multipart/form-data"
            novalidate
            x-data="slugForm(@js($initialTitle), @js($initialSlug), @js($initialManual))"
            x-init="init()"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="title">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $post->title) }}" x-model="name" @input="syncSlug()" required>
                @error('title')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual_slug">
                    <input id="manual_slug" name="manual_slug" type="checkbox" value="1" x-model="manualSlug" @change="toggleManual()">
                    Set slug manually
                </label>
            </div>

            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $post->slug) }}" x-model="slug" :disabled="!manualSlug">
                <input type="hidden" name="slug_effective" :value="manualSlug ? slug : slugify(name)">
                @error('slug')
                    <p>{{ $message }}</p>
                @enderror
                @error('slug_effective')
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
                <label for="featured_image">Featured image</label>
                <input id="featured_image" name="featured_image" type="file" accept="image/*">
                <p>Optional. Uploading a new image replaces the current one.</p>
                @if ($post->featured_image_path)
                    <p>
                        Current:
                        <a href="{{ Storage::disk('public')->url($post->featured_image_path) }}" target="_blank" rel="noopener noreferrer">
                            View image
                        </a>
                    </p>
                    <label for="remove_featured_image">
                        <input id="remove_featured_image" name="remove_featured_image" type="checkbox" value="1" @checked(old('remove_featured_image') == '1')>
                        Remove featured image
                    </label>
                @endif
                @error('featured_image')
                    <p>{{ $message }}</p>
                @enderror
                @error('remove_featured_image')
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
