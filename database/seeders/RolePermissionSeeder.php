<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Criar permissions
        $permissions = [
            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Tags
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            
            // Orders
            'view orders',
            'edit orders',
            'delete orders',
            
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Roles & Permissions
            'manage roles',
            'manage permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Criar roles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $editorRole = Role::create(['name' => 'editor']);

        // Atribuir permissions aos roles
        $adminRole->givePermissionTo(Permission::all());
        
        $managerRole->givePermissionTo([
            'view products', 'create products', 'edit products',
            'view categories', 'create categories', 'edit categories',
            'view tags', 'create tags', 'edit tags',
            'view orders', 'edit orders',
        ]);
        
        $editorRole->givePermissionTo([
            'view products', 'create products', 'edit products',
            'view categories', 'view tags', 'view orders',
        ]);
    }
}