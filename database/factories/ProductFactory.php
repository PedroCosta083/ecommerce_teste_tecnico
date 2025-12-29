<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 10, 2000);
        $costPrice = $price * fake()->randomFloat(2, 0.4, 0.8); // Cost price 40-80% of selling price
        
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->paragraph(),
            'price' => $price,
            'cost_price' => $costPrice,
            'quantity' => fake()->numberBetween(0, 100),
            'min_quantity' => fake()->numberBetween(1, 10),
            'active' => fake()->boolean(85),
            'category_id' => \App\Models\Category::factory(),
        ];
    }
}
