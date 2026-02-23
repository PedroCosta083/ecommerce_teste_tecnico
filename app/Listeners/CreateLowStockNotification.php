<?php

namespace App\Listeners;

use App\Events\StockLow;
use App\Models\Notification;

class CreateLowStockNotification
{
    public function handle(StockLow $event): void
    {
        Notification::create([
            'type' => 'low_stock',
            'title' => 'Estoque Baixo',
            'message' => "O produto '{$event->product->name}' está com estoque baixo ({$event->product->quantity} unidades).",
            'data' => [
                'product_id' => $event->product->id,
                'product_name' => $event->product->name,
                'quantity' => $event->product->quantity,
                'min_quantity' => $event->product->min_quantity,
            ],
        ]);
    }
}
