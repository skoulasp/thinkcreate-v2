@extends('layouts.admin')

@section('title', 'Users - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header class="admin-index-header">
            <h1>Users</h1>
        </header>

        @if ($users->isEmpty())
            <p>No users found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Registered</th>
                        <th scope="col">Last Login</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                            <td>{{ $user->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>{{ $user->last_login_at?->format('Y-m-d H:i') ?? 'Never' }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}">Details</a>

                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this user?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $users->links() }}
        @endif
    </section>
@endsection
