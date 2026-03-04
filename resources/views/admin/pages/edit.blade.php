@extends('layouts.admin')

@section('title', 'Edit Page - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Edit Page</h1>
            <p><a href="{{ route('admin.pages.index') }}">Back to pages</a></p>
        </header>

        @php
            $initialTitle = old('title', $page->title);
            $initialSlug = old('slug', $page->slug);
            $derivedSlug = \Illuminate\Support\Str::slug($initialTitle);
            $initialManual = old('manual_slug') == '1' || $initialSlug !== $derivedSlug;
        @endphp

        <form
            method="POST"
            action="{{ route('admin.pages.update', $page) }}"
            novalidate
            x-data="slugForm(@js($initialTitle), @js($initialSlug), @js($initialManual))"
            x-init="init()"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="title">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $page->title) }}" x-model="name" @input="syncSlug()" required>
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
                <input id="slug" name="slug" type="text" value="{{ old('slug', $page->slug) }}" x-model="slug" :disabled="!manualSlug">
                <input type="hidden" name="slug_effective" :value="manualSlug ? slug : slugify(name)">
                @error('slug')
                    <p>{{ $message }}</p>
                @enderror
                @error('slug_effective')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="body">Body</label>
                <textarea
                    id="body"
                    name="body"
                    rows="12"
                    required
                    data-rich-text-editor
                    data-editor-upload-url="{{ route('admin.posts.editor-images.store', absolute: false) }}"
                >{{ old('body', $page->body) }}</textarea>
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
