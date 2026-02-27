<?php

namespace App\Policies;

use App\Models\User;

class DashboardPolicy
{
    public function viewMetrics(User $user): bool
    {
        return $user->can('products.view');
    }
}
