@extends('layouts.admin')

@section('title', 'User Details - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header class="admin-index-header">
            <h1>User Details</h1>
            <a href="{{ route('admin.users.index') }}">Back to users</a>
        </header>

        <table>
            <tbody>
                <tr>
                    <th scope="row">ID</th>
                    <td>{{ $user->id }}</td>
                </tr>
                <tr>
                    <th scope="row">Name</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th scope="row">Role</th>
                    <td>{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                </tr>
                <tr>
                    <th scope="row">Registered</th>
                    <td>{{ $user->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
                <tr>
                    <th scope="row">Last Login</th>
                    <td>{{ $user->last_login_at?->format('Y-m-d H:i') ?? 'Never' }}</td>
                </tr>
            </tbody>
        </table>

        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="margin-top: 1rem;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Delete this user?');">Delete user</button>
        </form>
    </section>
@endsection
