<?php

namespace App\Jobs;

use App\Events\StockLow;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateStock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $productId,
        public string $type,
        public int $quantity,
        public string $reason,
        public ?string $referenceType = null,
        public ?int $referenceId = null
    ) {}

    public function handle(): void
    {
        $product = Product::findOrFail($this->productId);

        if ($this->type === 'entrada' || $this->type === 'devolucao') {
            $product->increment('quantity', $this->quantity);
        } elseif ($this->type === 'saida' || $this->type === 'venda') {
            $product->decrement('quantity', $this->quantity);
        } elseif ($this->type === 'ajuste') {
            $product->update(['quantity' => $this->quantity]);
        }

        StockMovement::create([
            'product_id' => $this->productId,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'reason' => $this->reason,
            'reference_type' => $this->referenceType,
            'reference_id' => $this->referenceId,
        ]);

        // Verificar se estoque está baixo e disparar evento
        $product->refresh();
        if ($product->min_quantity && $product->quantity < $product->min_quantity) {
            StockLow::dispatch($product);
        }
    }
}
