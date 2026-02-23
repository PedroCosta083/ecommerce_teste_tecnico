<?php

namespace App\Jobs;

use App\Events\ProductStockChanged;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function handle(): void
    {
        DB::transaction(function () {
            foreach ($this->order->orderItems as $item) {
                $product = $item->product;
                
                if ($product->quantity < $item->quantity) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $oldQuantity = $product->quantity;
                $product->decrement('quantity', $item->quantity);
                $product->refresh();
                
                ProductStockChanged::dispatch(
                    $product,
                    $oldQuantity,
                    $product->quantity,
                    'Venda - Pedido #' . $this->order->id
                );
            }

            $this->order->update(['status' => 'processing']);
        });
    }
}
