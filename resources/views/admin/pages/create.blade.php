@extends('layouts.admin')

@section('title', 'Create Page - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Create Page</h1>
            <p><a href="{{ route('admin.pages.index') }}">Back to pages</a></p>
        </header>

        @php
            $initialTitle = old('title', '');
            $initialSlug = old('slug', '');
            $initialManual = old('manual_slug') == '1';
        @endphp

        <form
            method="POST"
            action="{{ route('admin.pages.store') }}"
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
