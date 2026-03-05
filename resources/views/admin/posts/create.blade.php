@extends('layouts.admin')

@section('title', 'Create Post - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Create Post</h1>
            <p><a href="{{ route('admin.posts.index') }}">Back to posts</a></p>
        </header>

        @php
            $initialTitle = old('title', '');
            $initialSlug = old('slug', '');
            $initialManual = old('manual_slug') == '1';
        @endphp

        <form
            method="POST"
            action="{{ route('admin.posts.store') }}"
            enctype="multipart/form-data"
            novalidate
            x-data="slugForm(@js($initialTitle), @js($initialSlug), @js($initialManual))"
            x-init="init()"
        >
            @csrf

            <div>
                <label for="title">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" x-model="name" @input="syncSlug()" required>
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
                <input id="slug" name="slug" type="text" value="{{ old('slug') }}" x-model="slug" :disabled="!manualSlug">
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
                <textarea id="excerpt" name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="body">Body</label>
                <textarea
                    id="body"
                    name="body"
                    rows="10"
                    required
                    data-rich-text-editor
                    data-editor-upload-url="{{ route('admin.posts.editor-images.store', absolute: false) }}"
                >{{ old('body') }}</textarea>
                @error('body')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="featured_image">Featured image</label>
                <input id="featured_image" name="featured_image" type="file" accept="image/*">
                <p>Optional. JPG, PNG, GIF, WEBP, BMP, or SVG up to 5 MB.</p>
                @error('featured_image')
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
                <input type="hidden" name="comments_enabled" value="0">
                <label for="comments_enabled">
                    <input
                        id="comments_enabled"
                        name="comments_enabled"
                        type="checkbox"
                        value="1"
                        @checked(old('comments_enabled', '0') == '1')
                    >
                    Enable comments for this post
                </label>
                @error('comments_enabled')
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

            <fieldset>
                <legend>Categories</legend>
                @forelse ($categories as $category)
                    <label>
                        <input
                            type="checkbox"
                            name="categories[]"
                            value="{{ $category->id }}"
                            @checked(in_array($category->id, old('categories', [])))
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
                @forelse ($tags as $tag)
                    <label>
                        <input
                            type="checkbox"
                            name="tags[]"
                            value="{{ $tag->id }}"
                            @checked(in_array($tag->id, old('tags', [])))
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

            <button type="submit">Create post</button>
        </form>
    </section>
@endsection
