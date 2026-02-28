@extends('layouts.admin')

@section('title', 'Pages - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header class="admin-index-header">
            <h1>Pages</h1>
            <a href="{{ route('admin.pages.create') }}" class="btn admin-create-link">Create new page</a>
        </header>

        @if ($pages->isEmpty())
            <p>No pages found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Status</th>
                        <th scope="col">Published</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td>
                                <a href="{{ route('pages.show', $page) }}" target="_blank" rel="noopener noreferrer">
                                    {{ $page->title }}
                                </a>
                            </td>
                            <td>{{ ucfirst($page->status) }}</td>
                            <td>{{ $page->published_at ?: '-' }}</td>
                            <td>
                                <a href="{{ route('admin.pages.edit', $page) }}">Edit</a>

                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this page?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $pages->links() }}
        @endif
    </section>
@endsection
