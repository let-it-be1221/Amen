<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\MenuItem;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
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

        $recentActivities = ActivityLog::with('user')
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
}
