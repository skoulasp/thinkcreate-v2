@extends('layouts.admin')

@section('title', 'Edit Tag - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Edit Tag</h1>
            <p><a href="{{ route('admin.tags.index') }}">Back to tags</a></p>
        </header>

        @php
            $initialName = old('name', $tag->name);
            $initialSlug = old('slug', $tag->slug);
            $derivedSlug = \Illuminate\Support\Str::slug($initialName);
            $initialManual = old('manual_slug') == '1' || $initialSlug !== $derivedSlug;
        @endphp

        <form
            method="POST"
            action="{{ route('admin.tags.update', $tag) }}"
            novalidate
            x-data="slugForm(@js($initialName), @js($initialSlug), @js($initialManual))"
            x-init="init()"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $tag->name) }}" x-model="name" @input="syncSlug()" required>
                @error('name')
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
                <input id="slug" name="slug" type="text" value="{{ old('slug', $tag->slug) }}" x-model="slug" :disabled="!manualSlug">
                <input type="hidden" name="slug_effective" :value="manualSlug ? slug : slugify(name)">
                @error('slug')
                    <p>{{ $message }}</p>
                @enderror
                @error('slug_effective')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Update tag</button>
        </form>
    </section>
@endsection
