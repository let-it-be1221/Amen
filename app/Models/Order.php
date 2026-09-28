<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    const STATUS_ORDERED = 'ordered';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_COOKING = 'cooking';
    const STATUS_READY = 'ready';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_ORDERED => 'Ordered',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_COOKING => 'Cooking',
        self::STATUS_READY => 'Ready',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_PAID => 'Paid',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    const ACTIVE_STATUSES = [
        self::STATUS_ORDERED,
        self::STATUS_ACCEPTED,
        self::STATUS_COOKING,
        self::STATUS_READY,
        self::STATUS_DELIVERED,
    ];

    const ORDER_TYPE_DINE_IN = 'dine_in';
    const ORDER_TYPE_TAKEAWAY = 'takeaway';
    const ORDER_TYPE_DELIVERY = 'delivery';

    const ORDER_TYPES = [
        self::ORDER_TYPE_DINE_IN => 'Dine In',
        self::ORDER_TYPE_TAKEAWAY => 'Takeaway',
        self::ORDER_TYPE_DELIVERY => 'Delivery',
    ];

    protected $fillable = [
        'order_number',
        'waiter_id',
        'cooker_id',
        'cashier_id',
        'table_id',
        'customer_name',
        'customer_phone',
        'customer_count',
        'order_type',
        'status',
        'notes',
        'accepted_at',
        'cooking_started_at',
        'ready_at',
        'delivered_at',
        'paid_at',
        'cancelled_at',
    ];

    protected $casts = [
        'customer_count' => 'integer',
        'accepted_at' => 'datetime',
        'cooking_started_at' => 'datetime',
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
            if (empty($order->status)) {
                $order->status = self::STATUS_ORDERED;
            }
        });
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function cooker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cooker_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->payments()->latest()->limit(1);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function orderTypeLabel(): string
    {
        return self::ORDER_TYPES[$this->order_type] ?? ucfirst($this->order_type);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_ORDERED => 'bg-blue-100 text-blue-800 border-blue-200',
            self::STATUS_ACCEPTED => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            self::STATUS_COOKING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::STATUS_READY => 'bg-violet-100 text-violet-800 border-violet-200',
            self::STATUS_DELIVERED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::STATUS_PAID => 'bg-green-100 text-green-800 border-green-200',
            self::STATUS_CANCELLED => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function totalAmount(): float
    {
        return (float) $this->items()->sum('subtotal');
    }

    public function formattedTotal(): string
    {
        return '$' . number_format($this->totalAmount(), 2);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true);
    }

    public function isDelivered(): bool
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_PAID], true);
    }
}
