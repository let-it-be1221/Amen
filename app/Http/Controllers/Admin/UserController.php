<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => User::ROLES,
        ]);
    }

    public function create()
    {
        return view('admin.users.create', [
            'roles' => User::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$validated['name']} created successfully.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => User::ROLES,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', false);

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$user->name} updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$user->name} deleted.");
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$user->name} {$status}.");
    }
}
