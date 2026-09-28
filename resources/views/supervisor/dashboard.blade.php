<x-app-layout>
    <x-slot name="title">Supervisor Dashboard</x-slot>
    <x-slot name="page-title">Dashboard</x-slot>
    <x-slot name="page-subtitle">Monitor all orders and operations</x-slot>

    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Total Orders" value="{{ number_format($totalOrders) }}" icon="list" color="blue" />
            <x-stat-card title="Active Orders" value="{{ $activeOrders }}" icon="fire" color="amber" />
            <x-stat-card title="Total Revenue" value="${{ number_format($totalRevenue, 2) }}" icon="cash" color="emerald" />
            <x-stat-card title="Cancelled" value="{{ $cancelledOrders }}" icon="list" color="rose" />
        </div>

        <!-- Quick action -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl p-6 text-white shadow-lg shadow-indigo-500/20">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Reports & Analytics</h2>
                    <p class="text-indigo-50 text-sm mt-1">Daily, weekly, and monthly reports.</p>
                </div>
                <a href="{{ route('supervisor.reports') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 px-5 py-2.5 rounded-lg font-semibold hover:bg-indigo-50 transition-colors">
                    View Reports
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                </a>
            </div>
        </div>

        <!-- Orders by status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Orders by Status</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach ($ordersByStatus as $status => $data)
                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                            <x-status-badge :status="$status" />
                            <p class="text-2xl font-bold text-slate-900 mt-2">{{ $data['count'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Today's Performance</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Today's Orders</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $todayOrders }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Today's Revenue</span>
                        <span class="text-sm font-semibold text-emerald-700">${{ number_format($todayRevenue, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Active Orders</span>
                        <span class="text-sm font-semibold text-amber-700">{{ $activeOrders }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Cancelled Orders</span>
                        <span class="text-sm font-semibold text-rose-700">{{ $cancelledOrders }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top performers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Top Waiters</h3>
                @forelse ($topWaiters as $waiter)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr($waiter['name'], 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-slate-900">{{ $waiter['name'] }}</span>
                        </div>
                        <span class="text-sm font-semibold text-slate-900">{{ $waiter['total_orders'] }} orders</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 text-center py-4">No data yet</p>
                @endforelse
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Top Cookers</h3>
                @forelse ($topCookers as $cooker)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr($cooker['name'], 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-slate-900">{{ $cooker['name'] }}</span>
                        </div>
                        <span class="text-sm font-semibold text-slate-900">{{ $cooker['total_orders'] }} orders</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 text-center py-4">No data yet</p>
                @endforelse
            </div>
        </div>

        <!-- Recent orders -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Recent Orders</h3>
                <a href="{{ route('supervisor.orders') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Order #</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Waiter</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Cooker</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Items</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Total</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Status</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentOrders as $order)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2 text-sm font-medium text-slate-900">{{ $order->order_number }}</td>
                                <td class="px-4 py-2 text-sm text-slate-600">{{ $order->waiter?->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-slate-600">{{ $order->cooker?->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-slate-600">{{ $order->items->count() }}</td>
                                <td class="px-4 py-2 text-sm font-semibold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</td>
                                <td class="px-4 py-2"><x-status-badge :status="$order->status" /></td>
                                <td class="px-4 py-2 text-sm text-slate-500">{{ $order->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
