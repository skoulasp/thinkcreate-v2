<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Β· ' . config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @endif
</head>
<body class="admin-body">
@php
    $adminSidebarActive = [
        'posts' => request()->routeIs('admin.posts.*'),
        'pages' => request()->routeIs('admin.pages.*'),
        'categories' => request()->routeIs('admin.categories.*'),
        'tags' => request()->routeIs('admin.tags.*'),
        'menus' => request()->routeIs('admin.menus.*'),
        'menu_locations' => request()->routeIs('admin.menu-locations.*'),
        'settings' => request()->routeIs('admin.settings.*'),
        'users' => request()->routeIs('admin.users.*'),
    ];
@endphp

@include('partials.navbar', ['variant' => 'admin'])

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-section">
            <h4>Content</h4>
            <ul>
                <li><a href="{{ route('admin.posts.index') }}" @class(['is-active' => $adminSidebarActive['posts']])>Posts</a></li>
                <li><a href="{{ route('admin.pages.index') }}" @class(['is-active' => $adminSidebarActive['pages']])>Pages</a></li>
                <li><a href="{{ route('admin.categories.index') }}" @class(['is-active' => $adminSidebarActive['categories']])>Categories</a></li>
                <li><a href="{{ route('admin.tags.index') }}" @class(['is-active' => $adminSidebarActive['tags']])>Tags</a></li>
                <li><a href="{{ route('admin.menus.index') }}" @class(['is-active' => $adminSidebarActive['menus']])>Navigation</a></li>
                <li><a href="{{ route('admin.menu-locations.edit') }}" @class(['is-active' => $adminSidebarActive['menu_locations']])>Menu Locations</a></li>
            </ul>
        </div>
        <div class="sidebar-section">
            <h4>Site</h4>
            <ul>
                <li><a href="{{ route('admin.users.index') }}" @class(['is-active' => $adminSidebarActive['users']])>Users</a></li>
                <li><a href="{{ route('admin.settings.edit') }}" @class(['is-active' => $adminSidebarActive['settings']])>Settings</a></li>
            </ul>
        </div>
    </aside>

    <main class="admin-main">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Please fix the following:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')

</body>
</html>
