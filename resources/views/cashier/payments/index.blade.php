<x-app-layout>
    <x-slot name="title">Payments - Cashier</x-slot>
    <x-slot name="page-title">Payments</x-slot>
    <x-slot name="page-subtitle">Process payments and view transactions</x-slot>

    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card title="Today's Revenue" value="${{ number_format($todayRevenue, 2) }}" icon="cash" color="emerald" />
            <x-stat-card title="Today's Transactions" value="{{ $todayTransactions }}" icon="list" color="blue" />
            <x-stat-card title="Pending Payments" value="{{ $pendingPayments->total() }}" icon="list" color="amber" />
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <form method="GET" class="flex gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by order number, customer name, or phone..." class="flex-1 rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-800">Search</button>
            </form>
        </div>

        <!-- Pending payments -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Orders Awaiting Payment</h3>
                <p class="text-sm text-slate-500 mt-1">Only delivered orders are eligible for payment</p>
            </div>
            @if ($pendingPayments->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <p class="mt-2 text-sm text-slate-500">No pending payments</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Order #</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Waiter</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Items</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Total</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Delivered</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($pendingPayments as $order)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $order->order_number }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $order->customer_name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $order->waiter?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $order->items->count() }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-500">{{ $order->delivered_at?->diffForHumans() ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('cashier.payments.create', $order) }}" class="bg-emerald-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-emerald-600">Process</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-200">{{ $pendingPayments->links() }}</div>
            @endif
        </div>

        <!-- Recent transactions -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Recent Transactions</h3>
            </div>
            @if ($recentPayments->isEmpty())
                <div class="p-12 text-center text-sm text-slate-500">No recent transactions</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Receipt #</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Order #</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Method</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Total</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentPayments as $payment)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $payment->payment_number }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $payment->order->order_number }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $payment->methodLabel() }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-slate-900">${{ number_format($payment->total, 2) }}</td>
                                    <td class="px-4 py-3"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">{{ $payment->statusLabel() }}</span></td>
                                    <td class="px-4 py-3 text-sm text-slate-500">{{ $payment->created_at->format('M j, Y g:i A') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('cashier.payments.receipt', $payment) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">View →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
