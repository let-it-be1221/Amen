<x-app-layout>
    <x-slot name="title">Waiter Dashboard</x-slot>
    <x-slot name="page-title">My Dashboard</x-slot>
    <x-slot name="page-subtitle">Manage your orders</x-slot>

    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Active Orders" value="{{ $activeOrders }}" icon="fire" color="amber" />
            <x-stat-card title="Orders Today" value="{{ $totalOrdersToday }}" icon="list" color="blue" />
            <x-stat-card title="Total Revenue" value="${{ number_format($totalRevenue, 2) }}" icon="cash" color="emerald" subtitle="From your paid orders" />
            <x-stat-card title="Available Items" value="{{ $menuItems->count() }}" icon="menu" color="violet" />
        </div>

        <!-- Quick action -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-6 text-white shadow-lg shadow-amber-500/20">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Ready to take a new order?</h2>
                    <p class="text-amber-50 text-sm mt-1">Tap below to create a new order for your customers.</p>
                </div>
                <a href="{{ route('waiter.orders.create') }}" class="inline-flex items-center gap-2 bg-white text-amber-600 px-5 py-2.5 rounded-lg font-semibold hover:bg-amber-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    New Order
                </a>
            </div>
        </div>

        <!-- Recent orders -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">My Recent Orders</h3>
                <a href="{{ route('waiter.orders.index') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($myOrders as $order)
                    <a href="{{ route('waiter.orders.show', $order) }}" class="block p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-slate-900">{{ $order->order_number }}</p>
                                    <x-status-badge :status="$order->status" />
                                </div>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $order->items->count() }} item(s) • {{ $order->created_at->diffForHumans() }}
                                    @if ($order->table) • {{ $order->table->name }}@endif
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-semibold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" /></svg>
                        <p class="mt-2 text-sm text-slate-500">No orders yet</p>
                        <a href="{{ route('waiter.orders.create') }}" class="mt-3 inline-flex items-center gap-1 text-sm text-amber-600 hover:text-amber-700 font-medium">Create your first order →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
