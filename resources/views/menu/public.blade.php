<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $restaurantName }} - Menu</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Figtree', system-ui, -apple-system, sans-serif; }
        .hero-bg {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.75)),
                        url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80') center/cover;
        }
        .menu-card:hover .menu-img { transform: scale(1.05); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <!-- Hero header -->
    <header class="hero-bg text-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-slate-900 font-bold text-2xl shadow-lg">A</div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold">{{ $restaurantName }}</h1>
                            <p class="text-amber-300 text-sm">Fresh &amp; delicious</p>
                        </div>
                    </div>
                    @if ($restaurantAddress)
                        <p class="text-slate-300 text-sm flex items-center gap-2 mt-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            {{ $restaurantAddress }}
                        </p>
                    @endif
                    @if ($restaurantPhone)
                        <p class="text-slate-300 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                            {{ $restaurantPhone }}
                        </p>
                    @endif
                </div>
                <div class="text-right text-xs text-slate-300">
                    <p>Menu updated</p>
                    <p class="font-semibold">{{ now()->format('M j, Y - g:i A') }}</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Categories navigation -->
    @if ($categories->isNotEmpty())
    <nav class="sticky top-0 z-10 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-2 overflow-x-auto py-3">
                @foreach ($categories as $category)
                    <a href="#cat-{{ $category->id }}" class="flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 hover:bg-amber-500 hover:text-white transition-colors">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>
    @endif

    <!-- Menu items by category -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">
        @forelse ($categories as $category)
            <section id="cat-{{ $category->id }}">
                <div class="flex items-baseline justify-between mb-4 border-b border-slate-200 pb-2">
                    <h2 class="text-2xl font-bold text-slate-900">{{ $category->name }}</h2>
                    @if ($category->description)
                        <p class="text-sm text-slate-500 hidden sm:block max-w-md text-right">{{ $category->description }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($category->menuItems as $item)
                        <div class="menu-card bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-all">
                            <div class="flex">
                                @if ($item->image)
                                    <div class="w-28 h-28 sm:w-32 sm:h-32 flex-shrink-0 overflow-hidden bg-slate-100">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->image) }}" alt="{{ $item->name }}" class="menu-img w-full h-full object-cover transition-transform">
                                    </div>
                                @else
                                    <div class="w-28 h-28 sm:w-32 sm:h-32 flex-shrink-0 bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-2.25-1.313M21 7.5v2.25m0-2.25-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3 2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75 2.25-1.313M12 21.75V19.5m0 2.25-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m0-16.875L21 7.5m-9 6.75 2.25-1.313M12 12.75V15m0 0 2.25 1.313M3.75 9 12 4.5l8.25 4.5M3 9v6l9 4.5 9-4.5V9" /></svg>
                                    </div>
                                @endif
                                <div class="flex-1 p-4 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <h3 class="text-base font-semibold text-slate-900 leading-tight">{{ $item->name }}</h3>
                                        <p class="text-lg font-bold text-amber-600 whitespace-nowrap">{{ $currencySymbol }}{{ number_format($item->price, 2) }}</p>
                                    </div>
                                    @if ($item->description)
                                        <p class="text-xs text-slate-500 line-clamp-2 mb-2">{{ $item->description }}</p>
                                    @endif
                                    <div class="flex flex-wrap gap-1 text-[10px]">
                                        @if ($item->is_vegetarian)
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 font-medium">🌱 Vegetarian</span>
                                        @endif
                                        @if ($item->is_spicy)
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 font-medium">🌶 Spicy</span>
                                        @endif
                                        @if ($item->calories)
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $item->calories }}</span>
                                        @endif
                                        @if ($item->preparation_time)
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-blue-50 text-blue-600">⏱ {{ $item->preparation_time }} min</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" /></svg>
                <h2 class="mt-4 text-lg font-semibold text-slate-700">Menu is being updated</h2>
                <p class="mt-1 text-sm text-slate-500">Please check back in a moment. Our team is preparing the menu.</p>
            </div>
        @endforelse
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 mt-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center">
            <p class="text-sm">{{ $restaurantName }} • Thank you for visiting!</p>
            <p class="text-xs text-slate-500 mt-2">Please call a waiter to place your order. Prices are inclusive of taxes unless noted otherwise.</p>
        </div>
    </footer>

    <script>
    // Smooth scroll to categories
    document.querySelectorAll('a[href^="#cat-"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const target = document.querySelector(a.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    </script>
</body>
</html>
