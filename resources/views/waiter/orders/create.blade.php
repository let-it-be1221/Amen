<x-app-layout>
    <x-slot name="title">New Order - Waiter</x-slot>
    <x-slot name="page-title">New Order</x-slot>
    <x-slot name="page-subtitle">Select items and submit to the kitchen</x-slot>

    <form action="{{ route('waiter.orders.store') }}" method="POST" id="order-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Menu selection (left/center, 2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Order type selector -->
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-medium text-slate-700 mb-3">Order Type</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach (\App\Models\Order::ORDER_TYPES as $key => $label)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="order_type" value="{{ $key }}" class="peer sr-only" @if ($key === 'dine_in') checked @endif required>
                                <div class="p-3 rounded-lg border border-slate-200 text-center text-sm peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 transition-colors hover:border-slate-300">
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Category tabs -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-200 overflow-x-auto">
                        <div class="flex gap-1 p-2 min-w-max">
                            <button type="button" onclick="filterCategory('all')" id="cat-all" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-500 text-white">All</button>
                            @foreach ($categories as $category)
                                <button type="button" onclick="filterCategory('{{ $category->id }}')" id="cat-{{ $category->id }}" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 hover:bg-slate-200">{{ $category->name }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Menu items grid -->
                    <div class="p-4 max-h-[600px] overflow-y-auto">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach ($categories as $category)
                                @foreach ($category->menuItems as $item)
                                    <div class="menu-item-card border border-slate-200 rounded-lg p-3 hover:border-amber-400 hover:shadow-md transition-all cursor-pointer" data-category="{{ $category->id }}" onclick="addItem({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})">
                                        <div class="aspect-square bg-gradient-to-br from-amber-100 to-orange-100 rounded-md mb-2 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-2.25-1.313M21 7.5v2.25m0-2.25-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3 2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75 2.25-1.313M12 21.75V19.5m0 2.25-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m0-16.875L21 7.5m-9 6.75 2.25-1.313M12 12.75V15m0 0 2.25 1.313M3.75 9 12 4.5l8.25 4.5M3 9v6l9 4.5 9-4.5V9" /></svg>
                                        </div>
                                        <p class="text-xs font-medium text-slate-900 line-clamp-2">{{ $item->name }}</p>
                                        <p class="text-xs text-amber-600 font-semibold mt-1">${{ number_format($item->price, 2) }}</p>
                                        @if ($item->is_vegetarian)
                                            <span class="inline-block mt-1 text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">Veg</span>
                                        @endif
                                        @if ($item->is_spicy)
                                            <span class="inline-block mt-1 text-[10px] px-1.5 py-0.5 rounded bg-rose-100 text-rose-700">Spicy</span>
                                        @endif
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart / Order summary (right, 1 col) -->
            <div class="space-y-4">
                <!-- Customer info -->
                <div class="bg-white rounded-xl border border-slate-200 p-4 space-y-3">
                    <p class="text-sm font-medium text-slate-700">Customer & Table</p>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Table</label>
                        <select name="table_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                            <option value="">— Select table —</option>
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}">{{ $table->name }} ({{ $table->seats }} seats)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Customer name (optional)</label>
                        <input type="text" name="customer_name" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Walk-in customer">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Phone</label>
                            <input type="text" name="customer_phone" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="—">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Guests</label>
                            <input type="number" name="customer_count" min="1" max="50" value="1" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Allergies, special requests..."></textarea>
                    </div>
                </div>

                <!-- Cart -->
                <div class="bg-white rounded-xl border border-slate-200 p-4 sticky top-20">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-slate-700">Cart</p>
                        <span id="cart-count" class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">0 items</span>
                    </div>

                    <div id="cart-items" class="space-y-2 max-h-[300px] overflow-y-auto mb-3">
                        <p id="empty-cart" class="text-center text-xs text-slate-400 py-6">Click menu items to add them to your order</p>
                    </div>

                    <div class="border-t border-slate-200 pt-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Subtotal</span>
                            <span id="cart-total" class="font-semibold text-slate-900">$0.00</span>
                        </div>
                    </div>

                    <button type="submit" id="submit-btn" class="w-full mt-4 bg-amber-500 text-white py-2.5 rounded-lg font-semibold hover:bg-amber-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Submit Order to Kitchen
                    </button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        let cart = [];
        const itemsByCategory = {};

        function filterCategory(categoryId) {
            document.querySelectorAll('.menu-item-card').forEach(card => {
                if (categoryId === 'all' || card.dataset.category === categoryId) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
            document.querySelectorAll('[id^="cat-"]').forEach(btn => {
                btn.classList.remove('bg-amber-500', 'text-white');
                btn.classList.add('bg-slate-100', 'text-slate-700');
            });
            const activeBtn = document.getElementById('cat-' + categoryId);
            activeBtn.classList.add('bg-amber-500', 'text-white');
            activeBtn.classList.remove('bg-slate-100', 'text-slate-700');
        }

        function addItem(id, name, price) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({ id, name, price, quantity: 1 });
            }
            renderCart();
        }

        function removeItem(id) {
            cart = cart.filter(item => item.id !== id);
            renderCart();
        }

        function updateQuantity(id, delta) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity < 1) {
                    removeItem(id);
                    return;
                }
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const totalEl = document.getElementById('cart-total');
            const countEl = document.getElementById('cart-count');
            const submitBtn = document.getElementById('submit-btn');

            if (cart.length === 0) {
                container.innerHTML = '<p id="empty-cart" class="text-center text-xs text-slate-400 py-6">Click menu items to add them to your order</p>';
                totalEl.textContent = '$0.00';
                countEl.textContent = '0 items';
                submitBtn.disabled = true;
                // Remove all hidden inputs
                document.querySelectorAll('.cart-input').forEach(el => el.remove());
                return;
            }

            // Clear hidden inputs
            document.querySelectorAll('.cart-input').forEach(el => el.remove());

            let html = '';
            let total = 0;
            let totalQty = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                totalQty += item.quantity;

                html += `
                    <div class="bg-slate-50 rounded-lg p-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-xs font-medium text-slate-900 flex-1">${item.name}</p>
                            <button type="button" onclick="removeItem(${item.id})" class="text-rose-500 hover:text-rose-700">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="updateQuantity(${item.id}, -1)" class="w-6 h-6 rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100">-</button>
                                <span class="text-xs font-medium w-6 text-center">${item.quantity}</span>
                                <button type="button" onclick="updateQuantity(${item.id}, 1)" class="w-6 h-6 rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100">+</button>
                            </div>
                            <p class="text-xs font-semibold text-slate-900">$${itemTotal.toFixed(2)}</p>
                        </div>
                    </div>
                `;

                // Add hidden inputs
                const form = document.getElementById('order-form');
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = `items[${index}][menu_item_id]`;
                idInput.value = item.id;
                idInput.classList.add('cart-input');
                form.appendChild(idInput);

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `items[${index}][quantity]`;
                qtyInput.value = item.quantity;
                qtyInput.classList.add('cart-input');
                form.appendChild(qtyInput);
            });

            container.innerHTML = html;
            totalEl.textContent = '$' + total.toFixed(2);
            countEl.textContent = totalQty + (totalQty === 1 ? ' item' : ' items');
            submitBtn.disabled = false;
        }
    </script>
    @endpush
</x-app-layout>
