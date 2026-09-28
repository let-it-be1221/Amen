<x-app-layout>
    <x-slot name="title">Order {{ $order->order_number }}</x-slot>
    <x-slot name="page-title">Order Details</x-slot>
    <x-slot name="page-subtitle">{{ $order->order_number }}</x-slot>

    <div class="space-y-6">
        <a href="{{ route('cooker.kitchen') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back to kitchen
        </a>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-slate-900">{{ $order->order_number }}</h2>
                        <x-status-badge :status="$order->status" />
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Waiter: {{ $order->waiter?->name }} • {{ $order->created_at->format('M j, Y g:i A') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">{{ $order->items->count() }} items</p>
                    <p class="text-lg font-bold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-base font-semibold text-slate-900">Items to Prepare</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach ($order->items as $item)
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-900">{{ $item->menu_item_name }}</p>
                                        @if ($item->notes)
                                            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded p-2 mt-1">{{ $item->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-slate-900">{{ $item->quantity }}×</p>
                                        <p class="text-xs text-slate-500">${{ number_format($item->subtotal, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($order->notes)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-1">Order Notes</p>
                    <p class="text-sm text-slate-900">{{ $order->notes }}</p>
                </div>
                @endif
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Order Info</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Type</dt>
                            <dd class="text-slate-900">{{ $order->orderTypeLabel() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Table</dt>
                            <dd class="text-slate-900">{{ $order->table?->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Customer</dt>
                            <dd class="text-slate-900">{{ $order->customer_name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Guests</dt>
                            <dd class="text-slate-900">{{ $order->customer_count }}</dd>
                        </div>
                    </dl>
                </div>

                @if (in_array($order->status, ['accepted']))
                    <form action="{{ route('cooker.orders.start', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-amber-500 text-white py-2.5 rounded-lg font-semibold hover:bg-amber-600">Start Cooking</button>
                    </form>
                @elseif ($order->status === 'cooking')
                    <form action="{{ route('cooker.orders.ready', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-500 text-white py-2.5 rounded-lg font-semibold hover:bg-emerald-600">Mark as Ready</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
