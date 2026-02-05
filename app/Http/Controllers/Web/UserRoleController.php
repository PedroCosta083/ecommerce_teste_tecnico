<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserRoleController extends Controller
{
    public function index(): Response
    {
        $users = User::with('roles')->get();
        
        return Inertia::render('users/index', [
            'users' => $users,
        ]);
    }

    public function edit(User $user): Response
    {
        $roles = Role::all();
        
        return Inertia::render('users/edit-roles', [
            'user' => $user->load('roles'),
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
        ]);

        $user->syncRoles($request->roles ?? []);

        return redirect()->route('users.index')
            ->with('success', 'Roles do usuário atualizadas com sucesso!');
    }
}