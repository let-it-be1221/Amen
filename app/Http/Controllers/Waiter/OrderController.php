<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private OrderWorkflowService $workflow,
    ) {}

    /**
     * Display the new order creation page (POS-like interface).
     */
    public function create(Request $request): View
    {
        $categories = Category::where('is_active', true)
            ->with(['menuItems' => function ($q) {
                $q->where('is_available', true)->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tables = RestaurantTable::where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedCategoryId = $request->integer('category');
        if ($selectedCategoryId) {
            $categories->load(['menuItems']);
        }

        return view('waiter.orders.create', compact('categories', 'tables'));
    }

    /**
     * Store a new order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => ['nullable', 'exists:tables,id'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_count' => ['nullable', 'integer', 'min:1', 'max:50'],
            'order_type' => ['required', 'in:dine_in,takeaway,delivery'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.notes' => ['nullable', 'string', 'max:200'],
        ]);

        $user = $request->user();

        $order = DB::transaction(function () use ($validated, $user) {
            // Lock the menu items to get accurate pricing snapshot
            $menuItemIds = collect($validated['items'])->pluck('menu_item_id')->unique();
            $menuItems = MenuItem::whereIn('id', $menuItemIds)->get()->keyBy('id');

            $order = Order::create([
                'waiter_id' => $user->id,
                'table_id' => $validated['table_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_count' => $validated['customer_count'] ?? 1,
                'order_type' => $validated['order_type'],
                'notes' => $validated['notes'] ?? null,
                'status' => Order::STATUS_ORDERED,
            ]);

            foreach ($validated['items'] as $itemData) {
                $menuItem = $menuItems->get($itemData['menu_item_id']);
                if (!$menuItem || !$menuItem->is_available) {
                    throw new \InvalidArgumentException("Menu item not available: {$itemData['menu_item_id']}");
                }

                $quantity = (int) $itemData['quantity'];
                $subtotal = $menuItem->price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'menu_item_name' => $menuItem->name,
                    'menu_item_price' => $menuItem->price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'notes' => $itemData['notes'] ?? null,
                    'status' => OrderItem::STATUS_PENDING,
                ]);
            }

            // Record initial status in history
            \App\Models\OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'status' => Order::STATUS_ORDERED,
                'notes' => 'Order created by waiter',
            ]);

            return $order;
        });

        return redirect()
            ->route('waiter.orders.show', $order)
            ->with('success', "Order {$order->order_number} created successfully and submitted to the kitchen.");
    }

    /**
     * Display the waiter's orders list.
     */
    public function index(Request $request): View
    {
        $query = Order::where('waiter_id', $request->user()->id)
            ->with(['items.menuItem', 'table', 'cooker']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('waiter.orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Display a specific order.
     */
    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $order->load([
            'items.menuItem.category',
            'table',
            'cooker',
            'cashier',
            'statusHistory.user',
            'payments.cashier',
        ]);

        return view('waiter.orders.show', compact('order'));
    }

    /**
     * Cancel an order (only allowed when status is "ordered" or "accepted").
     */
    public function cancel(Request $request, Order $order)
    {
        $this->authorize('cancel', $order);

        $validated = $request->validate([
            'cancel_reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            $this->workflow->transition(
                $order,
                Order::STATUS_CANCELLED,
                $request->user(),
                'Cancelled by waiter: ' . $validated['cancel_reason']
            );

            return redirect()
                ->route('waiter.orders.index')
                ->with('success', "Order {$order->order_number} cancelled.");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
