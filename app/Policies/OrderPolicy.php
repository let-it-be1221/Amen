<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view the order.
     * - Admin/Supervisor: all orders
     * - Waiter: own orders only
     * - Cooker: orders assigned to them or pending orders
     * - Cashier: delivered orders awaiting payment
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin() || $user->isSupervisor()) {
            return true;
        }

        if ($user->isWaiter() && $order->waiter_id === $user->id) {
            return true;
        }

        if ($user->isCooker() && ($order->cooker_id === $user->id || $order->status === Order::STATUS_ORDERED)) {
            return true;
        }

        if ($user->isCashier() && in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_PAID], true)) {
            return true;
        }

        return false;
    }

    public function cancel(User $user, Order $order): bool
    {
        if ($user->isAdmin() || $user->isSupervisor()) {
            return true;
        }

        if ($user->isWaiter() && $order->waiter_id === $user->id) {
            return in_array($order->status, [Order::STATUS_ORDERED, Order::STATUS_ACCEPTED], true);
        }

        return false;
    }

    public function updateStatus(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCooker() && in_array($order->status, [Order::STATUS_ACCEPTED, Order::STATUS_COOKING, Order::STATUS_READY], true)) {
            return $order->cooker_id === $user->id || $order->cooker_id === null;
        }

        return false;
    }

    public function accept(User $user, Order $order): bool
    {
        if ($user->isCooker() && $order->status === Order::STATUS_ORDERED) {
            return true;
        }

        return $user->isAdmin();
    }
}
