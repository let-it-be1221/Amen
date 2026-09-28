<x-app-layout>
    <x-slot name="title">Order {{ $order->order_number }}</x-slot>
    <x-slot name="page-title">Order Details</x-slot>
    <x-slot name="page-subtitle">{{ $order->order_number }}</x-slot>

    <div class="space-y-6">
        <!-- Back link -->
        <div>
            <a href="{{ route('waiter.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                Back to orders
            </a>
        </div>

        <!-- Status banner -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-2xl font-bold text-slate-900">{{ $order->order_number }}</h2>
                        <x-status-badge :status="$order->status" />
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Created {{ $order->created_at->format('M j, Y g:i A') }} by {{ $order->waiter?->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-bold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Workflow timeline -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Order Progress</h3>
            <div class="flex items-center justify-between relative overflow-x-auto">
                @php
                    $steps = [
                        ['key' => 'ordered', 'label' => 'Ordered', 'time' => $order->created_at],
                        ['key' => 'accepted', 'label' => 'Accepted', 'time' => $order->accepted_at],
                        ['key' => 'cooking', 'label' => 'Cooking', 'time' => $order->cooking_started_at],
                        ['key' => 'ready', 'label' => 'Ready', 'time' => $order->ready_at],
                        ['key' => 'delivered', 'label' => 'Delivered', 'time' => $order->delivered_at],
                        ['key' => 'paid', 'label' => 'Paid', 'time' => $order->paid_at],
                    ];
                    $currentIdx = array_search($order->status, array_column($steps, 'key'));
                    if ($order->status === 'cancelled') $currentIdx = -1;
                @endphp
                @foreach ($steps as $i => $step)
                    <div class="flex-1 flex flex-col items-center text-center relative min-w-[80px]">
                        @if ($i < count($steps) - 1)
                            <div class="absolute top-4 left-1/2 w-full h-0.5 {{ $i < $currentIdx ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                        @endif
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold relative z-10 {{ $i <= $currentIdx && $currentIdx >= 0 ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500' }}">
                            @if ($i < $currentIdx || ($i === $currentIdx && $order->status !== 'ordered'))
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <p class="text-xs mt-2 {{ $i <= $currentIdx && $currentIdx >= 0 ? 'text-slate-900 font-medium' : 'text-slate-500' }}">{{ $step['label'] }}</p>
                        <p class="text-[10px] text-slate-400">{{ $step['time']?->format('M j, g:i A') ?? '—' }}</p>
                    </div>
                @endforeach
            </div>
            @if ($order->status === 'cancelled')
                <div class="mt-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-sm text-rose-700">
                    <strong>Order cancelled:</strong> {{ $order->cancelled_at?->format('M j, Y g:i A') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Items -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-base font-semibold text-slate-900">Order Items</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach ($order->items as $item)
                            <div class="p-4 flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900">{{ $item->menu_item_name }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">${{ number_format($item->menu_item_price, 2) }} × {{ $item->quantity }}</p>
                                    @if ($item->notes)
                                        <p class="text-xs text-slate-500 italic mt-1">"{{ $item->notes }}"</p>
                                    @endif
                                </div>
                                <p class="text-sm font-semibold text-slate-900">${{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-700">Total</span>
                        <span class="text-lg font-bold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</span>
                    </div>
                </div>

                <!-- History -->
                @if ($order->statusHistory->isNotEmpty())
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-base font-semibold text-slate-900">History</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach ($order->statusHistory as $history)
                            <div class="p-4 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-semibold text-slate-700 flex-shrink-0">
                                    {{ strtoupper(substr($history->user?->name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-900">
                                        <span class="font-medium">{{ $history->user?->name ?? 'System' }}</span> changed status to
                                        <x-status-badge :status="$history->status" />
                                    </p>
                                    @if ($history->notes)
                                        <p class="text-xs text-slate-500 mt-1">{{ $history->notes }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $history->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Customer Info</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Name</dt>
                            <dd class="text-slate-900">{{ $order->customer_name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Phone</dt>
                            <dd class="text-slate-900">{{ $order->customer_phone ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Guests</dt>
                            <dd class="text-slate-900">{{ $order->customer_count }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Table</dt>
                            <dd class="text-slate-900">{{ $order->table?->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Type</dt>
                            <dd class="text-slate-900">{{ $order->orderTypeLabel() }}</dd>
                        </div>
                    </dl>
                    @if ($order->notes)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <p class="text-xs text-slate-500 mb-1">Notes</p>
                            <p class="text-sm text-slate-900">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Staff</h3>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-slate-500 text-xs">Waiter</dt>
                            <dd class="text-slate-900 font-medium">{{ $order->waiter?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 text-xs">Cooker</dt>
                            <dd class="text-slate-900 font-medium">{{ $order->cooker?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 text-xs">Cashier</dt>
                            <dd class="text-slate-900 font-medium">{{ $order->cashier?->name ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                @if (in_array($order->status, ['ordered', 'accepted']))
                <div class="bg-white rounded-xl border border-rose-200 p-6">
                    <h3 class="text-sm font-semibold text-rose-700 mb-2">Cancel Order</h3>
                    <p class="text-xs text-slate-500 mb-3">Cancel this order if it was placed by mistake.</p>
                    <form action="{{ route('waiter.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                        @csrf
                        <textarea name="cancel_reason" rows="2" required class="w-full rounded-lg border-rose-300 text-sm focus:border-rose-500 focus:ring-rose-500 mb-2" placeholder="Reason for cancellation"></textarea>
                        <button type="submit" class="w-full bg-rose-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Cancel Order</button>
                    </form>
                </div>
                @endif

                @if ($canReassign && $otherWaiters->isNotEmpty())
                <div class="bg-white rounded-xl border border-blue-200 p-6">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                        <h3 class="text-sm font-semibold text-blue-700">Reassign Order</h3>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">End-of-shift? Hand this order off to another waiter. They will take over delivery and tracking.</p>
                    <form action="{{ route('waiter.orders.reassign', $order) }}" method="POST" onsubmit="return confirm('Hand this order off to the selected waiter?')">
                        @csrf
                        <select name="new_waiter_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 mb-2">
                            <option value="">— Select a waiter —</option>
                            @foreach ($otherWaiters as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}@if ($w->phone) — {{ $w->phone }}@endif</option>
                            @endforeach
                        </select>
                        <textarea name="reason" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 mb-2" placeholder="Reason for handoff (optional)..."></textarea>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Reassign Order</button>
                    </form>
                </div>
                @endif

                @if ($order->status === 'ready')
                <div class="bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl p-6 text-white shadow-lg shadow-emerald-500/20">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <h3 class="text-sm font-bold">Ready for Delivery!</h3>
                    </div>
                    <p class="text-xs text-emerald-50 mb-4">The kitchen has finished preparing this order. Please serve it to the customer and confirm delivery below.</p>
                    <form action="{{ route('waiter.orders.deliver', $order) }}" method="POST" onsubmit="return confirm('Confirm that this order has been delivered to the customer?')">
                        @csrf
                        <button type="submit" class="w-full bg-white text-emerald-700 py-2.5 rounded-lg text-sm font-semibold hover:bg-emerald-50 transition-colors">
                            ✓ Confirm Delivered
                        </button>
                    </form>
                </div>
                @endif

                @if ($order->status === 'delivered')
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <p class="text-sm font-medium text-emerald-800">Delivered — awaiting payment</p>
                    </div>
                    <p class="text-xs text-emerald-700 mt-1">The cashier has been notified. The customer can pay at the counter.</p>
                </div>
                @endif

                @if ($order->status === 'paid')
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15" /></svg>
                        <p class="text-sm font-medium text-green-800">Paid — order complete</p>
                    </div>
                    <p class="text-xs text-green-700 mt-1">This order has been paid and is closed.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
