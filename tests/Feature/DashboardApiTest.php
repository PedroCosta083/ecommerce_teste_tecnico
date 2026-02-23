<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_admin_can_access_dashboard_metrics()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/dashboard/metrics');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'overview',
                    'sales_by_status',
                    'top_products',
                    'sales_last_7_days',
                    'products_by_category',
                ],
            ]);
    }

    public function test_manager_can_access_dashboard_metrics()
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager, 'sanctum')
            ->getJson('/api/v1/dashboard/metrics');

        $response->assertOk();
    }

    public function test_regular_user_cannot_access_dashboard_metrics()
    {
        $user = User::factory()->create();
        // Usuário sem role não tem permissão

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard/metrics');

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_dashboard_metrics()
    {
        $response = $this->getJson('/api/v1/dashboard/metrics');

        $response->assertUnauthorized();
    }
}
