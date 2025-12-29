<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        // Criar tags específicas
        $specificTags = [
            ['name' => 'New', 'slug' => 'new'],
            ['name' => 'Sale', 'slug' => 'sale'],
            ['name' => 'Featured', 'slug' => 'featured'],
            ['name' => 'Popular', 'slug' => 'popular'],
            ['name' => 'Limited Edition', 'slug' => 'limited-edition'],
            ['name' => 'Best Seller', 'slug' => 'best-seller'],
            ['name' => 'Eco Friendly', 'slug' => 'eco-friendly'],
            ['name' => 'Premium', 'slug' => 'premium'],
        ];

        foreach ($specificTags as $tagData) {
            Tag::factory()->create($tagData);
        }

        // Criar tags adicionais aleatórias
        Tag::factory(12)->create();
    }
}