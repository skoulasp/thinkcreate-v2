@extends('layouts.admin')

@section('title', 'Edit Menu - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Edit Menu</h1>
            <p><a href="{{ route('admin.menus.index') }}">Back to navigation</a></p>
        </header>

        <form method="POST" action="{{ route('admin.menus.update', $menu) }}" novalidate>
            @csrf
            @method('PUT')

            <div>
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $menu->name) }}" required>
            </div>

            <div>
                <label for="slug">Slug</label>
                <input id="slug" type="text" value="{{ $menu->slug }}" disabled readonly>
                <p>This identifier is set at creation and cannot be changed.</p>
            </div>

            <button type="submit">Update menu</button>
        </form>
    </section>

    <section class="menu-items-editor" data-menu-items-sortable data-reorder-url="{{ route('admin.menus.items.reorder', $menu) }}" data-csrf-token="{{ csrf_token() }}">
        <header class="admin-index-header">
            <h2>Menu Items</h2>
            <a href="{{ route('admin.menus.items.create', $menu) }}" class="btn admin-create-link">Add item</a>
        </header>

        @if ($menu->items->isEmpty())
            <p>This menu has no items yet.</p>
        @else
            <p class="menu-items-help">Drag rows using the handle to reorder items.</p>

            <table>
                <thead>
                    <tr>
                        <th scope="col">Order</th>
                        <th scope="col">Label</th>
                        <th scope="col">Destination</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody data-menu-items-list>
                    @foreach ($menu->items as $item)
                        <tr data-item-id="{{ $item->id }}">
                            <td>
                                <button type="button" class="menu-item-handle" data-drag-handle aria-label="Drag to reorder">::</button>
                            </td>
                            <td>{{ $item->display_label }}</td>
                            <td>
                                @if ($item->page)
                                    Page: <a href="{{ route('pages.show', $item->page) }}" target="_blank" rel="noopener noreferrer">{{ $item->page->title }}</a>
                                @else
                                    URL: <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer">{{ $item->url }}</a>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.menus.items.edit', [$menu, $item]) }}">Edit</a>

                                <form method="POST" action="{{ route('admin.menus.items.destroy', [$menu, $item]) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this menu item?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p class="menu-items-status" data-order-status aria-live="polite"></p>
    </section>
@endsection
