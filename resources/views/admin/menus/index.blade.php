@extends('layouts.admin')

@section('title', 'Navigation - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header class="admin-index-header">
            <h1>Navigation</h1>
            <a href="{{ route('admin.menus.create') }}" class="btn admin-create-link">Create new menu</a>
        </header>

        @if ($menus->isEmpty())
            <p>No menus found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Items</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menus as $menu)
                        <tr>
                            <td>{{ $menu->name }}</td>
                            <td>{{ $menu->slug }}</td>
                            <td>{{ $menu->items_count }}</td>
                            <td>
                                <a href="{{ route('admin.menus.edit', $menu) }}">Manage items</a>

                                <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this menu and all its items?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection
