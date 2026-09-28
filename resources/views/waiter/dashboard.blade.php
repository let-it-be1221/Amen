<x-app-layout>
    <x-slot name="title">Waiter Dashboard</x-slot>
    <x-slot name="page-title">My Dashboard</x-slot>
    <x-slot name="page-subtitle">Manage your orders</x-slot>

    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Active Orders" value="{{ $activeOrders }}" icon="fire" color="amber" />
            <x-stat-card title="Orders Today" value="{{ $totalOrdersToday }}" icon="list" color="blue" />
            <x-stat-card title="Total Revenue" value="${{ number_format($totalRevenue, 2) }}" icon="cash" color="emerald" subtitle="From your paid orders" />
            <x-stat-card title="Available Items" value="{{ $menuItems->count() }}" icon="menu" color="violet" />
        </div>

        <!-- QR Code for customer menu -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" x-data="qrCodeCard()" x-init="init()">
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-4 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold">Customer Menu QR Code</p>
                        <p class="text-xs text-slate-300">Show this to your customer — they scan &amp; view the live menu.</p>
                    </div>
                </div>
                <span class="text-xs text-slate-400 hidden sm:inline" :class="{ 'text-amber-400 animate-pulse': updating }" x-text="updating ? 'Refreshing...' : 'Menu v' + versionShort"></span>
            </div>
            <div class="p-6 flex flex-col lg:flex-row items-center gap-6">
                <div class="flex-shrink-0">
                    <div class="relative w-48 h-48 bg-white border-4 border-slate-100 rounded-xl p-2 shadow-inner">
                        <img :src="qrUrl" :alt="'Menu QR Code v' + versionShort" class="w-full h-full" x-show="qrUrl" />
                        <div x-show="!qrUrl" class="w-full h-full flex items-center justify-center text-slate-400">
                            <svg class="w-10 h-10 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="60" stroke-dashoffset="20"></circle></svg>
                        </div>
                        <div x-show="updating" x-cloak class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <div class="text-xs text-slate-500 flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="60" stroke-dashoffset="20"></circle></svg>
                                Updating...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 min-w-0 text-center lg:text-left">
                    <h3 class="text-base font-semibold text-slate-900">How to use</h3>
                    <ol class="mt-2 text-sm text-slate-600 space-y-1.5 list-decimal list-inside">
                        <li>Show this QR code to your customer.</li>
                        <li>They scan it with their phone camera.</li>
                        <li>The full menu opens in their browser — no app needed.</li>
                        <li>They tell you what they want, and you place the order.</li>
                    </ol>
                    <p class="mt-3 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-2.5">
                        <strong>Auto-refreshes:</strong> If the supervisor adds or removes a menu item, this QR code automatically regenerates — the customer always sees the latest menu.
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2 justify-center lg:justify-start">
                        <a :href="menuUrl" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                            Preview menu
                        </a>
                        <button @click="copyUrl()" class="inline-flex items-center gap-1.5 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125V8.625M16.5 4.5v6m0-6H9.75" /></svg>
                            <span x-text="copied ? '✓ Copied' : 'Copy link'"></span>
                        </button>
                        <button @click="downloadQr()" class="inline-flex items-center gap-1.5 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                            Download QR
                        </button>
                        <button @click="printQr()" class="inline-flex items-center gap-1.5 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.839c.19.093.373.197.55.312l5.04 3.415a.75.75 0 0 0 .84-.001l5.027-3.39a3.256 3.256 0 0 1 .564-.328m-12.02 0c.41-.197.873-.31 1.362-.31.488 0 .952.113 1.361.31m7.297-.31c.41.197.873.31 1.362.31.488 0 .952-.113 1.361-.31M3 16.5V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v9M3 16.5h18" /></svg>
                            Print QR
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function qrCodeCard() {
            return {
                qrUrl: '{{ $qrUrl }}',
                menuUrl: '{{ $menuUrl }}',
                versionShort: '{{ $menuVersionShort }}',
                updating: false,
                copied: false,
                pollInterval: null,

                init() {
                    // Poll every 30 seconds for menu version changes
                    this.pollInterval = setInterval(() => this.checkForUpdate(), 30000);
                },

                async checkForUpdate() {
                    try {
                        const resp = await fetch('{{ route("menu.qrcode") }}', {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        });
                        if (!resp.ok) return;
                        const data = await resp.json();
                        if (data.version_short !== this.versionShort) {
                            // Menu has been updated - refresh QR code
                            this.updating = true;
                            this.versionShort = data.version_short;
                            this.menuUrl = data.menu_url;
                            // Add cache-buster so the browser reloads the QR image
                            this.qrUrl = data.qr_url + '&t=' + Date.now();
                            setTimeout(() => this.updating = false, 800);
                        }
                    } catch (e) {}
                },

                async copyUrl() {
                    try {
                        await navigator.clipboard.writeText(this.menuUrl);
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    } catch (e) {
                        // Fallback
                        const input = document.createElement('textarea');
                        input.value = this.menuUrl;
                        document.body.appendChild(input);
                        input.select();
                        document.execCommand('copy');
                        document.body.removeChild(input);
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }
                },

                downloadQr() {
                    const a = document.createElement('a');
                    a.href = this.qrUrl;
                    a.download = 'amen-menu-qr-v' + this.versionShort + '.png';
                    a.target = '_blank';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                },

                printQr() {
                    const w = window.open('', '_blank', 'width=600,height=800');
                    if (!w) return;
                    w.document.write(`
                        <html>
                        <head><title>Amen Menu QR Code</title>
                        <style>
                            body { font-family: sans-serif; text-align: center; padding: 40px; }
                            img { width: 400px; height: 400px; }
                            h1 { margin-top: 20px; }
                            p { color: #555; }
                        </style></head>
                        <body>
                            <img src="${this.qrUrl}" alt="Menu QR Code" />
                            <h1>Amen Restaurant Menu</h1>
                            <p>Scan with your phone camera to view our full menu.</p>
                            <p>Version: ${this.versionShort}</p>
                            <p style="font-size:11px;color:#999">Generated {{ now()->format('M j, Y g:i A') }}</p>
                        </body>
                        </html>
                    `);
                    w.document.close();
                    w.focus();
                    setTimeout(() => w.print(), 500);
                },
            };
        }
        </script>


        <!-- Quick action -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-6 text-white shadow-lg shadow-amber-500/20">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Ready to take a new order?</h2>
                    <p class="text-amber-50 text-sm mt-1">Tap below to create a new order for your customers.</p>
                </div>
                <a href="{{ route('waiter.orders.create') }}" class="inline-flex items-center gap-2 bg-white text-amber-600 px-5 py-2.5 rounded-lg font-semibold hover:bg-amber-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    New Order
                </a>
            </div>
        </div>

        <!-- Ready for delivery alert -->
        @if ($readyCount > 0)
        <div class="bg-white rounded-xl border-2 border-emerald-300 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-4 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold">{{ $readyCount }} order(s) ready for delivery</p>
                        <p class="text-xs text-emerald-50">The kitchen has finished preparing these. Please serve them to your customers.</p>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($readyForDelivery as $order)
                    <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-slate-900">{{ $order->order_number }}</p>
                                <span class="text-xs bg-violet-100 text-violet-800 px-2 py-0.5 rounded-full">Ready</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $order->items->count() }} item(s) • {{ $order->table?->name ?? 'No table' }}
                                @if ($order->ready_at) • Ready since {{ $order->ready_at->diffForHumans() }}@endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('waiter.orders.show', $order) }}" class="text-amber-600 hover:text-amber-700 text-xs font-medium px-3 py-1.5 border border-amber-300 rounded-lg hover:bg-amber-50">View</a>
                            <form action="{{ route('waiter.orders.deliver', $order) }}" method="POST" onsubmit="return confirm('Confirm that order {{ $order->order_number }} has been delivered to the customer?')">
                                @csrf
                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg">
                                    ✓ Mark Delivered
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent orders -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">My Recent Orders</h3>
                <a href="{{ route('waiter.orders.index') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($myOrders as $order)
                    <a href="{{ route('waiter.orders.show', $order) }}" class="block p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-slate-900">{{ $order->order_number }}</p>
                                    <x-status-badge :status="$order->status" />
                                </div>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $order->items->count() }} item(s) • {{ $order->created_at->diffForHumans() }}
                                    @if ($order->table) • {{ $order->table->name }}@endif
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-semibold text-slate-900">${{ number_format($order->totalAmount(), 2) }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" /></svg>
                        <p class="mt-2 text-sm text-slate-500">No orders yet</p>
                        <a href="{{ route('waiter.orders.create') }}" class="mt-3 inline-flex items-center gap-1 text-sm text-amber-600 hover:text-amber-700 font-medium">Create your first order →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
