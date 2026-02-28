@extends('layouts.admin')

@section('title', 'Settings - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Settings</h1>
            <p>Update your profile information and password.</p>
        </header>

        <section aria-labelledby="profile-settings-heading">
            <header>
                <h2 id="profile-settings-heading">Profile</h2>
            </header>

            <form method="POST" action="{{ route('admin.settings.profile.update') }}" novalidate>
                @csrf
                @method('PATCH')

                <div>
                    <label for="name">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        autocomplete="name"
                        required
                    >
                </div>

                <div>
                    <label for="email">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        autocomplete="email"
                        required
                    >
                </div>

                <button type="submit">Save profile</button>
            </form>
        </section>

        <section aria-labelledby="password-settings-heading">
            <header>
                <h2 id="password-settings-heading">Password</h2>
            </header>

            <form method="POST" action="{{ route('admin.settings.password.update') }}" novalidate>
                @csrf
                @method('PATCH')

                <div>
                    <label for="current_password">Current password</label>
                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div>
                    <label for="password">New password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div>
                    <label for="password_confirmation">Confirm new password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit">Update password</button>
            </form>
        </section>
    </section>
@endsection
