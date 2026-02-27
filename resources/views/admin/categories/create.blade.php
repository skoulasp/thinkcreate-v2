@extends('layouts.admin')

@section('title', 'Create Category - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Create Category</h1>
            <p><a href="{{ route('admin.categories.index') }}">Back to categories</a></p>
        </header>

        <form method="POST" action="{{ route('admin.categories.store') }}" novalidate>
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

            <button type="submit">Create category</button>
        </form>
    </section>
@endsection
