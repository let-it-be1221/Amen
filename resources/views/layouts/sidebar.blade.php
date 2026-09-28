@php
    $user = auth()->user();
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $menus = [
        'admin' => [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'users'],
            ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => 'tag'],
            ['route' => 'admin.menu-items.index', 'label' => 'Menu Items', 'icon' => 'menu'],
            ['route' => 'admin.tables.index', 'label' => 'Tables', 'icon' => 'table'],
        ],
        'supervisor' => [
            ['route' => 'supervisor.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'supervisor.reports', 'label' => 'Reports', 'icon' => 'reports'],
            ['route' => 'supervisor.orders', 'label' => 'All Orders', 'icon' => 'list'],
            ['route' => 'admin.menu-items.index', 'label' => 'Menu Items', 'icon' => 'menu'],
            ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => 'tag'],
        ],
        'waiter' => [
            ['route' => 'waiter.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'waiter.orders.create', 'label' => 'New Order', 'icon' => 'plus'],
            ['route' => 'waiter.orders.index', 'label' => 'My Orders', 'icon' => 'list'],
        ],
        'cooker' => [
            ['route' => 'cooker.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'cooker.kitchen', 'label' => 'Kitchen', 'icon' => 'fire'],
        ],
        'cashier' => [
            ['route' => 'cashier.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'cashier.payments.index', 'label' => 'Payments', 'icon' => 'cash'],
        ],
    ];
    $userMenu = $menus[$user->role] ?? [];
@endphp

<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen bg-slate-900 text-slate-100 transition-transform -translate-x-full md:translate-x-0 border-r border-slate-800">
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-slate-900 font-bold text-xl shadow-lg shadow-amber-500/20">
                A
            </div>
            <div class="leading-tight">
                <p class="text-sm font-bold">Amen</p>
                <p class="text-xs text-slate-400">Restaurant System</p>
            </div>
        </a>
        <button id="sidebar-close" class="md:hidden text-slate-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <div class="px-3 py-4">
        <div class="px-3 py-2 mb-2 rounded-lg bg-slate-800/50 border border-slate-700/50">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Signed in as</p>
            <p class="text-sm font-medium text-slate-100 truncate">{{ $user->name }}</p>
            <p class="text-xs text-amber-400 font-medium">{{ $user->roleLabel() }}</p>
        </div>

        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $currentRoute === 'dashboard' ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.5a.75.75 0 0 0 .75.75h4.5a.75.75 0 0 0 .75-.75V15a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v5.25a.75.75 0 0 0 .75.75h4.5a.75.75 0 0 0 .75-.75V9.75M8.25 21h8.25" /></svg>
                Dashboard
            </a>

            @foreach ($userMenu as $item)
                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ str_starts_with($currentRoute, explode('.', $item['route'])[0] . '.' . explode('.', $item['route'])[1]) ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    @include('partials.nav-icons', ['icon' => $item['icon']])
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-slate-800">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-400 transition-colors" title="Sign out">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 hidden md:hidden" onclick="document.getElementById('sidebar').classList.remove('-translate-x-full'); this.classList.add('hidden')"></div>
