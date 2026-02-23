<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
            
            'orders.view',
            'orders.create',
            'orders.update',
            'orders.delete',
            
            'tags.view',
            'tags.create',
            'tags.update',
            'tags.delete',
            
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            
            'permissions.view',
            'permissions.create',
            'permissions.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'products.view', 'products.create', 'products.update',
            'categories.view', 'categories.create', 'categories.update',
            'orders.view', 'orders.update',
            'tags.view', 'tags.create', 'tags.update',
        ]);

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([
            'products.view',
            'categories.view',
            'orders.view', 'orders.create',
        ]);
    }
}
