@extends('layouts.admin')

@section('title', 'Create Tag - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Create Tag</h1>
            <p><a href="{{ route('admin.tags.index') }}">Back to tags</a></p>
        </header>

        <form method="POST" action="{{ route('admin.tags.store') }}" novalidate>
            @csrf

            <div>
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                @error('name')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug') }}" required>
                @error('slug')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Create tag</button>
        </form>
    </section>
@endsection
