<?php

namespace App\Listeners;

use App\Events\StockLow;
use Illuminate\Support\Facades\Log;

class NotifyLowStock
{
    public function handle(StockLow $event): void
    {
        // Notificar admins sobre estoque baixo
        Log::warning('Low stock alert', [
            'product_id' => $event->product->id,
            'name' => $event->product->name,
            'current_quantity' => $event->product->quantity,
            'min_quantity' => $event->product->min_quantity,
        ]);
        
        // Aqui poderia enviar email, SMS, notificação push, etc
        // Mail::to($admins)->send(new LowStockAlert($event->product));
    }
}
