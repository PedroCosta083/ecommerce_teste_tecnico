<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Criar pedidos
        Order::factory(50)->create()->each(function ($order) use ($products) {
            // Criar itens para cada pedido
            $orderProducts = $products->random(rand(1, 5));
            
            $orderProducts->each(function ($product) use ($order) {
                $quantity = rand(1, 3);
                $unitPrice = $product->price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $quantity * $unitPrice,
                ]);
            });
        });
    }
}