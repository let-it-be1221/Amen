<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>
    <x-slot name="page-title">Dashboard</x-slot>
    <x-slot name="page-subtitle">Welcome back, {{ auth()->user()->name }}</x-slot>

    <div class="space-y-6">
        <!-- Stats grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Total Revenue" value="${{ number_format($stats['totalRevenue'], 2) }}" icon="cash" color="emerald" subtitle="All-time" />
            <x-stat-card title="Today's Revenue" value="${{ number_format($stats['todayRevenue'], 2) }}" icon="cash" color="amber" subtitle="{{ now()->format('M j, Y') }}" />
            <x-stat-card title="Total Orders" value="{{ number_format($stats['totalOrders']) }}" icon="list" color="blue" subtitle="{{ $stats['activeOrders'] }} active" />
            <x-stat-card title="Total Users" value="{{ number_format($stats['totalUsers']) }}" icon="users" color="violet" subtitle="{{ $stats['totalMenuItems'] }} menu items" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Orders by status -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-slate-900">Orders by Status</h3>
                    <a href="{{ route('supervisor.orders') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">View all →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach ($ordersByStatus as $status => $data)
                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                            <x-status-badge :status="$status" />
                            <p class="text-2xl font-bold text-slate-900 mt-2">{{ $data['count'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Users by role -->
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Users by Role</h3>
                <div class="space-y-3">
                    @foreach ($usersByRole as $role => $data)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">{{ $data['label'] }}</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $data['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent orders -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Recent Orders</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentOrders as $order)
                        <div class="p-4 hover:bg-slate-50 transition-colors flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-900">{{ $order->order_number }}</p>
                                <p class="text-xs text-slate-500">{{ $order->waiter?->name ?? 'N/A' }} • {{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</span>
                                <x-status-badge :status="$order->status" />
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-slate-500">No recent orders</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent activity -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Recent Activity</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentActivities as $log)
                        <div class="p-4 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex-shrink-0 flex items-center justify-center text-xs font-semibold text-slate-700">
                                {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-slate-900">
                                    <span class="font-medium">{{ $log->user?->name ?? 'System' }}</span>
                                    <span class="text-slate-500">{{ strtolower($log->action) }}</span>
                                </p>
                                <p class="text-xs text-slate-500">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-slate-500">No recent activity</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
