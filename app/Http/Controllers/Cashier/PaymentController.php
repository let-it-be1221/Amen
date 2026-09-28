<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private OrderWorkflowService $workflow,
    ) {}

    /**
     * Display orders awaiting payment (delivered, not yet paid).
     */
    public function index(Request $request): View
    {
        $query = Order::where('status', Order::STATUS_DELIVERED)
            ->whereDoesntHave('payments', function ($q) {
                $q->where('status', Payment::STATUS_COMPLETED);
            })
            ->with(['items.menuItem', 'waiter', 'table']);

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $pendingPayments = $query->latest()->paginate(15)->withQueryString();

        // Recent transactions
        $recentPayments = Payment::with(['order.waiter', 'order.items', 'cashier'])
            ->latest()
            ->limit(10)
            ->get();

        $user = $request->user();

        return view('cashier.payments.index', [
            'pendingPayments' => $pendingPayments,
            'recentPayments' => $recentPayments,
            'todayRevenue' => (float) Payment::where('cashier_id', $user->id)
                ->whereDate('created_at', today())
                ->where('status', Payment::STATUS_COMPLETED)
                ->sum('total'),
            'todayTransactions' => Payment::where('cashier_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
        ]);
    }

    /**
     * Show the payment processing form.
     */
    public function create(Request $request, Order $order): View
    {
        if (!in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_PAID], true)) {
            abort(403, 'Only delivered orders can be paid.');
        }

        $order->load(['items.menuItem', 'waiter', 'table', 'cooker']);

        // Items eligible for payment: only those that were cooked and delivered
        $eligibleItems = $order->items;

        $subtotal = (float) $eligibleItems->sum('subtotal');

        // Default tax & service charge (admin can change in settings)
        $taxRate = (float) (\App\Models\SystemSetting::get('tax_rate', 0));
        $serviceRate = (float) (\App\Models\SystemSetting::get('service_charge_rate', 0));

        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $serviceCharge = round($subtotal * ($serviceRate / 100), 2);

        return view('cashier.payments.create', [
            'order' => $order,
            'items' => $eligibleItems,
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'serviceCharge' => $serviceCharge,
            'taxRate' => $taxRate,
            'serviceRate' => $serviceRate,
            'paymentMethods' => Payment::METHODS,
        ]);
    }

    /**
     * Process a payment.
     */
    public function store(Request $request, Order $order)
    {
        if (!in_array($order->status, [Order::STATUS_DELIVERED], true)) {
            return back()->withErrors(['error' => 'Only delivered orders can be paid.']);
        }

        $validated = $request->validate([
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'service_charge' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:' . implode(',', array_keys(Payment::METHODS))],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['amount_paid'] < $validated['total']) {
            return back()
                ->withInput()
                ->withErrors(['amount_paid' => 'Amount paid cannot be less than the total amount due.']);
        }

        $change = round($validated['amount_paid'] - $validated['total'], 2);

        $user = $request->user();

        try {
            DB::transaction(function () use ($order, $user, $validated, $change) {
                // Assign cashier
                $this->workflow->assignCashier($order, $user);

                // Create payment record
                Payment::create([
                    'order_id' => $order->id,
                    'cashier_id' => $user->id,
                    'subtotal' => $validated['subtotal'],
                    'tax' => $validated['tax'],
                    'service_charge' => $validated['service_charge'],
                    'discount' => $validated['discount'] ?? 0,
                    'total' => $validated['total'],
                    'amount_paid' => $validated['amount_paid'],
                    'change' => $change,
                    'payment_method' => $validated['payment_method'],
                    'status' => Payment::STATUS_COMPLETED,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Transition order to paid
                $this->workflow->transition($order, Order::STATUS_PAID, $user, 'Payment processed');

                // Update order items to served
                \App\Models\OrderItem::where('order_id', $order->id)
                    ->update(['status' => \App\Models\OrderItem::STATUS_SERVED]);
            });

            return redirect()
                ->route('cashier.payments.index')
                ->with('success', "Payment processed for order {$order->order_number}.");
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show payment receipt.
     */
    public function receipt(Request $request, Payment $payment): View
    {
        $payment->load(['order.items.menuItem', 'order.waiter', 'cashier']);

        return view('cashier.payments.receipt', compact('payment'));
    }
}
