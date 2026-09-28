@php
    $isEdit = isset($user);
    $action = $isEdit ? route('admin.users.update', $user) : route('admin.users.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<x-app-layout>
    <x-slot name="title">{{ $isEdit ? 'Edit' : 'Create' }} User</x-slot>
    <x-slot name="page-title">{{ $isEdit ? 'Edit' : 'Create' }} User</x-slot>
    <x-slot name="page-subtitle">{{ $isEdit ? $user->name : 'Add a new system user' }}</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back to users
        </a>

        <form action="{{ $action }}" method="POST" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
            @csrf
            @method($method)

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $isEdit ? $user->name : '') }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $isEdit ? $user->email : '') }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password {{ $isEdit ? '(leave blank to keep current)' : '' }}</label>
                <input type="password" name="password" {{ $isEdit ? '' : 'required' }} class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                <select name="role" required class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @foreach ($roles as $key => $label)
                        <option value="{{ $key }}" @if (old('role', $isEdit ? $user->role : '') === $key) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone (optional)</label>
                <input type="text" name="phone" value="{{ old('phone', $isEdit ? $user->phone : '') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $isEdit ? $user->is_active : true) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <label for="is_active" class="text-sm text-slate-700">Active (user can sign in)</label>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-amber-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-amber-600">{{ $isEdit ? 'Update User' : 'Create User' }}</button>
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2 text-sm text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
