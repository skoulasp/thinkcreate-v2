@if ($variant === 'admin')
<header class="header">
    <nav class="topnav" aria-label="Primary navigation">
        <div class="brand">
            <a href="{{ route('home') }}">{{ config('app.name') }}</a>
            <span class="label">Admin</span>
        </div>

        <div class="user">
            <a href="{{ route('admin.posts.index') }}">Posts</a>
            <a href="{{ route('admin.pages.index') }}">Pages</a>
            <a href="{{ route('admin.categories.index') }}">Categories</a>
            <a href="{{ route('admin.tags.index') }}">Tags</a>
            <a href="{{ route('admin.menus.index') }}">Navigation</a>
            <span class="user-separator" aria-hidden="true"></span>

            <span class="username">
                {{ auth()->user()->name ?? auth()->user()->email }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="link-button">Logout</button>
            </form>
        </div>
    </nav>
</header>
@elseif ($variant === 'public')
@php
    $mainMenu = \App\Models\Menu::query()
        ->where('slug', 'main')
        ->with(['items' => fn ($query) => $query->with('page')->orderBy('sort_order')])
        ->first();

    $mainMenuItems = $mainMenu?->items ?? collect();
@endphp
<header class="header">
    <nav class="topnav" aria-label="Primary navigation">
        <div class="brand">
            <a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
        </div>

        <div class="user">
            @forelse ($mainMenuItems as $menuItem)
                @php
                    $href = $menuItem->page
                        ? route('pages.show', $menuItem->page)
                        : $menuItem->url;
                @endphp
                @if (filled($href))
                    <a href="{{ $href }}">{{ $menuItem->display_label }}</a>
                @endif
            @empty
                <a href="{{ route('blog.index') }}">Blog</a>
            @endforelse

            @guest
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endguest

            @auth
                @if (Auth::user()->is_admin === true)
                    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                @endif

                <span class="username">Welcome, {{ Auth::user()->name }}</span>

                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" hidden>
                    @csrf
                </form>
            @endauth
        </div>
    </nav>
</header>
@endif
