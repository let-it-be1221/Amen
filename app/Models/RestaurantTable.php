<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = [
        'name',
        'seats',
        'location',
        'status',
        'is_active',
    ];

    protected $casts = [
        'seats' => 'integer',
        'is_active' => 'boolean',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_RESERVED = 'reserved';

    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_RESERVED => 'Reserved',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }
}
