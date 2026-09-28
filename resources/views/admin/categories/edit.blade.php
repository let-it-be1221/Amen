@php
    $isEdit = isset($category);
    $action = $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<x-app-layout>
    <x-slot name="title">{{ $isEdit ? 'Edit' : 'Create' }} Category</x-slot>
    <x-slot name="page-title">{{ $isEdit ? 'Edit' : 'Create' }} Category</x-slot>
    <x-slot name="page-subtitle">{{ $isEdit ? $category->name : 'Add a new menu category' }}</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back to categories
        </a>

        <form action="{{ $action }}" method="POST" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
            @csrf
            @method($method)

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $isEdit ? $category->name : '') }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">{{ old('description', $isEdit ? $category->description : '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Icon (optional)</label>
                <input type="text" name="icon" value="{{ old('icon', $isEdit ? $category->icon : '') }}" placeholder="e.g. utensils, drink, pizza" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                <p class="text-xs text-slate-500 mt-1">Use a heroicon name (without the icon- prefix).</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $isEdit ? $category->sort_order : 0) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                <p class="text-xs text-slate-500 mt-1">Lower numbers appear first.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $isEdit ? $category->is_active : true) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <label for="is_active" class="text-sm text-slate-700">Active (visible to waiters)</label>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-amber-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-amber-600">{{ $isEdit ? 'Update' : 'Create' }}</button>
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2 text-sm text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
