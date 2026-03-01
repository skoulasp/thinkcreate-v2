@extends('layouts.admin')

@section('title', 'Edit Menu Item - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Edit Menu Item</h1>
            <p><a href="{{ route('admin.menus.edit', $menu) }}">Back to menu</a></p>
        </header>

        <form method="POST" action="{{ route('admin.menus.items.update', [$menu, $item]) }}" novalidate>
            @csrf
            @method('PATCH')

            <div>
                <label for="label">Label override (optional)</label>
                <input id="label" name="label" type="text" value="{{ old('label', $item->label) }}">
            </div>

            <div>
                <label for="page_id">Internal page (optional)</label>
                <select id="page_id" name="page_id">
                    <option value="">Select a page</option>
                    @foreach ($pages as $page)
                        <option value="{{ $page->id }}" @selected(old('page_id', $item->page_id) == $page->id)>{{ $page->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="url">Custom URL (optional)</label>
                <input id="url" name="url" type="text" value="{{ old('url', $item->url) }}" placeholder="/contact or https://example.com">
            </div>

            <p>Choose either an internal page or a custom URL.</p>

            <button type="submit">Update menu item</button>
        </form>
    </section>
@endsection
