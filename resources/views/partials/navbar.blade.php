@if ($variant === 'admin')
@php
    $adminNavLinks = [
        ['label' => 'Posts', 'href' => route('admin.posts.index')],
        ['label' => 'Pages', 'href' => route('admin.pages.index')],
        ['label' => 'Categories', 'href' => route('admin.categories.index')],
        ['label' => 'Tags', 'href' => route('admin.tags.index')],
        ['label' => 'Navigation', 'href' => route('admin.menus.index')],
        ['label' => 'Menu Locations', 'href' => route('admin.menu-locations.edit')],
    ];
@endphp
<header class="header" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
    <nav class="topnav" aria-label="Primary navigation">
        <div class="brand">
            <a href="{{ route('home') }}">{{ config('app.name') }}</a>
            <span class="label">Admin</span>
        </div>

        <button
            type="button"
            class="nav-toggle"
            :class="{ 'is-open': mobileOpen }"
            @click="mobileOpen = !mobileOpen"
            :aria-expanded="mobileOpen.toString()"
            aria-label="Toggle navigation menu"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="user user-desktop">
            @foreach ($adminNavLinks as $link)
                <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
            @endforeach
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

    <div class="mobile-nav-panel" x-cloak x-show="mobileOpen" x-transition.opacity.duration.200ms>
        <div class="mobile-nav-links">
            @foreach ($adminNavLinks as $link)
                <a href="{{ $link['href'] }}" @click="mobileOpen = false">{{ $link['label'] }}</a>
            @endforeach

            <span class="mobile-nav-divider" aria-hidden="true"></span>
            <span class="username">{{ auth()->user()->name ?? auth()->user()->email }}</span>

            <form method="POST" action="{{ route('logout') }}" @submit="mobileOpen = false">
                @csrf
                <button type="submit" class="mobile-link-button">Logout</button>
            </form>
        </div>
    </div>
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

    $publicNavLinks = [];

    if ($hasNavbarMenuAssigned) {
        foreach ($navbarMenuItems as $menuItem) {
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

            if (filled($href) && filled($label)) {
                $publicNavLinks[] = [
                    'href' => $href,
                    'label' => $label,
                ];
            }
        }
    }

    if ($showBlogNavLink) {
        $publicNavLinks[] = [
            'href' => route('blog.index'),
            'label' => 'Blog',
        ];
    }
@endphp
<header class="header" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
    <nav class="topnav" aria-label="Primary navigation">
        <div class="brand">
            <a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
        </div>

        <button
            type="button"
            class="nav-toggle"
            :class="{ 'is-open': mobileOpen }"
            @click="mobileOpen = !mobileOpen"
            :aria-expanded="mobileOpen.toString()"
            aria-label="Toggle navigation menu"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="user user-desktop">
            @foreach ($publicNavLinks as $link)
                <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
            @endforeach

            @if (! empty($publicNavLinks))
                <span class="user-separator" aria-hidden="true"></span>
            @endif

            @guest
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endguest

            @auth
                <span class="username">Welcome, {{ Auth::user()->name }}</span>

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

    <div class="mobile-nav-panel" x-cloak x-show="mobileOpen" x-transition.opacity.duration.200ms>
        <div class="mobile-nav-links">
            @foreach ($publicNavLinks as $link)
                <a href="{{ $link['href'] }}" @click="mobileOpen = false">{{ $link['label'] }}</a>
            @endforeach

            @if (! empty($publicNavLinks))
                <span class="mobile-nav-divider" aria-hidden="true"></span>
            @endif

            @guest
                <a href="{{ route('login') }}" @click="mobileOpen = false">Login</a>
                <a href="{{ route('register') }}" @click="mobileOpen = false">Register</a>
            @endguest

            @auth
                <span class="username">Welcome, {{ Auth::user()->name }}</span>

                @if (Auth::user()->is_admin === true)
                    <a href="{{ route('admin.dashboard') }}" @click="mobileOpen = false">Admin Dashboard</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" @submit="mobileOpen = false">
                    @csrf
                    <button type="submit" class="mobile-link-button">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</header>
@endif
