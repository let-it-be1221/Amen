<x-app-layout>
    <x-slot name="title">Reports - Supervisor</x-slot>
    <x-slot name="page-title">Reports & Analytics</x-slot>
    <x-slot name="page-subtitle">{{ $periodLabel }} • {{ $from->format('M j') }} - {{ $to->format('M j, Y') }}</x-slot>

    <div class="space-y-6">
        <!-- Period selector -->
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex flex-wrap gap-2 items-center">
                <span class="text-sm text-slate-600 mr-2">Period:</span>
                <a href="{{ route('supervisor.reports', ['period' => 'daily']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $period === 'daily' ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Today</a>
                <a href="{{ route('supervisor.reports', ['period' => 'weekly']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $period === 'weekly' ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">This Week</a>
                <a href="{{ route('supervisor.reports', ['period' => 'monthly']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $period === 'monthly' ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">This Month</a>
                <a href="{{ route('supervisor.reports', ['period' => 'yearly']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $period === 'yearly' ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">This Year</a>
                <form method="GET" class="ml-auto flex gap-2">
                    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="rounded-lg border-slate-300 text-sm">
                    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="rounded-lg border-slate-300 text-sm">
                    <button type="submit" class="bg-slate-900 text-white px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-slate-800">Apply</button>
                </form>
            </div>
        </div>

        <!-- Top stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Total Revenue" value="${{ number_format($totalRevenue, 2) }}" icon="cash" color="emerald" subtitle="{{ $paidOrders }} paid orders" />
            <x-stat-card title="Avg Order Value" value="${{ number_format($avgOrderValue, 2) }}" icon="cash" color="blue" />
            <x-stat-card title="Total Orders" value="{{ number_format($totalOrders) }}" icon="list" color="violet" subtitle="{{ $activeOrders }} active" />
            <x-stat-card title="Cancelled" value="{{ $cancelledOrders }}" icon="list" color="rose" />
        </div>

        <!-- Revenue breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Revenue Breakdown</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Tax Collected</span>
                        <span class="font-medium text-slate-900">${{ number_format($totalTax, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Service Charge</span>
                        <span class="font-medium text-slate-900">${{ number_format($totalServiceCharge, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Discounts Given</span>
                        <span class="font-medium text-rose-600">-${{ number_format($totalDiscount, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-100">
                        <span class="font-semibold text-slate-900">Net Revenue</span>
                        <span class="font-bold text-emerald-700">${{ number_format($totalRevenue - $totalDiscount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Orders by status -->
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Orders by Status</h3>
                <div class="space-y-2">
                    @foreach ($ordersByStatus as $status => $data)
                        <div class="flex items-center justify-between">
                            <x-status-badge :status="$status" />
                            <span class="text-sm font-semibold text-slate-900">{{ $data['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top performers summary -->
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Top Waiters</h3>
                @forelse ($topWaiters as $waiter)
                    <div class="flex items-center justify-between py-1.5 text-sm">
                        <span class="text-slate-700">{{ $waiter->name }}</span>
                        <span class="font-medium text-slate-900">{{ $waiter->total_orders }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No data</p>
                @endforelse
            </div>
        </div>

        <!-- Revenue by day -->
        @if ($revenueByDay->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Revenue Trend</h3>
            <div class="space-y-2">
                @php
                    $maxRevenue = $revenueByDay->max('total');
                @endphp
                @foreach ($revenueByDay as $day)
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-500 w-24 flex-shrink-0">{{ \Carbon\Carbon::parse($day->date)->format('M j') }}</span>
                        <div class="flex-1 bg-slate-100 rounded-full h-6 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-full rounded-full flex items-center justify-end pr-2" style="width: {{ ($day->total / max($maxRevenue, 1)) * 100 }}%">
                                <span class="text-xs text-white font-semibold">${{ number_format($day->total, 0) }}</span>
                            </div>
                        </div>
                        <span class="text-xs text-slate-500 w-12 text-right">{{ $day->count }} orders</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Top cookers -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Top Cookers</h3>
            @forelse ($topCookers as $cooker)
                <div class="flex items-center justify-between py-1.5 text-sm">
                    <span class="text-slate-700">{{ $cooker->name }}</span>
                    <span class="font-medium text-slate-900">{{ $cooker->total_orders }} orders</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No data</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
