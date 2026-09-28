<x-app-layout>
    <x-slot name="title">Tables - Admin</x-slot>
    <x-slot name="page-title">Tables</x-slot>
    <x-slot name="page-subtitle">Manage restaurant tables</x-slot>

    <div class="space-y-6">
        <div class="flex justify-end">
            <a href="{{ route('admin.tables.create') }}" class="inline-flex items-center gap-1 bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-amber-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                New Table
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Name</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Seats</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Location</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Active</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tables as $table)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $table->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $table->seats }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $table->location ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $colors = ['available' => 'bg-emerald-100 text-emerald-700', 'occupied' => 'bg-rose-100 text-rose-700', 'reserved' => 'bg-amber-100 text-amber-700'];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$table->status] ?? 'bg-slate-100 text-slate-700' }}">{{ $table->statusLabel() }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($table->is_active)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.tables.edit', $table) }}" class="text-amber-600 hover:text-amber-700 text-xs font-medium">Edit</a>
                                    <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="inline" onsubmit="return confirm('Delete table {{ $table->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 text-xs font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-12 text-sm text-slate-500">No tables yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">{{ $tables->links() }}</div>
        </div>
    </div>
</x-app-layout>
