<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AssignAdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        
        if ($user) {
            $user->assignRole('admin');
            $this->command->info("Role admin atribuída ao usuário {$user->email}");
        } else {
            $this->command->error('Usuário admin@example.com não encontrado');
        }
    }
}
