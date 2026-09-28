@props(['status' => ''])

@php
    $statusColors = [
        'ordered' => 'bg-blue-100 text-blue-800 border-blue-200',
        'accepted' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
        'cooking' => 'bg-amber-100 text-amber-800 border-amber-200',
        'ready' => 'bg-violet-100 text-violet-800 border-violet-200',
        'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'paid' => 'bg-green-100 text-green-800 border-green-200',
        'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
    ];
    $statusLabels = [
        'ordered' => 'Ordered',
        'accepted' => 'Accepted',
        'cooking' => 'Cooking',
        'ready' => 'Ready',
        'delivered' => 'Delivered',
        'paid' => 'Paid',
        'cancelled' => 'Cancelled',
    ];
    $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    $label = $statusLabels[$status] ?? ucfirst($status);
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
    {{ $label }}
</span>
