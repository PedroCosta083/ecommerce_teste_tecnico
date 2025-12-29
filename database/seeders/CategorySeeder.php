<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Criar categorias principais
        $electronics = Category::factory()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and accessories',
        ]);

        $clothing = Category::factory()->create([
            'name' => 'Clothing',
            'slug' => 'clothing',
            'description' => 'Fashion and apparel',
        ]);

        $home = Category::factory()->create([
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
            'description' => 'Home improvement and garden supplies',
        ]);

        // Criar subcategorias para Electronics
        Category::factory(3)->create([
            'parent_id' => $electronics->id,
        ]);

        // Criar subcategorias para Clothing
        Category::factory(4)->create([
            'parent_id' => $clothing->id,
        ]);

        // Criar subcategorias para Home
        Category::factory(2)->create([
            'parent_id' => $home->id,
        ]);

        // Criar categorias adicionais sem pai
        Category::factory(5)->create();
    }
}