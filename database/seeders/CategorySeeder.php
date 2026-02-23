<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Eletrônicos
        $eletronicos = Category::create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'description' => 'Smartphones, notebooks, tablets e acessórios',
        ]);

        Category::create(['name' => 'Smartphones', 'slug' => 'smartphones', 'description' => 'Celulares e acessórios', 'parent_id' => $eletronicos->id]);
        Category::create(['name' => 'Notebooks', 'slug' => 'notebooks', 'description' => 'Computadores portáteis', 'parent_id' => $eletronicos->id]);
        Category::create(['name' => 'Tablets', 'slug' => 'tablets', 'description' => 'Tablets e e-readers', 'parent_id' => $eletronicos->id]);
        Category::create(['name' => 'Fones de Ouvido', 'slug' => 'fones-de-ouvido', 'description' => 'Fones e headsets', 'parent_id' => $eletronicos->id]);

        // Moda
        $moda = Category::create([
            'name' => 'Moda',
            'slug' => 'moda',
            'description' => 'Roupas, calçados e acessórios',
        ]);

        Category::create(['name' => 'Camisetas', 'slug' => 'camisetas', 'description' => 'Camisetas masculinas e femininas', 'parent_id' => $moda->id]);
        Category::create(['name' => 'Calças', 'slug' => 'calcas', 'description' => 'Calças jeans e sociais', 'parent_id' => $moda->id]);
        Category::create(['name' => 'Tênis', 'slug' => 'tenis', 'description' => 'Tênis esportivos e casuais', 'parent_id' => $moda->id]);
        Category::create(['name' => 'Bolsas', 'slug' => 'bolsas', 'description' => 'Bolsas e mochilas', 'parent_id' => $moda->id]);

        // Casa e Decoração
        $casa = Category::create([
            'name' => 'Casa e Decoração',
            'slug' => 'casa-decoracao',
            'description' => 'Móveis, decoração e utilidades domésticas',
        ]);

        Category::create(['name' => 'Móveis', 'slug' => 'moveis', 'description' => 'Sofás, mesas e cadeiras', 'parent_id' => $casa->id]);
        Category::create(['name' => 'Decoração', 'slug' => 'decoracao', 'description' => 'Quadros, vasos e objetos decorativos', 'parent_id' => $casa->id]);
        Category::create(['name' => 'Cozinha', 'slug' => 'cozinha', 'description' => 'Utensílios e eletrodomésticos', 'parent_id' => $casa->id]);

        // Esportes
        $esportes = Category::create([
            'name' => 'Esportes e Fitness',
            'slug' => 'esportes-fitness',
            'description' => 'Equipamentos esportivos e fitness',
        ]);

        Category::create(['name' => 'Academia', 'slug' => 'academia', 'description' => 'Halteres, colchonetes e acessórios', 'parent_id' => $esportes->id]);
        Category::create(['name' => 'Ciclismo', 'slug' => 'ciclismo', 'description' => 'Bicicletas e acessórios', 'parent_id' => $esportes->id]);

        // Livros
        Category::create([
            'name' => 'Livros',
            'slug' => 'livros',
            'description' => 'Livros físicos e digitais',
        ]);

        // Beleza
        Category::create([
            'name' => 'Beleza e Cuidados',
            'slug' => 'beleza-cuidados',
            'description' => 'Cosméticos, perfumes e cuidados pessoais',
        ]);
    }
}