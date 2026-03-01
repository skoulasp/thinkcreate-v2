<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Admin · ' . config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @endif
</head>
<body class="admin-body">

@include('partials.navbar', ['variant' => 'admin'])

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-section">
            <h4>Content</h4>
            <ul>
                <li><a href="{{ route('admin.posts.index') }}">Posts</a></li>
                <li><a href="{{ route('admin.pages.index') }}">Pages</a></li>
                <li><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                <li><a href="{{ route('admin.tags.index') }}">Tags</a></li>
                <li><a href="{{ route('admin.menus.index') }}">Navigation</a></li>
            </ul>
        </div>
        <div class="sidebar-section">
            <h4>Account</h4>
            <ul>
                <li><a href="{{ route('admin.settings.edit') }}">Settings</a></li>
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

</body>
</html>
