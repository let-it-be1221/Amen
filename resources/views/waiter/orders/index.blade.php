<x-app-layout>
    <x-slot name="title">My Orders - Waiter</x-slot>
    <x-slot name="page-title">My Orders</x-slot>
    <x-slot name="page-subtitle">All orders you have taken</x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Filter by status</label>
                    <select name="status" onchange="this.form.submit()" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @if ($currentStatus === $key) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('waiter.orders.create') }}" class="inline-flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-amber-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        New Order
                    </a>
                </div>
            </form>
        </div>

        <!-- Orders list -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            @if ($orders->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" /></svg>
                    <p class="mt-2 text-sm text-slate-500">No orders found</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Order #</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Items</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Total</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Created</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $order->order_number }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        @if ($order->customer_name)
                                            {{ $order->customer_name }}
                                            @if ($order->customer_phone)<br><span class="text-xs text-slate-400">{{ $order->customer_phone }}</span>@endif
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                        @if ($order->table)<br><span class="text-xs text-slate-500">{{ $order->table->name }}</span>@endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $order->items->count() }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</td>
                                    <td class="px-4 py-3"><x-status-badge :status="$order->status" /></td>
                                    <td class="px-4 py-3 text-sm text-slate-500">{{ $order->created_at->diffForHumans() }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('waiter.orders.show', $order) }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-200">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
