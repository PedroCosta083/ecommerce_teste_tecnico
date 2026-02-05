<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Executar seeder de roles e permissions primeiro
        $this->call([
            RolePermissionSeeder::class,
        ]);
        
        // Criar usuários
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Atribuir roles aos usuários
        $adminUser->assignRole('admin');
        $testUser->assignRole('editor');
        
        User::factory(10)->create();

        // Executar seeders específicos
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
