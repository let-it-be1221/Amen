<?php

namespace App\Http\Controllers\Cooker;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function __construct(
        private OrderWorkflowService $workflow,
    ) {}

    /**
     * Display the kitchen view - showing incoming orders (status: ordered)
     * and currently cooking orders (assigned to this cooker).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Incoming orders not yet claimed by any cooker
        $incomingOrders = Order::where('status', Order::STATUS_ORDERED)
            ->whereNull('cooker_id')
            ->with(['items.menuItem', 'waiter', 'table'])
            ->latest()
            ->get();

        // Orders being cooked by this cooker
        $myActiveOrders = Order::where('cooker_id', $user->id)
            ->whereIn('status', [Order::STATUS_ACCEPTED, Order::STATUS_COOKING])
            ->with(['items.menuItem', 'waiter', 'table'])
            ->latest('cooking_started_at')
            ->get();

        // Ready orders waiting for delivery
        $readyOrders = Order::where('cooker_id', $user->id)
            ->where('status', Order::STATUS_READY)
            ->with(['items.menuItem', 'waiter', 'table'])
            ->latest('ready_at')
            ->get();

        // History of orders prepared by this cooker
        $historyOrders = Order::where('cooker_id', $user->id)
            ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_PAID])
            ->with(['items', 'waiter'])
            ->latest()
            ->paginate(15);

        $completedToday = Order::where('cooker_id', $user->id)
            ->whereIn('status', [Order::STATUS_READY, Order::STATUS_DELIVERED, Order::STATUS_PAID])
            ->whereDate('updated_at', today())
            ->count();

        $totalPrepared = Order::where('cooker_id', $user->id)
            ->whereIn('status', [Order::STATUS_READY, Order::STATUS_DELIVERED, Order::STATUS_PAID])
            ->count();

        return view('cooker.kitchen', compact(
            'incomingOrders',
            'myActiveOrders',
            'readyOrders',
            'historyOrders',
            'completedToday',
            'totalPrepared',
        ));
    }

    /**
     * Display a specific order's details.
     */
    public function show(Request $request, Order $order): View
    {
        $order->load(['items.menuItem', 'waiter', 'table', 'statusHistory.user']);

        return view('cooker.show', compact('order'));
    }

    /**
     * Accept an incoming order (assigns the cooker to it).
     */
    public function accept(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_ORDERED) {
            return back()->withErrors(['error' => 'This order can no longer be accepted.']);
        }

        $user = $request->user();

        try {
            // Assign cooker
            $this->workflow->assignCooker($order, $user);
            // Transition to accepted
            $this->workflow->transition($order, Order::STATUS_ACCEPTED, $user, "Order accepted by {$user->name}");

            return redirect()
                ->route('cooker.kitchen')
                ->with('success', "Order {$order->order_number} accepted.");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Start cooking an order.
     */
    public function startCooking(Request $request, Order $order)
    {
        if (!in_array($order->status, [Order::STATUS_ACCEPTED], true)) {
            return back()->withErrors(['error' => 'Order must be in "Accepted" state to start cooking.']);
        }

        if ($order->cooker_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return back()->withErrors(['error' => 'Only the assigned cooker can start cooking this order.']);
        }

        try {
            $this->workflow->transition($order, Order::STATUS_COOKING, $request->user(), 'Cooking started');

            // Update order items status
            OrderItem::where('order_id', $order->id)->update(['status' => OrderItem::STATUS_COOKING]);

            return redirect()
                ->route('cooker.kitchen')
                ->with('success', "Cooking started for order {$order->order_number}.");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Mark an order as ready (cooking completed).
     * Sends a database notification to the waiter so they know to pick it up.
     */
    public function markReady(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_COOKING) {
            return back()->withErrors(['error' => 'Order must be in "Cooking" state to mark as ready.']);
        }

        if ($order->cooker_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return back()->withErrors(['error' => 'Only the assigned cooker can mark this order as ready.']);
        }

        try {
            $this->workflow->transition($order, Order::STATUS_READY, $request->user(), 'Order is ready for delivery');

            // Update order items status
            OrderItem::where('order_id', $order->id)->update(['status' => OrderItem::STATUS_READY]);

            // Eager load items so the notification data includes the count
            $order->load('items', 'table', 'cooker');

            // Notify the waiter who took the order that the food is ready to serve
            if ($order->waiter) {
                $order->waiter->notify(new \App\Notifications\OrderReady($order));
            }

            return redirect()
                ->route('cooker.kitchen')
                ->with('success', "Order {$order->order_number} marked as ready. The waiter has been notified.");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
