@props(['title' => '', 'value' => '', 'icon' => 'dashboard', 'color' => 'slate', 'subtitle' => ''])

@php
    $colors = [
        'slate' => 'bg-slate-100 text-slate-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'rose' => 'bg-rose-100 text-rose-700',
        'cyan' => 'bg-cyan-100 text-cyan-700',
        'violet' => 'bg-violet-100 text-violet-700',
        'blue' => 'bg-blue-100 text-blue-700',
        'orange' => 'bg-orange-100 text-orange-700',
    ];
    $iconColor = $colors[$color] ?? $colors['slate'];
@endphp

<div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div class="min-w-0 flex-1">
            <p class="text-sm text-slate-500 font-medium">{{ $title }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-1 truncate">{{ $value }}</p>
            @if ($subtitle)
                <p class="text-xs text-slate-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        @if (!empty($icon))
            <div class="w-10 h-10 rounded-lg {{ $iconColor }} flex items-center justify-center flex-shrink-0">
                @include('partials.nav-icons', ['icon' => $icon])
            </div>
        @endif
    </div>
    {{ $slot ?? '' }}
</div>
