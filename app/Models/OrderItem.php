<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'menu_item_name',
        'menu_item_price',
        'quantity',
        'subtotal',
        'notes',
        'status',
    ];

    protected $casts = [
        'menu_item_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_COOKING = 'cooking';
    const STATUS_READY = 'ready';
    const STATUS_SERVED = 'served';

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_COOKING => 'Cooking',
        self::STATUS_READY => 'Ready',
        self::STATUS_SERVED => 'Served',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-gray-100 text-gray-800',
            self::STATUS_COOKING => 'bg-amber-100 text-amber-800',
            self::STATUS_READY => 'bg-violet-100 text-violet-800',
            self::STATUS_SERVED => 'bg-emerald-100 text-emerald-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function formattedPrice(): string
    {
        return '$' . number_format($this->menu_item_price, 2);
    }

    public function formattedSubtotal(): string
    {
        return '$' . number_format($this->subtotal, 2);
    }
}
