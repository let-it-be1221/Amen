@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-20 bg-white border-b border-slate-200 h-16 flex items-center px-4 sm:px-6 lg:px-8">
    <button id="sidebar-open" class="md:hidden text-slate-500 hover:text-slate-900 mr-3">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
    </button>

    <div class="flex-1 min-w-0">
        <h1 class="text-lg sm:text-xl font-semibold text-slate-900 truncate">@yield('page-title', 'Dashboard')</h1>
        <p class="text-xs text-slate-500 hidden sm:block">@yield('page-subtitle', 'Welcome back')</p>
    </div>

    <div class="flex items-center gap-3">
        <!-- Date display -->
        <div class="hidden lg:flex flex-col items-end text-xs">
            <span class="text-slate-500">{{ now()->format('l, F j, Y') }}</span>
            <span class="text-slate-400">{{ now()->format('g:i A') }}</span>
        </div>

        <!-- Profile dropdown -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open" class="flex items-center gap-2 rounded-full hover:bg-slate-100 px-2 py-1 transition-colors">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 text-white flex items-center justify-center text-sm font-semibold uppercase">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-sm font-medium text-slate-900 leading-tight">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $user->roleLabel() }}</p>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
            </button>

            <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-slate-200 py-2" style="display: none;">
                <div class="px-4 py-2 border-b border-slate-100">
                    <p class="text-sm font-medium text-slate-900 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $user->email }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    My Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const openBtn = document.getElementById('sidebar-open');
    const closeBtn = document.getElementById('sidebar-close');

    if (openBtn) {
        openBtn.addEventListener('click', function() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        });
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    }
});
</script>
