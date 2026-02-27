@extends('layouts.admin')

@section('title', 'Tags - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Tags</h1>
            <p><a href="{{ route('admin.tags.create') }}">Create new tag</a></p>
        </header>

        @if ($tags->isEmpty())
            <p>No tags found.</p>
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
                    @foreach ($tags as $tag)
                        <tr>
                            <td>{{ $tag->name }}</td>
                            <td>{{ $tag->slug }}</td>
                            <td>{{ $tag->created_at }}</td>
                            <td><a href="{{ route('admin.tags.edit', $tag) }}">Edit</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $tags->links() }}
        @endif
    </section>
@endsection
