<x-app-layout>
    <x-slot name="title">Categories - Admin</x-slot>
    <x-slot name="page-title">Categories</x-slot>
    <x-slot name="page-subtitle">Manage menu categories</x-slot>

    <div class="space-y-6">
        <div class="flex justify-end">
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-1 bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-amber-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                New Category
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Name</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Menu Items</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Order</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-900">{{ $category->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $category->description ?? 'No description' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $category->menu_items_count }}</td>
                                <td class="px-4 py-3">
                                    @if ($category->is_active)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $category->sort_order }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-amber-600 hover:text-amber-700 text-xs font-medium">Edit</a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete category {{ $category->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 text-xs font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-12 text-sm text-slate-500">No categories yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">{{ $categories->links() }}</div>
        </div>
    </div>
</x-app-layout>
