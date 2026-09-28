<x-app-layout>
    <x-slot name="title">Menu Items - Admin</x-slot>
    <x-slot name="page-title">Menu Items</x-slot>
    <x-slot name="page-subtitle">Manage all menu items</x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Item name..." class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                    <select name="category_id" class="rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if (request('category_id') == $category->id) selected @endif>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-800">Filter</button>
                <a href="{{ route('admin.menu-items.create') }}" class="inline-flex items-center gap-1 bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-amber-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    New Item
                </a>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Item</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Category</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Price</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Flags</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($menuItems as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($item->image)
                                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-2.25-1.313M21 7.5v2.25m0-2.25-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3 2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75 2.25-1.313M12 21.75V19.5" /></svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-slate-900">{{ $item->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $item->description ? Str::limit($item->description, 50) : '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $item->category?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-slate-900">${{ number_format($item->price, 2) }}</td>
                                <td class="px-4 py-3">
                                    @if ($item->is_available)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Unavailable</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if ($item->is_vegetarian)<span class="inline-block px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 mr-1">Veg</span>@endif
                                    @if ($item->is_spicy)<span class="inline-block px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 mr-1">Spicy</span>@endif
                                    @if ($item->calories)<span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">{{ $item->calories }}</span>@endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.menu-items.edit', $item) }}" class="text-amber-600 hover:text-amber-700 text-xs font-medium">Edit</a>
                                    <form action="{{ route('admin.menu-items.toggle-availability', $item) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-slate-600 hover:text-slate-900 text-xs font-medium">{{ $item->is_available ? 'Hide' : 'Show' }}</button>
                                    </form>
                                    <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $item->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 text-xs font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-12 text-sm text-slate-500">No menu items found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">{{ $menuItems->links() }}</div>
        </div>
    </div>
</x-app-layout>
