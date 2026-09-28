<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderWorkflowService
{
    /**
     * Transition an order to a new status, recording the change in the history table.
     * Validates the transition according to the workflow:
     *   ordered -> accepted -> cooking -> ready -> delivered -> paid
     *   (any active status -> cancelled is allowed)
     */
    public function transition(Order $order, string $newStatus, ?User $user = null, ?string $notes = null): Order
    {
        $allowed = $this->allowedTransitions($order->status);

        if (!in_array($newStatus, $allowed, true)) {
            throw new \InvalidArgumentException(
                "Cannot transition order from '{$order->status}' to '{$newStatus}'."
            );
        }

        return DB::transaction(function () use ($order, $newStatus, $user, $notes) {
            $now = now();

            $updates = ['status' => $newStatus];

            // Set timestamps based on the new status
            match ($newStatus) {
                Order::STATUS_ACCEPTED => $updates['accepted_at'] = $now,
                Order::STATUS_COOKING => $updates['cooking_started_at'] = $now,
                Order::STATUS_READY => $updates['ready_at'] = $now,
                Order::STATUS_DELIVERED => $updates['delivered_at'] = $now,
                Order::STATUS_PAID => $updates['paid_at'] = $now,
                Order::STATUS_CANCELLED => $updates['cancelled_at'] = $now,
                default => null,
            };

            $order->update($updates);

            // Record the history entry
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => $user?->id,
                'status' => $newStatus,
                'notes' => $notes,
            ]);

            return $order->fresh();
        });
    }

    public function allowedTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            Order::STATUS_ORDERED => [Order::STATUS_ACCEPTED, Order::STATUS_CANCELLED],
            Order::STATUS_ACCEPTED => [Order::STATUS_COOKING, Order::STATUS_CANCELLED],
            Order::STATUS_COOKING => [Order::STATUS_READY, Order::STATUS_CANCELLED],
            Order::STATUS_READY => [Order::STATUS_DELIVERED, Order::STATUS_CANCELLED],
            Order::STATUS_DELIVERED => [Order::STATUS_PAID, Order::STATUS_CANCELLED],
            Order::STATUS_PAID, Order::STATUS_CANCELLED => [], // terminal states
            default => [],
        };
    }

    /**
     * Assign a cooker to an order when it is accepted / starts cooking.
     */
    public function assignCooker(Order $order, User $cooker): Order
    {
        if (!$cooker->isCooker() && !$cooker->isAdmin()) {
            throw new \InvalidArgumentException('Only cookers can be assigned to orders.');
        }

        $order->update(['cooker_id' => $cooker->id]);

        return $order->fresh();
    }

    /**
     * Assign a cashier to an order when a payment is processed.
     */
    public function assignCashier(Order $order, User $cashier): Order
    {
        if (!$cashier->isCashier() && !$cashier->isAdmin()) {
            throw new \InvalidArgumentException('Only cashiers can process payments.');
        }

        $order->update(['cashier_id' => $cashier->id]);

        return $order->fresh();
    }
}
