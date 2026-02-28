@extends('layouts.admin')

@section('title', 'Categories - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header class="admin-index-header">
            <h1>Categories</h1>
            <a href="{{ route('admin.categories.create') }}" class="btn admin-create-link">Create new category</a>
        </header>

        @if ($categories->isEmpty())
            <p>No categories found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Created</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->created_at }}</td>
                            <td><a href="{{ route('admin.categories.edit', $category) }}">Edit</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $categories->links() }}
        @endif
    </section>
@endsection
