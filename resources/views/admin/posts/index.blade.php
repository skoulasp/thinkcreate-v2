@extends('layouts.admin')

@section('title', 'Posts - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Posts</h1>
            <p><a href="{{ route('admin.posts.create') }}">Create new post</a></p>
        </header>

        @if ($posts->isEmpty())
            <p>No posts found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Status</th>
                        <th scope="col">Author</th>
                        <th scope="col">Published</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($posts as $post)
                        <tr>
                            <td>{{ $post->title }}</td>
                            <td>{{ ucfirst($post->status) }}</td>
                            <td>{{ $post->author->name ?? $post->author->email ?? 'Unknown' }}</td>
                            <td>{{ $post->published_at ?: '-' }}</td>
                            <td>
                                <a href="{{ route('admin.posts.edit', $post) }}">Edit</a>

                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this post?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $posts->links() }}
        @endif
    </section>
@endsection
