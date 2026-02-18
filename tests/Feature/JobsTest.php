<?php

namespace Tests\Feature;

use App\Jobs\ProcessOrder;
use App\Jobs\SendOrderConfirmation;
use App\Jobs\UpdateStock;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class JobsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_process_order_job_updates_stock_and_status(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create(['quantity' => 100]);
        
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $order->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => $product->price,
            'total_price' => $product->price * 10,
        ]);

        $job = new ProcessOrder($order->fresh(['orderItems.product']));
        $job->handle();

        $this->assertEquals('processing', $order->fresh()->status);
        
        Queue::assertPushed(UpdateStock::class, function ($job) use ($product, $order) {
            return $job->productId === $product->id
                && $job->type === 'venda'
                && $job->quantity === 10
                && $job->referenceId === $order->id;
        });
    }

    public function test_send_order_confirmation_job_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $job = new SendOrderConfirmation($order->fresh('user'));
        $job->handle();

        Mail::assertSent(\App\Mail\OrderConfirmationMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });
    }

    public function test_update_stock_job_increments_quantity(): void
    {
        $product = Product::factory()->create(['quantity' => 50]);

        $job = new UpdateStock(
            productId: $product->id,
            type: 'entrada',
            quantity: 20,
            reason: 'Compra'
        );
        $job->handle();

        $this->assertEquals(70, $product->fresh()->quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'entrada',
            'quantity' => 20,
        ]);
    }

    public function test_update_stock_job_decrements_quantity(): void
    {
        $product = Product::factory()->create(['quantity' => 50]);

        $job = new UpdateStock(
            productId: $product->id,
            type: 'saida',
            quantity: 15,
            reason: 'Ajuste'
        );
        $job->handle();

        $this->assertEquals(35, $product->fresh()->quantity);
    }

    public function test_jobs_are_dispatched_to_queue(): void
    {
        Queue::fake();

        $order = Order::factory()->create();

        ProcessOrder::dispatch($order);
        SendOrderConfirmation::dispatch($order);

        Queue::assertPushed(ProcessOrder::class);
        Queue::assertPushed(SendOrderConfirmation::class);
    }
}
