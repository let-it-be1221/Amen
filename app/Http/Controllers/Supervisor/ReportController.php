<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display all orders with filters (supervisor view).
     */
    public function orders(Request $request): View
    {
        $query = Order::with(['waiter', 'cooker', 'cashier', 'items', 'table']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($waiterId = $request->get('waiter_id')) {
            $query->where('waiter_id', $waiterId);
        }

        if ($cookerId = $request->get('cooker_id')) {
            $query->where('cooker_id', $cookerId);
        }

        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $waiters = User::where('role', User::ROLE_WAITER)->orderBy('name')->get();
        $cookers = User::where('role', User::ROLE_COOKER)->orderBy('name')->get();

        return view('supervisor.orders', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
            'waiters' => $waiters,
            'cookers' => $cookers,
            'filters' => $request->only(['status', 'waiter_id', 'cooker_id', 'from', 'to']),
        ]);
    }

    /**
     * Display the daily, weekly, monthly reports dashboard.
     */
    public function index(Request $request): View
    {
        $period = $request->get('period', 'daily');

        [$from, $to, $label] = $this->resolvePeriod($period, $request);

        $ordersQuery = Order::whereBetween('created_at', [$from, $to]);
        $paymentsQuery = Payment::where('status', Payment::STATUS_COMPLETED)
            ->whereBetween('created_at', [$from, $to]);

        $totalOrders = (clone $ordersQuery)->count();
        $deliveredOrders = (clone $ordersQuery)->where('status', Order::STATUS_DELIVERED)->count();
        $paidOrders = (clone $ordersQuery)->where('status', Order::STATUS_PAID)->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', Order::STATUS_CANCELLED)->count();
        $activeOrders = (clone $ordersQuery)->whereIn('status', Order::ACTIVE_STATUSES)->count();

        $totalRevenue = (float) (clone $paymentsQuery)->sum('total');
        $totalTax = (float) (clone $paymentsQuery)->sum('tax');
        $totalServiceCharge = (float) (clone $paymentsQuery)->sum('service_charge');
        $totalDiscount = (float) (clone $paymentsQuery)->sum('discount');
        $avgOrderValue = $paidOrders > 0 ? $totalRevenue / $paidOrders : 0;

        $revenueByDay = (clone $paymentsQuery)
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) ASC')
            ->get();

        $topWaiters = User::where('role', User::ROLE_WAITER)
            ->withCount(['ordersAsWaiter as total_orders' => function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to])
                    ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_PAID]);
            }])
            ->orderByDesc('total_orders')
            ->limit(10)
            ->get()
            ->filter(fn($u) => $u->total_orders > 0)
            ->values();

        $topCookers = User::where('role', User::ROLE_COOKER)
            ->withCount(['ordersAsCooker as total_orders' => function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to])
                    ->whereIn('status', [Order::STATUS_READY, Order::STATUS_DELIVERED, Order::STATUS_PAID]);
            }])
            ->orderByDesc('total_orders')
            ->limit(10)
            ->get()
            ->filter(fn($u) => $u->total_orders > 0)
            ->values();

        $ordersByStatus = [];
        foreach (Order::STATUSES as $key => $label) {
            $ordersByStatus[$key] = [
                'label' => $label,
                'count' => (clone $ordersQuery)->where('status', $key)->count(),
            ];
        }

        return view('supervisor.reports.index', [
            'period' => $period,
            'periodLabel' => $label,
            'from' => $from,
            'to' => $to,
            'totalOrders' => $totalOrders,
            'deliveredOrders' => $deliveredOrders,
            'paidOrders' => $paidOrders,
            'cancelledOrders' => $cancelledOrders,
            'activeOrders' => $activeOrders,
            'totalRevenue' => $totalRevenue,
            'totalTax' => $totalTax,
            'totalServiceCharge' => $totalServiceCharge,
            'totalDiscount' => $totalDiscount,
            'avgOrderValue' => $avgOrderValue,
            'revenueByDay' => $revenueByDay,
            'topWaiters' => $topWaiters,
            'topCookers' => $topCookers,
            'ordersByStatus' => $ordersByStatus,
        ]);
    }

    private function resolvePeriod(string $period, Request $request): array
    {
        $now = now();

        if ($request->get('from') && $request->get('to')) {
            $from = \Carbon\Carbon::parse($request->get('from'))->startOfDay();
            $to = \Carbon\Carbon::parse($request->get('to'))->endOfDay();
            return [$from, $to, 'Custom Range'];
        }

        return match ($period) {
            'daily' => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'Today'],
            'weekly' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), 'This Week'],
            'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), 'This Month'],
            'yearly' => [$now->copy()->startOfYear(), $now->copy()->endOfYear(), 'This Year'],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'Today'],
        };
    }
}
