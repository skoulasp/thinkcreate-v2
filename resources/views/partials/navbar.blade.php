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
            <a href="{{ route('admin.menu-locations.edit') }}">Menu Locations</a>
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
    $navbarLocation = \App\Models\MenuLocation::query()
        ->where('location', 'navbar')
        ->with(['menu.items' => fn ($query) => $query->with('page')->orderBy('sort_order')])
        ->first();

    $hasNavbarMenuAssigned = filled($navbarLocation?->menu_id);
    $navbarMenuItems = $navbarLocation?->menu?->items ?? collect();
    $showBlogNavLink = \App\Models\Setting::getBoolValue('website.navbar.show_blog_link', false);

    $reservedTopLevelSegments = [];

    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        $uri = $route->uri();

        if ($uri === '' || str_contains($uri, '{')) {
            continue;
        }

        $first = strtok($uri, '/');

        if ($first !== false && $first !== '') {
            $reservedTopLevelSegments[] = strtolower($first);
        }
    }

    $reservedTopLevelSegments = array_values(array_unique($reservedTopLevelSegments));

    $conflictsReservedTopLevelRoute = static function (string $path) use ($reservedTopLevelSegments): bool {
        $parsed = parse_url($path);

        if (($parsed['scheme'] ?? null) || ($parsed['host'] ?? null)) {
            return false;
        }

        $routePath = trim($parsed['path'] ?? $path, '/');

        if ($routePath === '') {
            return false;
        }

        $firstSegment = strtok($routePath, '/');

        if ($firstSegment === false) {
            return false;
        }

        return in_array(strtolower($firstSegment), $reservedTopLevelSegments, true);
    };

    $hasMainPublicNavLinks = $hasNavbarMenuAssigned || $showBlogNavLink;
@endphp
<header class="header">
    <nav class="topnav" aria-label="Primary navigation">
        <div class="brand">
            <a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
        </div>

        <div class="user">
            @if ($hasNavbarMenuAssigned)
                @foreach ($navbarMenuItems as $menuItem)
                    @php
                        $href = null;

                        if ($menuItem->page) {
                            $slugPath = trim($menuItem->page->slug, '/');

                            if ($slugPath !== '' && ! $conflictsReservedTopLevelRoute($slugPath)) {
                                $href = url('/'.$slugPath);
                            }
                        } elseif (filled($menuItem->url) && ! $conflictsReservedTopLevelRoute($menuItem->url)) {
                            $href = $menuItem->url;
                        }

                        $label = $menuItem->label ?: ($menuItem->page?->title ?: $menuItem->url);
                    @endphp

                    @if (filled($href) && filled($label))
                        <a href="{{ $href }}">{{ $label }}</a>
                    @endif
                @endforeach
            @endif

            @if ($showBlogNavLink)
                <a href="{{ route('blog.index') }}">Blog</a>
            @endif

            @if ($hasMainPublicNavLinks)
                <span class="user-separator" aria-hidden="true"></span>
            @endif

            @guest
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endguest

            @auth
            <span class="username">Welcome, {{ Auth::user()->name }} 👋</span>
                @if (Auth::user()->is_admin === true)
                    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                @endif


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
