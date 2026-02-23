<?php

namespace App\Listeners;

use App\Events\ProductStockChanged;
use App\Models\StockMovement;

class RecordStockMovement
{
    public function handle(ProductStockChanged $event): void
    {
        $quantityDiff = $event->newQuantity - $event->oldQuantity;
        
        if ($quantityDiff === 0) {
            return;
        }
        
        $type = $quantityDiff > 0 ? 'entrada' : 'saida';
        
        // Verificar se já existe movimentação idêntica nos últimos 2 segundos
        $exists = StockMovement::where('product_id', $event->product->id)
            ->where('type', $type)
            ->where('quantity', abs($quantityDiff))
            ->where('reason', $event->reason)
            ->where('created_at', '>=', now()->subSeconds(2))
            ->exists();
            
        if ($exists) {
            return;
        }
        
        StockMovement::create([
            'product_id' => $event->product->id,
            'type' => $type,
            'quantity' => abs($quantityDiff),
            'reason' => $event->reason,
        ]);
    }
}
