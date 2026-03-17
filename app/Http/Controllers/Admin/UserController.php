<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors([
                'user' => 'You cannot delete your own account while logged in.',
            ]);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
