@php
    $isEdit = isset($menuItem);
    $action = $isEdit ? route('admin.menu-items.update', $menuItem) : route('admin.menu-items.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<x-app-layout>
    <x-slot name="title">{{ $isEdit ? 'Edit' : 'Create' }} Menu Item</x-slot>
    <x-slot name="page-title">{{ $isEdit ? 'Edit' : 'Create' }} Menu Item</x-slot>
    <x-slot name="page-subtitle">{{ $isEdit ? $menuItem->name : 'Add a new menu item' }}</x-slot>

    <div class="max-w-3xl">
        <a href="{{ route('admin.menu-items.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back to menu items
        </a>

        <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
            @csrf
            @method($method)

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $isEdit ? $menuItem->name : '') }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <select name="category_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">— Select category —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if (old('category_id', $isEdit ? $menuItem->category_id : '') == $category->id) selected @endif>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">{{ old('description', $isEdit ? $menuItem->description : '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Price ($)</label>
                    <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $isEdit ? $menuItem->price : '0.00') }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @error('price') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Prep time (min)</label>
                    <input type="number" name="preparation_time" min="0" value="{{ old('preparation_time', $isEdit ? $menuItem->preparation_time : 15) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Calories</label>
                    <input type="text" name="calories" value="{{ old('calories', $isEdit ? $menuItem->calories : '') }}" placeholder="e.g. 450 kcal" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Image</label>
                @if ($isEdit && $menuItem->image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($menuItem->image) }}" alt="{{ $menuItem->name }}" class="w-24 h-24 rounded-lg object-cover">
                    </div>
                @endif
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-50 file:text-amber-700 file:font-medium hover:file:bg-amber-100">
                <p class="text-xs text-slate-500 mt-1">JPEG, PNG, or WebP. Max 2MB.</p>
            </div>

            <div class="grid grid-cols-3 gap-4 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', $isEdit ? $menuItem->is_available : true) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    <span class="text-sm text-slate-700">Available</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_vegetarian" value="1" {{ old('is_vegetarian', $isEdit ? $menuItem->is_vegetarian : false) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    <span class="text-sm text-slate-700">Vegetarian</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_spicy" value="1" {{ old('is_spicy', $isEdit ? $menuItem->is_spicy : false) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    <span class="text-sm text-slate-700">Spicy</span>
                </label>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-amber-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-amber-600">{{ $isEdit ? 'Update' : 'Create' }}</button>
                <a href="{{ route('admin.menu-items.index') }}" class="px-5 py-2 text-sm text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
