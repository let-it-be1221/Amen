<x-app-layout>
    <x-slot name="title">All Orders - Supervisor</x-slot>
    <x-slot name="page-title">All Orders</x-slot>
    <x-slot name="page-subtitle">Monitor every order in the system</x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @if (($filters['status'] ?? '') === $key) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Waiter</label>
                    <select name="waiter_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All waiters</option>
                        @foreach ($waiters as $waiter)
                            <option value="{{ $waiter->id }}" @if (($filters['waiter_id'] ?? '') == $waiter->id) selected @endif>{{ $waiter->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Cooker</label>
                    <select name="cooker_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All cookers</option>
                        @foreach ($cookers as $cooker)
                            <option value="{{ $cooker->id }}" @if (($filters['cooker_id'] ?? '') == $cooker->id) selected @endif>{{ $cooker->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2 lg:col-span-5 flex gap-2 justify-end">
                    <a href="{{ route('supervisor.orders') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-900">Clear filters</a>
                    <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-600">Filter</button>
                </div>
            </form>
        </div>

        <!-- Orders table -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Order #</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Waiter</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Cooker</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Cashier</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Items</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Total</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $order->order_number }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $order->waiter?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $order->cooker?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $order->cashier?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">
                                    @if ($order->customer_name){{ $order->customer_name }}@else <span class="text-slate-400">—</span> @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $order->items->count() }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$order->status" /></td>
                                <td class="px-4 py-3 text-sm text-slate-500">{{ $order->created_at->format('M j, g:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-12 text-sm text-slate-500">No orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">{{ $orders->links() }}</div>
        </div>
    </div>
</x-app-layout>
