<?php

namespace Database\Seeders;

use App\Models\{Product, Category, Tag, StockMovement};
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Garantir que existam categorias e tags
        $categories = Category::all();
        $tags = Tag::all();

        if ($categories->isEmpty()) {
            $categories = Category::factory(10)->create();
        }

        if ($tags->isEmpty()) {
            $tags = Tag::factory(15)->create();
        }

        // Criar produtos
        Product::factory(100)->create()->each(function ($product) use ($tags) {
            // Associar tags aleatórias
            $product->tags()->attach(
                $tags->random(rand(1, 5))->pluck('id')->toArray()
            );

            // Criar movimentações de estoque
            StockMovement::factory(rand(2, 8))->create([
                'product_id' => $product->id,
            ]);
        });
    }
}