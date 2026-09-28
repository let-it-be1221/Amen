@php
    $isEdit = isset($table);
    $action = $isEdit ? route('admin.tables.update', $table) : route('admin.tables.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<x-app-layout>
    <x-slot name="title">{{ $isEdit ? 'Edit' : 'Create' }} Table</x-slot>
    <x-slot name="page-title">{{ $isEdit ? 'Edit' : 'Create' }} Table</x-slot>
    <x-slot name="page-subtitle">{{ $isEdit ? $table->name : 'Add a new restaurant table' }}</x-slot>

    <div class="max-w-xl">
        <a href="{{ route('admin.tables.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back to tables
        </a>

        <form action="{{ $action }}" method="POST" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
            @csrf
            @method($method)

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Table Name / Number</label>
                <input type="text" name="name" value="{{ old('name', $isEdit ? $table->name : '') }}" required placeholder="e.g. T-01 or Table 1" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Seats</label>
                <input type="number" name="seats" min="1" max="50" value="{{ old('seats', $isEdit ? $table->seats : 4) }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Location (optional)</label>
                <input type="text" name="location" value="{{ old('location', $isEdit ? $table->location : '') }}" placeholder="Indoor, Outdoor, VIP..." class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>

            @if ($isEdit)
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @foreach (\App\Models\RestaurantTable::statuses() as $key => $label)
                        <option value="{{ $key }}" @if (old('status', $table->status) === $key) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $isEdit ? $table->is_active : true) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <label for="is_active" class="text-sm text-slate-700">Active</label>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-amber-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-amber-600">{{ $isEdit ? 'Update' : 'Create' }}</button>
                <a href="{{ route('admin.tables.index') }}" class="px-5 py-2 text-sm text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
