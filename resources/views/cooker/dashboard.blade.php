<x-app-layout>
    <x-slot name="title">Cooker Dashboard</x-slot>
    <x-slot name="page-title">Dashboard</x-slot>
    <x-slot name="page-subtitle">Kitchen overview</x-slot>

    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Incoming Orders" value="{{ $incomingOrders->count() }}" icon="plus" color="blue" subtitle="Awaiting acceptance" />
            <x-stat-card title="In Progress" value="{{ $assignedOrders->count() }}" icon="fire" color="amber" subtitle="Currently cooking" />
            <x-stat-card title="Ready" value="{{ $readyOrders->count() }}" icon="list" color="violet" subtitle="Awaiting delivery" />
            <x-stat-card title="Completed Today" value="{{ $completedToday }}" icon="cash" color="emerald" subtitle="{{ $totalPrepared }} total" />
        </div>

        <!-- Quick action -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-6 text-white shadow-lg shadow-amber-500/20">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Kitchen View</h2>
                    <p class="text-amber-50 text-sm mt-1">View and accept incoming orders, then start cooking.</p>
                </div>
                <a href="{{ route('cooker.kitchen') }}" class="inline-flex items-center gap-2 bg-white text-amber-600 px-5 py-2.5 rounded-lg font-semibold hover:bg-amber-50 transition-colors">
                    Go to Kitchen
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                </a>
            </div>
        </div>

        <!-- Incoming orders -->
        @if ($incomingOrders->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Incoming Orders ({{ $incomingOrders->count() }})</h3>
                <p class="text-sm text-slate-500 mt-1">Accept to start preparing</p>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($incomingOrders as $order)
                    <div class="p-4 flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-900">{{ $order->order_number }}</p>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">New</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Waiter: {{ $order->waiter?->name }} • {{ $order->items->count() }} items
                            </p>
                        </div>
                        <form action="{{ route('cooker.orders.accept', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-amber-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-amber-600">Accept</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
