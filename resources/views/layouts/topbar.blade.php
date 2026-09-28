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
        <!-- Notifications bell (only for waiters - they get "order ready" alerts) -->
        @if (auth()->user()->isWaiter())
        <div class="relative" x-data="notificationsDropdown()" x-init="init()" @click.outside="open = false">
            <button @click="open = !open; if(!open) markAllSeen()" class="relative p-2 rounded-full hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                <span x-show="unreadCount > 0" x-cloak
                      x-text="unreadCount"
                      class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center animate-pulse"></span>
            </button>

            <div x-show="open" x-transition x-cloak
                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 py-2 z-50" style="display: none;">
                <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-900">Notifications</p>
                    <button @click="markAllRead()" x-show="unreadCount > 0" x-cloak
                            class="text-xs text-amber-600 hover:text-amber-700 font-medium">Mark all read</button>
                </div>
                <div class="max-h-80 overflow-y-auto">
                    <template x-if="notifications.length === 0">
                        <div class="p-6 text-center">
                            <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <p class="mt-2 text-xs text-slate-500">You're all caught up</p>
                        </div>
                    </template>
                    <template x-for="n in notifications" :key="n.id">
                        <a :href="n.order_url || '#'" @click.prevent="visit(n)"
                           class="block px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                           :class="{ 'bg-amber-50/50': !n.read }">
                            <div class="flex items-start gap-2">
                                <div class="w-7 h-7 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-slate-900" x-text="n.message"></p>
                                    <div class="mt-1 flex flex-wrap gap-x-2 gap-y-0.5 text-xs text-slate-500">
                                        <span x-show="n.table_name" x-text="'🍽 ' + n.table_name"></span>
                                        <span x-show="n.items_count" x-text="n.items_count + ' items'"></span>
                                        <span x-show="n.cooker_name" x-text="'👨‍🍳 ' + n.cooker_name"></span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1" x-text="n.created_at"></p>
                                </div>
                                <span x-show="!n.read" x-cloak class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0 mt-1"></span>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        <script>
        function notificationsDropdown() {
            return {
                open: false,
                unreadCount: 0,
                notifications: [],
                seenIds: new Set(),
                pollInterval: null,

                init() {
                    this.fetch();
                    // Poll every 20 seconds for new notifications
                    this.pollInterval = setInterval(() => this.fetch(), 20000);
                },

                async fetch() {
                    try {
                        const resp = await fetch('{{ route("notifications.index") }}', {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        });
                        if (!resp.ok) return;
                        const data = await resp.json();
                        // Track newly arrived unread notifications to play a subtle sound effect
                        const prevUnread = this.unreadCount;
                        this.unreadCount = data.unread_count;
                        this.notifications = data.notifications;
                        // If there are new notifications, briefly highlight the bell
                        if (data.unread_count > prevUnread && prevUnread !== null) {
                            // Optional: subtle browser notification
                            if ('Notification' in window && Notification.permission === 'granted') {
                                const latest = data.notifications[0];
                                if (latest) {
                                    new Notification('🍽 Order Ready', { body: latest.message });
                                }
                            }
                        }
                    } catch (e) {
                        // Silent fail - polling should be resilient
                    }
                },

                markAllSeen() {
                    // Local UI hint - server-side read happens on click
                    this.notifications.forEach(n => { n.read = true; });
                },

                async markAllRead() {
                    try {
                        await fetch('{{ route("notifications.mark-all-read") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            credentials: 'same-origin',
                        });
                        this.unreadCount = 0;
                        this.notifications.forEach(n => { n.read = true; });
                    } catch (e) {}
                },

                async visit(n) {
                    if (!n.read) {
                        // Mark as read on server first
                        try {
                            await fetch(`/notifications/${n.id}/read`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                credentials: 'same-origin',
                            });
                        } catch (e) {}
                    }
                    if (n.order_url) {
                        window.location.href = n.order_url;
                    }
                },
            };
        }
        </script>
        @endif

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
