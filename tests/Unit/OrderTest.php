<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\{Order, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_user()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->id, $order->user->id);
    }

    public function test_order_has_status()
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $this->assertEquals('pending', $order->status);
    }

    public function test_order_can_update_status()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $order->update(['status' => 'processing']);

        $this->assertEquals('processing', $order->fresh()->status);
    }
}
