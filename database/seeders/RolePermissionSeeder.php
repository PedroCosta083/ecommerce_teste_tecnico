<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissions
        $permissions = [
            // Products
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            
            // Categories
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
            
            // Tags
            'tags.view',
            'tags.create',
            'tags.update',
            'tags.delete',
            
            // Orders
            'orders.view',
            'orders.create',
            'orders.update',
            'orders.delete',
            
            // Users
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            
            // Roles
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            
            // Permissions
            'permissions.view',
            'permissions.create',
            'permissions.delete',
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
            'products.view', 'products.create', 'products.update',
            'categories.view', 'categories.create', 'categories.update',
            'tags.view', 'tags.create', 'tags.update',
            'orders.view', 'orders.update',
        ]);
        
        $editorRole->givePermissionTo([
            'products.view', 'products.create', 'products.update',
            'categories.view', 'tags.view', 'orders.view',
        ]);
    }
}