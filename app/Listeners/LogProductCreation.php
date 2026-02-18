<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use Illuminate\Support\Facades\Log;

class LogProductCreation
{
    public function handle(ProductCreated $event): void
    {
        Log::info('Product created', [
            'product_id' => $event->product->id,
            'name' => $event->product->name,
            'price' => $event->product->price,
            'quantity' => $event->product->quantity,
        ]);
    }
}
