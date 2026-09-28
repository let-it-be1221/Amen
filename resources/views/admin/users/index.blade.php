<x-app-layout>
    <x-slot name="title">Users - Admin</x-slot>
    <x-slot name="page-title">Users</x-slot>
    <x-slot name="page-subtitle">Manage all system users</x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, email, or phone..." class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Role</label>
                    <select name="role" class="rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All roles</option>
                        @foreach ($roles as $key => $label)
                            <option value="{{ $key }}" @if (request('role') === $key) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-800">Search</button>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-1 bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-amber-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    New User
                </a>
            </form>
        </div>

        <!-- Users table -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">User</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Role</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Phone</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Joined</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 text-white flex items-center justify-center text-sm font-semibold uppercase flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-900">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ match($user->role) {
                                        'admin' => 'bg-purple-100 text-purple-800',
                                        'supervisor' => 'bg-blue-100 text-blue-800',
                                        'waiter' => 'bg-amber-100 text-amber-800',
                                        'cooker' => 'bg-rose-100 text-rose-800',
                                        'cashier' => 'bg-emerald-100 text-emerald-800',
                                        default => 'bg-slate-100 text-slate-800',
                                    } }}">{{ $user->roleLabel() }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $user->phone ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-500">{{ $user->created_at->format('M j, Y') }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-amber-600 hover:text-amber-700 text-xs font-medium">Edit</a>
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-slate-600 hover:text-slate-900 text-xs font-medium">{{ $user->is_active ? 'Disable' : 'Enable' }}</button>
                                    </form>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete user {{ $user->name }}? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 text-xs font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-12 text-sm text-slate-500">No users found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>
