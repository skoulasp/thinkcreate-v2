@extends('layouts.guest')

@section('title', 'Login | ' . config('app.name', 'Laravel'))

@section('content')
    <section>
        <h1>Sign in</h1>

        @if (session('status'))
            <p role="status">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div role="alert" aria-live="assertive">
                <p>Unable to sign in with the provided credentials.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" novalidate>
            @csrf

            <div>
                <label for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    autofocus
                >
                @error('email')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                >
                @error('password')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1">
                    Remember me
                </label>
            </div>

            <button type="submit">Sign in</button>
        </form>

        <p>
            Need an account?
            <a href="{{ route('register') }}">Register</a>
        </p>
    </section>
@endsection