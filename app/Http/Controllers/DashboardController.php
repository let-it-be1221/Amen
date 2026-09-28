<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use App\Models\User;
use App\Models\Payment;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return match ($user->role) {
            User::ROLE_ADMIN => $this->adminDashboard($user),
            User::ROLE_SUPERVISOR => $this->supervisorDashboard($user),
            User::ROLE_WAITER => $this->waiterDashboard($user),
            User::ROLE_COOKER => $this->cookerDashboard($user),
            User::ROLE_CASHIER => $this->cashierDashboard($user),
            default => abort(403, 'Unknown role.'),
        };
    }

    private function adminDashboard(User $user): View
    {
        $today = now()->startOfDay();

        $stats = [
            'totalOrders' => Order::count(),
            'todayOrders' => Order::where('created_at', '>=', $today)->count(),
            'activeOrders' => Order::whereIn('status', Order::ACTIVE_STATUSES)->count(),
            'cancelledOrders' => Order::where('status', Order::STATUS_CANCELLED)->count(),
            'totalRevenue' => (float) Payment::where('status', Payment::STATUS_COMPLETED)->sum('total'),
            'todayRevenue' => (float) Payment::where('status', Payment::STATUS_COMPLETED)
                ->where('created_at', '>=', $today)
                ->sum('total'),
            'totalUsers' => User::count(),
            'totalMenuItems' => MenuItem::count(),
            'availableMenuItems' => MenuItem::where('is_available', true)->count(),
        ];

        $recentOrders = Order::with(['waiter', 'cooker', 'items'])
            ->latest()
            ->limit(8)
            ->get();

        $ordersByStatus = [];
        foreach (Order::STATUSES as $key => $label) {
            $ordersByStatus[$key] = [
                'label' => $label,
                'count' => Order::where('status', $key)->count(),
            ];
        }

        $recentActivities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->limit(15)
            ->get();

        $usersByRole = [];
        foreach (User::ROLES as $role => $label) {
            $usersByRole[$role] = [
                'label' => $label,
                'count' => User::where('role', $role)->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'ordersByStatus',
            'recentActivities',
            'usersByRole'
        ));
    }

    private function supervisorDashboard(User $user): View
    {
        $today = now()->startOfDay();

        $topWaiters = User::where('role', User::ROLE_WAITER)
            ->withCount(['ordersAsWaiter as total_orders' => fn($q) => $q->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_PAID])])
            ->orderByDesc('total_orders')
            ->limit(5)
            ->get()
            ->filter(fn($u) => $u->total_orders > 0)
            ->values()
            ->toArray();

        $topCookers = User::where('role', User::ROLE_COOKER)
            ->withCount(['ordersAsCooker as total_orders' => fn($q) => $q->whereIn('status', [Order::STATUS_READY, Order::STATUS_DELIVERED, Order::STATUS_PAID])])
            ->orderByDesc('total_orders')
            ->limit(5)
            ->get()
            ->filter(fn($u) => $u->total_orders > 0)
            ->values()
            ->toArray();

        $ordersByStatus = collect(Order::STATUSES)
            ->map(fn($label, $key) => ['label' => $label, 'count' => Order::where('status', $key)->count()])
            ->toArray();

        return view('supervisor.dashboard', [
            'totalOrders' => Order::count(),
            'todayOrders' => Order::where('created_at', '>=', $today)->count(),
            'activeOrders' => Order::whereIn('status', Order::ACTIVE_STATUSES)->count(),
            'cancelledOrders' => Order::where('status', Order::STATUS_CANCELLED)->count(),
            'totalRevenue' => (float) Payment::where('status', Payment::STATUS_COMPLETED)->sum('total'),
            'todayRevenue' => (float) Payment::where('status', Payment::STATUS_COMPLETED)->where('created_at', '>=', $today)->sum('total'),
            'recentOrders' => Order::with(['waiter', 'cooker', 'items'])->latest()->limit(10)->get(),
            'ordersByStatus' => $ordersByStatus,
            'topWaiters' => $topWaiters,
            'topCookers' => $topCookers,
        ]);
    }

    private function waiterDashboard(User $user): View
    {
        return view('waiter.dashboard', [
            'myOrders' => Order::where('waiter_id', $user->id)->with(['items', 'table'])->latest()->limit(10)->get(),
            'activeOrders' => Order::where('waiter_id', $user->id)->whereIn('status', Order::ACTIVE_STATUSES)->count(),
            'totalOrdersToday' => Order::where('waiter_id', $user->id)->whereDate('created_at', today())->count(),
            'totalRevenue' => (float) Order::where('waiter_id', $user->id)
                ->where('status', Order::STATUS_PAID)
                ->withSum('items', 'subtotal')
                ->get()
                ->sum('items_sum_subtotal'),
            'menuItems' => MenuItem::where('is_available', true)->with('category')->limit(6)->get(),
        ]);
    }

    private function cookerDashboard(User $user): View
    {
        $incomingOrders = Order::where('status', Order::STATUS_ORDERED)
            ->whereNull('cooker_id')
            ->with(['items.menuItem', 'waiter'])
            ->latest()
            ->get();

        $assignedOrders = Order::where('cooker_id', $user->id)
            ->whereIn('status', [Order::STATUS_ACCEPTED, Order::STATUS_COOKING])
            ->with(['items.menuItem', 'waiter'])
            ->latest()
            ->get();

        $readyOrders = Order::where('cooker_id', $user->id)
            ->where('status', Order::STATUS_READY)
            ->with(['items.menuItem', 'waiter'])
            ->latest('ready_at')
            ->get();

        $completedToday = Order::where('cooker_id', $user->id)
            ->whereIn('status', [Order::STATUS_READY, Order::STATUS_DELIVERED, Order::STATUS_PAID])
            ->whereDate('updated_at', today())
            ->count();

        $totalPrepared = Order::where('cooker_id', $user->id)
            ->whereIn('status', [Order::STATUS_READY, Order::STATUS_DELIVERED, Order::STATUS_PAID])
            ->count();

        return view('cooker.dashboard', compact(
            'incomingOrders',
            'assignedOrders',
            'readyOrders',
            'completedToday',
            'totalPrepared',
        ));
    }

    private function cashierDashboard(User $user): View
    {
        $today = now()->startOfDay();

        $pendingPayments = Order::where('status', Order::STATUS_DELIVERED)
            ->whereDoesntHave('payments', fn($q) => $q->where('status', Payment::STATUS_COMPLETED))
            ->with(['waiter', 'items', 'table'])
            ->latest()
            ->get();

        return view('cashier.dashboard', [
            'pendingPayments' => $pendingPayments,
            'todayPayments' => Payment::where('cashier_id', $user->id)
                ->where('created_at', '>=', $today)
                ->with('order')
                ->latest()
                ->limit(10)
                ->get(),
            'todayRevenue' => (float) Payment::where('cashier_id', $user->id)
                ->where('created_at', '>=', $today)
                ->sum('total'),
            'todayTransactions' => Payment::where('cashier_id', $user->id)
                ->where('created_at', '>=', $today)
                ->count(),
            'recentPayments' => Payment::with(['order.waiter', 'order.items', 'cashier'])
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
