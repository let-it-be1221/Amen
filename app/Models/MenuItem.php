<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'preparation_time',
        'is_available',
        'is_vegetarian',
        'is_spicy',
        'calories',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_spicy' => 'boolean',
        'preparation_time' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (MenuItem $item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name) . '-' . Str::random(5);
            }
        });

        static::updating(function (MenuItem $item) {
            if ($item->isDirty('name') && empty($item->slug)) {
                $item->slug = Str::slug($item->name) . '-' . Str::random(5);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function formattedPrice(): string
    {
        return '$' . number_format($this->price, 2);
    }
}
