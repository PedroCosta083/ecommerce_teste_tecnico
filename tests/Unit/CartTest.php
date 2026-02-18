<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\{Cart, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_belongs_to_user()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $cart->user);
        $this->assertEquals($user->id, $cart->user->id);
    }

    public function test_cart_can_be_created_with_session_id()
    {
        $cart = Cart::factory()->create(['session_id' => 'test-session', 'user_id' => null]);

        $this->assertEquals('test-session', $cart->session_id);
        $this->assertNull($cart->user_id);
    }
}
