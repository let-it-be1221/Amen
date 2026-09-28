<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';
    const METHOD_MOBILE = 'mobile';

    const METHODS = [
        self::METHOD_CASH => 'Cash',
        self::METHOD_CARD => 'Card',
        self::METHOD_MOBILE => 'Mobile Money',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REFUNDED = 'refunded';

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_REFUNDED => 'Refunded',
    ];

    protected $fillable = [
        'payment_number',
        'order_id',
        'cashier_id',
        'subtotal',
        'tax',
        'service_charge',
        'discount',
        'total',
        'amount_paid',
        'change',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = 'PAY-' . strtoupper(Str::random(8));
            }
            if (empty($payment->status)) {
                $payment->status = self::STATUS_COMPLETED;
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function formattedTotal(): string
    {
        return '$' . number_format($this->total, 2);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800',
            self::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-800',
            self::STATUS_REFUNDED => 'bg-rose-100 text-rose-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
