<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuários
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        User::factory(10)->create();

        // Criar categorias principais
        $electronics = Category::factory()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and accessories',
        ]);
        
        // Criar subcategorias
        Category::factory(5)->create([
            'parent_id' => $electronics->id,
        ]);
        
        Category::factory(10)->create();

        // Criar tags
        $tags = Tag::factory(15)->create();

        // Criar produtos
        $products = Product::factory(50)->create();
        
        // Associar tags aos produtos
        $products->each(function ($product) use ($tags) {
            $product->tags()->attach(
                $tags->random(rand(1, 4))->pluck('id')->toArray()
            );
        });

        // Criar movimentações de estoque
        $products->each(function ($product) {
            StockMovement::factory(rand(1, 5))->create([
                'product_id' => $product->id,
            ]);
        });

        // Criar pedidos
        $orders = Order::factory(20)->create();
        
        // Criar itens dos pedidos
        $orders->each(function ($order) use ($products) {
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

        // Criar carrinhos
        $users = User::all();
        $users->each(function ($user) use ($products) {
            $cart = Cart::create(['user_id' => $user->id]);
            
            $cartProducts = $products->random(rand(1, 3));
            $cartProducts->each(function ($product) use ($cart) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                ]);
            });
        });
    }
}
