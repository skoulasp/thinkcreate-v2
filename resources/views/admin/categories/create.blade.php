@extends('layouts.admin')

@section('title', 'Create Category - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Create Category</h1>
            <p><a href="{{ route('admin.categories.index') }}">Back to categories</a></p>
        </header>

        @php
            $initialName = old('name', '');
            $initialSlug = old('slug', '');
            $initialManual = old('manual_slug') == '1';
        @endphp

        <form
            method="POST"
            action="{{ route('admin.categories.store') }}"
            novalidate
            x-data="slugForm(@js($initialName), @js($initialSlug), @js($initialManual))"
            x-init="init()"
        >
            @csrf

            <div>
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" x-model="name" @input="syncSlug()" required>
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
                <input id="slug" name="slug" type="text" value="{{ old('slug') }}" x-model="slug" :disabled="!manualSlug">
                <input type="hidden" name="slug_effective" :value="manualSlug ? slug : slugify(name)">
                @error('slug')
                    <p>{{ $message }}</p>
                @enderror
                @error('slug_effective')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Create category</button>
        </form>
    </section>
@endsection
