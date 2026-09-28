<x-app-layout>
    <x-slot name="title">Kitchen</x-slot>
    <x-slot name="page-title">Kitchen</x-slot>
    <x-slot name="page-subtitle">Accept orders, cook, and mark them ready</x-slot>

    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white rounded-xl border border-blue-200 p-4">
                <p class="text-xs font-medium text-blue-600">Incoming</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $incomingOrders->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-amber-200 p-4">
                <p class="text-xs font-medium text-amber-600">Cooking</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $myActiveOrders->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-violet-200 p-4">
                <p class="text-xs font-medium text-violet-600">Ready</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $readyOrders->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-emerald-200 p-4">
                <p class="text-xs font-medium text-emerald-600">Today's Completed</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $completedToday }}</p>
            </div>
        </div>

        <!-- Incoming orders -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 bg-blue-50 border-b border-blue-100">
                <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Incoming Orders ({{ $incomingOrders->count() }})
                </h3>
            </div>
            <div class="p-4">
                @forelse ($incomingOrders as $order)
                    <div class="bg-slate-50 rounded-lg p-4 mb-3 last:mb-0">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-slate-900">{{ $order->order_number }}</p>
                                    <span class="text-xs text-slate-500">{{ $order->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">Waiter: {{ $order->waiter?->name }} • {{ $order->table?->name ?? 'No table' }}</p>
                                @if ($order->notes)
                                    <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded p-2 mt-2">{{ $order->notes }}</p>
                                @endif
                            </div>
                            <form action="{{ route('cooker.orders.accept', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700 whitespace-nowrap">Accept →</button>
                            </form>
                        </div>
                        <div class="space-y-1.5">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-medium text-slate-900">{{ $item->quantity }}×</span>
                                        <span class="text-slate-700 ml-1">{{ $item->menu_item_name }}</span>
                                        @if ($item->notes)<span class="text-slate-500 italic"> — {{ $item->notes }}</span>@endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-sm text-slate-500">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" /></svg>
                        No incoming orders. Great job!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Currently cooking -->
        @if ($myActiveOrders->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 bg-amber-50 border-b border-amber-100">
                <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Currently Cooking ({{ $myActiveOrders->count() }})
                </h3>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($myActiveOrders as $order)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold text-slate-900">{{ $order->order_number }}</p>
                            <x-status-badge :status="$order->status" />
                        </div>
                        <p class="text-xs text-slate-500 mb-3">{{ $order->waiter?->name }} • {{ $order->table?->name ?? 'No table' }}</p>
                        <div class="space-y-1 mb-3">
                            @foreach ($order->items->take(3) as $item)
                                <p class="text-xs text-slate-700"><span class="font-medium">{{ $item->quantity }}×</span> {{ $item->menu_item_name }}</p>
                            @endforeach
                            @if ($order->items->count() > 3)
                                <p class="text-xs text-slate-500">+ {{ $order->items->count() - 3 }} more</p>
                            @endif
                        </div>
                        @if ($order->status === 'accepted')
                            <form action="{{ route('cooker.orders.start', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-amber-500 text-white py-1.5 rounded text-xs font-medium hover:bg-amber-600">Start Cooking</button>
                            </form>
                        @elseif ($order->status === 'cooking')
                            <form action="{{ route('cooker.orders.ready', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-emerald-500 text-white py-1.5 rounded text-xs font-medium hover:bg-emerald-600">Mark Ready</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Ready orders -->
        @if ($readyOrders->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 bg-violet-50 border-b border-violet-100">
                <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                    Ready for Delivery ({{ $readyOrders->count() }})
                </h3>
                <p class="text-xs text-slate-500 mt-1">These orders have been cooked. The waiter will deliver them to the customer.</p>
            </div>
            <div class="p-4 space-y-3">
                @foreach ($readyOrders as $order)
                    <div class="flex items-center justify-between bg-violet-50 border border-violet-200 rounded-lg p-3">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $order->order_number }}</p>
                            <p class="text-xs text-slate-500">
                                Waiter: {{ $order->waiter?->name }}
                                @if ($order->ready_at) • Ready since {{ $order->ready_at->diffForHumans() }}@endif
                            </p>
                        </div>
                        <a href="{{ route('cooker.orders.show', $order) }}" class="text-violet-600 hover:text-violet-700 text-xs font-medium">View →</a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- History -->
        @if ($historyOrders->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900">History (Prepared by me)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Order #</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Waiter</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Items</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Status</th>
                            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-600 uppercase">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($historyOrders as $order)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2 text-sm font-medium text-slate-900">{{ $order->order_number }}</td>
                                <td class="px-4 py-2 text-sm text-slate-600">{{ $order->waiter?->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-slate-600">{{ $order->items->count() }}</td>
                                <td class="px-4 py-2"><x-status-badge :status="$order->status" /></td>
                                <td class="px-4 py-2 text-sm text-slate-500">{{ $order->updated_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">{{ $historyOrders->links() }}</div>
        </div>
        @endif
    </div>
</x-app-layout>
