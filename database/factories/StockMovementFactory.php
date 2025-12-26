<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'type' => fake()->randomElement(['entrada', 'saida', 'ajuste', 'venda', 'devolucao']),
            'quantity' => fake()->numberBetween(1, 50),
            'reason' => fake()->sentence(),
            'reference_type' => fake()->randomElement(['purchase', 'sale', 'adjustment', 'return']),
            'reference_id' => fake()->numberBetween(1, 100),
        ];
    }
}