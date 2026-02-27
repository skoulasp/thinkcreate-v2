@extends('layouts.admin')

@section('title', 'Edit Category - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Edit Category</h1>
            <p><a href="{{ route('admin.categories.index') }}">Back to categories</a></p>
        </header>

        @php
            $initialName = old('name', $category->name);
            $initialSlug = old('slug', $category->slug);
            $derivedSlug = \Illuminate\Support\Str::slug($initialName);
            $initialManual = old('manual_slug') == '1' || $initialSlug !== $derivedSlug;
        @endphp

        <form
            method="POST"
            action="{{ route('admin.categories.update', $category) }}"
            novalidate
            x-data="slugForm(@js($initialName), @js($initialSlug), @js($initialManual))"
            x-init="init()"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" x-model="name" @input="syncSlug()" required>
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
                <input id="slug" name="slug" type="text" value="{{ old('slug', $category->slug) }}" x-model="slug" :disabled="!manualSlug">
                <input type="hidden" name="slug_effective" :value="manualSlug ? slug : slugify(name)">
                @error('slug')
                    <p>{{ $message }}</p>
                @enderror
                @error('slug_effective')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Update category</button>
        </form>
    </section>
@endsection
