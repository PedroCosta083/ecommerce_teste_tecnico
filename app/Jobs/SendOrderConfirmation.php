<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [2, 5, 10];

    public function __construct(
        public Order $order
    ) {}

    public function handle(): void
    {
        try {
            // Adiciona delay de 1 segundo para evitar rate limit
            sleep(1);
            
            Mail::to($this->order->user->email)->send(new OrderConfirmationMail($this->order));
            
            Log::info("Order confirmation email sent", [
                'order_id' => $this->order->id,
                'user_id' => $this->order->user_id,
                'email' => $this->order->user->email,
                'total' => $this->order->total,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send order confirmation email", [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
            ]);
            
            // Não lança exceção para não bloquear o checkout
            // O email será tentado novamente automaticamente
        }
    }
}
