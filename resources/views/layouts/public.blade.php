<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        @endif
    </head>
    <body>
        <nav aria-label="Primary navigation">
            <div class="container">
                <a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>

                <div>
                    @guest
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endguest

                    @auth
                        @if (Auth::user()->is_admin === true)
                            <a href="{{ route('admin.index') }}">Admin Dashboard</a>
                        @endif

                        <span>Welcome, {{ Auth::user()->name }}</span>

                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" hidden>
                            @csrf
                        </form>
                    @endauth
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
    </body>
</html>
