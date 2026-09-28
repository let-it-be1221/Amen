<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Get the unread notification count and recent notifications (polled via AJAX).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $unreadCount = $user->unreadNotifications()->count();

        $recent = $user->notifications()
            ->latest()
            ->limit(8)
            ->get()
            ->map(function ($n) use ($user) {
                $data = is_array($n->data) ? $n->data : [];
                $orderId = $data['order_id'] ?? null;
                $order = $orderId ? Order::find($orderId) : null;

                return [
                    'id' => $n->id,
                    'type' => class_basename($n->type),
                    'message' => $data['message'] ?? 'Notification',
                    'order_number' => $data['order_number'] ?? null,
                    'order_url' => $order && $user->isWaiter() ? route('waiter.orders.show', $order) : null,
                    'table_name' => $data['table_name'] ?? null,
                    'items_count' => $data['items_count'] ?? null,
                    'cooker_name' => $data['cooker_name'] ?? null,
                    'created_at' => $n->created_at?->diffForHumans(),
                    'read' => (bool) $n->read_at,
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $recent,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        // Redirect to the order detail page if possible
        $data = is_array($notification->data) ? $notification->data : [];
        $orderId = $data['order_id'] ?? null;

        if ($orderId && $request->user()->isWaiter()) {
            $order = Order::find($orderId);
            if ($order) {
                return redirect()->route('waiter.orders.show', $order);
            }
        }

        return redirect()->back();
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
