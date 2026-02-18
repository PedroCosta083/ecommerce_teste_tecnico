<?php

namespace App\Listeners;

use App\Events\StockLow;
use Illuminate\Support\Facades\Log;

class LogLowStock
{
    public function handle(StockLow $event): void
    {
        Log::channel('stock')->warning('Product stock is low', [
            'product_id' => $event->product->id,
            'name' => $event->product->name,
            'slug' => $event->product->slug,
            'quantity' => $event->product->quantity,
            'min_quantity' => $event->product->min_quantity,
            'difference' => $event->product->min_quantity - $event->product->quantity,
        ]);
    }
}
