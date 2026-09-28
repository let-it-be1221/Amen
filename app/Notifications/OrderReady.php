<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReady extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification (stored in DB).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'table_name' => $this->order->table?->name,
            'customer_name' => $this->order->customer_name,
            'items_count' => $this->order->items->count(),
            'ready_at' => $this->order->ready_at?->toIso8601String(),
            'cooker_name' => $this->order->cooker?->name,
            'message' => "Order {$this->order->order_number} is ready for delivery.",
        ];
    }
}
