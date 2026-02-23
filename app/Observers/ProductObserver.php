<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Notification;

class ProductObserver
{
    public function created(Product $product): void
    {
        Notification::create([
            'type' => 'product_created',
            'title' => 'Produto Criado',
            'message' => "Novo produto '{$product->name}' foi criado.",
            'data' => ['product_id' => $product->id, 'product_name' => $product->name],
        ]);
    }

    public function updated(Product $product): void
    {
        if ($product->wasChanged(['name', 'price', 'quantity'])) {
            Notification::create([
                'type' => 'product_updated',
                'title' => 'Produto Atualizado',
                'message' => "O produto '{$product->name}' foi atualizado.",
                'data' => ['product_id' => $product->id, 'product_name' => $product->name],
            ]);
        }
    }

    public function deleted(Product $product): void
    {
        Notification::create([
            'type' => 'product_deleted',
            'title' => 'Produto Excluído',
            'message' => "O produto '{$product->name}' foi excluído.",
            'data' => ['product_name' => $product->name],
        ]);
    }
}
