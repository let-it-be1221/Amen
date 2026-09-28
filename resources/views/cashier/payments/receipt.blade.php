<x-app-layout>
    <x-slot name="title">Receipt {{ $payment->payment_number }}</x-slot>
    <x-slot name="page-title">Receipt</x-slot>
    <x-slot name="page-subtitle">{{ $payment->payment_number }}</x-slot>

    <div class="max-w-2xl mx-auto space-y-4">
        <a href="{{ route('cashier.payments.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back to payments
        </a>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-amber-500 mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-900" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <h2 class="text-2xl font-bold">Amen Restaurant</h2>
                <p class="text-sm text-slate-300 mt-1">Thank you for dining with us!</p>
            </div>

            <!-- Receipt body -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm border-b border-dashed border-slate-200 pb-4">
                    <div>
                        <p class="text-xs text-slate-500">Receipt #</p>
                        <p class="font-medium text-slate-900">{{ $payment->payment_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Order #</p>
                        <p class="font-medium text-slate-900">{{ $payment->order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Date</p>
                        <p class="font-medium text-slate-900">{{ $payment->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Cashier</p>
                        <p class="font-medium text-slate-900">{{ $payment->cashier->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Waiter</p>
                        <p class="font-medium text-slate-900">{{ $payment->order->waiter?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Payment Method</p>
                        <p class="font-medium text-slate-900">{{ $payment->methodLabel() }}</p>
                    </div>
                </div>

                <!-- Items -->
                <div class="space-y-2 border-b border-dashed border-slate-200 pb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Items</p>
                    @foreach ($payment->order->items as $item)
                        <div class="flex justify-between text-sm">
                            <div>
                                <p class="text-slate-900">{{ $item->quantity }}× {{ $item->menu_item_name }}</p>
                                <p class="text-xs text-slate-500">${{ number_format($item->menu_item_price, 2) }} each</p>
                            </div>
                            <p class="font-medium text-slate-900">${{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="space-y-2 text-sm border-b border-dashed border-slate-200 pb-4">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-medium text-slate-900">${{ number_format($payment->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Tax</span>
                        <span class="font-medium text-slate-900">${{ number_format($payment->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Service Charge</span>
                        <span class="font-medium text-slate-900">${{ number_format($payment->service_charge, 2) }}</span>
                    </div>
                    @if ($payment->discount > 0)
                    <div class="flex justify-between">
                        <span class="text-slate-600">Discount</span>
                        <span class="font-medium text-rose-600">-${{ number_format($payment->discount, 2) }}</span>
                    </div>
                    @endif
                </div>

                <!-- Total -->
                <div class="flex justify-between items-center text-lg font-bold">
                    <span class="text-slate-900">Total</span>
                    <span class="text-slate-900">${{ number_format($payment->total, 2) }}</span>
                </div>

                <div class="space-y-1 text-sm border-t border-slate-200 pt-4">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Amount Paid</span>
                        <span class="font-medium text-slate-900">${{ number_format($payment->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Change</span>
                        <span class="font-medium text-emerald-700">${{ number_format($payment->change, 2) }}</span>
                    </div>
                </div>

                @if ($payment->notes)
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-slate-700">
                    <p class="text-xs font-semibold text-amber-700 mb-1">Notes</p>
                    {{ $payment->notes }}
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="bg-slate-50 border-t border-slate-200 p-4 text-center">
                <p class="text-xs text-slate-500">Thank you for your business!</p>
                <p class="text-xs text-slate-400 mt-1">This receipt was generated electronically.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <button onclick="window.print()" class="flex-1 bg-slate-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">Print Receipt</button>
            <a href="{{ route('cashier.payments.index') }}" class="flex-1 bg-white border border-slate-300 text-slate-700 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-50 text-center">Done</a>
        </div>
    </div>
</x-app-layout>
