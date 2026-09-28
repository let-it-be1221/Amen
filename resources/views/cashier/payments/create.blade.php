<x-app-layout>
    <x-slot name="title">Process Payment</x-slot>
    <x-slot name="page-title">Process Payment</x-slot>
    <x-slot name="page-subtitle">{{ $order->order_number }}</x-slot>

    <div class="space-y-6">
        <a href="{{ route('cashier.payments.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back to payments
        </a>

        <form action="{{ route('cashier.payments.store', $order) }}" method="POST" id="payment-form">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order items -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="p-6 border-b border-slate-100">
                            <h3 class="text-base font-semibold text-slate-900">Order Items (Delivered Only)</h3>
                            <p class="text-xs text-slate-500 mt-1">Only items that were cooked and delivered are included in the payable amount.</p>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ($items as $item)
                                <div class="p-4 flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-900">{{ $item->menu_item_name }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">${{ number_format($item->menu_item_price, 2) }} × {{ $item->quantity }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900">${{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-between">
                            <span class="text-sm font-medium text-slate-700">Subtotal</span>
                            <span class="text-sm font-bold text-slate-900" id="subtotal-display">${{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Order info -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <h3 class="text-sm font-semibold text-slate-900 mb-3">Order Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-slate-500">Waiter</p>
                                <p class="text-slate-900 font-medium">{{ $order->waiter?->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Cooker</p>
                                <p class="text-slate-900 font-medium">{{ $order->cooker?->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Customer</p>
                                <p class="text-slate-900 font-medium">{{ $order->customer_name ?? 'Walk-in' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Table</p>
                                <p class="text-slate-900 font-medium">{{ $order->table?->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Delivered at</p>
                                <p class="text-slate-900 font-medium">{{ $order->delivered_at?->format('M j, Y g:i A') ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment form -->
                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-slate-200 p-6 sticky top-20">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">Payment Details</h3>

                        <!-- Hidden base fields -->
                        <input type="hidden" name="subtotal" value="{{ $subtotal }}">
                        <input type="hidden" name="tax" id="tax-input" value="{{ $taxAmount }}">
                        <input type="hidden" name="service_charge" id="service-input" value="{{ $serviceCharge }}">

                        <!-- Adjustable fields -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Discount ($)</label>
                                <input type="number" name="discount" id="discount-input" step="0.01" min="0" value="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Subtotal</span>
                                    <span class="font-medium text-slate-900" id="subtotal-line">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Tax ({{ $taxRate }}%)</span>
                                    <span class="font-medium text-slate-900" id="tax-line">${{ number_format($taxAmount, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Service ({{ $serviceRate }}%)</span>
                                    <span class="font-medium text-slate-900" id="service-line">${{ number_format($serviceCharge, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Discount</span>
                                    <span class="font-medium text-rose-600" id="discount-line">-$0.00</span>
                                </div>
                                <div class="pt-2 border-t border-slate-200 flex justify-between">
                                    <span class="font-semibold text-slate-900">Total Due</span>
                                    <span class="font-bold text-lg text-slate-900" id="total-display">${{ number_format($subtotal + $taxAmount + $serviceCharge, 2) }}</span>
                                    <input type="hidden" name="total" id="total-input" value="{{ $subtotal + $taxAmount + $serviceCharge }}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Amount Paid ($)</label>
                                <input type="number" name="amount_paid" id="amount-paid-input" step="0.01" min="0" value="{{ number_format($subtotal + $taxAmount + $serviceCharge, 2, '.', '') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 flex justify-between text-sm">
                                <span class="text-emerald-700">Change</span>
                                <span class="font-bold text-emerald-900" id="change-display">$0.00</span>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Payment Method</label>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach ($paymentMethods as $key => $label)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="payment_method" value="{{ $key }}" class="peer sr-only" @if ($key === 'cash') checked @endif required>
                                            <div class="p-2 rounded-lg border border-slate-200 text-center text-xs peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-colors">
                                                {{ $label }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Notes (optional)</label>
                                <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-emerald-500 text-white py-2.5 rounded-lg font-semibold hover:bg-emerald-600 transition-colors">
                                Confirm Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const subtotal = {{ $subtotal }};
        const taxAmount = {{ $taxAmount }};
        const serviceCharge = {{ $serviceCharge }};

        const discountInput = document.getElementById('discount-input');
        const amountPaidInput = document.getElementById('amount-paid-input');
        const totalInput = document.getElementById('total-input');
        const totalDisplay = document.getElementById('total-display');
        const discountLine = document.getElementById('discount-line');
        const changeDisplay = document.getElementById('change-display');

        function recalc() {
            const discount = parseFloat(discountInput.value) || 0;
            const total = Math.max(0, subtotal + taxAmount + serviceCharge - discount);
            const paid = parseFloat(amountPaidInput.value) || 0;
            const change = paid - total;

            totalInput.value = total.toFixed(2);
            totalDisplay.textContent = '$' + total.toFixed(2);
            discountLine.textContent = '-$' + discount.toFixed(2);
            changeDisplay.textContent = '$' + (change >= 0 ? change.toFixed(2) : '0.00');
            changeDisplay.classList.toggle('text-rose-600', change < 0);
            changeDisplay.classList.toggle('text-emerald-900', change >= 0);
        }

        discountInput.addEventListener('input', recalc);
        amountPaidInput.addEventListener('input', recalc);
        recalc();
    </script>
    @endpush
</x-app-layout>
