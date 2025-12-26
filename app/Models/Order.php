<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\OrderStatus;


class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total',
        'subtotal',
        'tax',
        'shipping_cost',
        'shipping_address',
        'billing_address',
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'status' => OrderStatus::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}