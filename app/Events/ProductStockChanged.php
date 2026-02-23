<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Product $product,
        public int $oldQuantity,
        public int $newQuantity,
        public string $reason = 'Atualização manual'
    ) {}
}
