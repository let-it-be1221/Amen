<x-app-layout>
    <x-slot name="title">Cashier Dashboard</x-slot>
    <x-slot name="page-title">Dashboard</x-slot>
    <x-slot name="page-subtitle">Process payments for delivered orders</x-slot>

    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Pending Payments" value="{{ $pendingPayments->count() }}" icon="list" color="amber" subtitle="Awaiting payment" />
            <x-stat-card title="Today's Revenue" value="${{ number_format($todayRevenue, 2) }}" icon="cash" color="emerald" subtitle="From your transactions" />
            <x-stat-card title="Today's Transactions" value="{{ $todayTransactions }}" icon="cash" color="blue" />
            <x-stat-card title="Recent Payments" value="{{ $recentPayments->count() }}" icon="list" color="violet" />
        </div>

        <!-- Quick action -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl p-6 text-white shadow-lg shadow-emerald-500/20">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Payments</h2>
                    <p class="text-emerald-50 text-sm mt-1">{{ $pendingPayments->count() }} orders awaiting payment.</p>
                </div>
                <a href="{{ route('cashier.payments.index') }}" class="inline-flex items-center gap-2 bg-white text-emerald-600 px-5 py-2.5 rounded-lg font-semibold hover:bg-emerald-50 transition-colors">
                    View Payments
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                </a>
            </div>
        </div>

        <!-- Pending payments -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Orders Awaiting Payment</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($pendingPayments as $order)
                    <div class="p-4 flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-900">{{ $order->order_number }}</p>
                                <x-status-badge :status="$order->status" />
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Waiter: {{ $order->waiter?->name }} • {{ $order->items->count() }} items • Delivered {{ $order->delivered_at?->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-semibold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</p>
                            <a href="{{ route('cashier.payments.create', $order) }}" class="bg-emerald-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-emerald-600">Process Payment</a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <p class="mt-2 text-sm text-slate-500">No pending payments. All caught up!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent transactions -->
        @if ($recentPayments->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Recent Transactions</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($recentPayments as $payment)
                    <div class="p-4 flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-900">{{ $payment->payment_number }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">Order: {{ $payment->order->order_number }} • {{ $payment->methodLabel() }} • {{ $payment->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-semibold text-slate-900">${{ number_format($payment->total, 2) }}</p>
                            <a href="{{ route('cashier.payments.receipt', $payment) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Receipt</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
