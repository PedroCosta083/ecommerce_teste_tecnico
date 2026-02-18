<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use Illuminate\Support\Facades\Log;

class SendProductCreatedNotification
{
    public function handle(ProductCreated $event): void
    {
        // Notificar admins sobre novo produto
        Log::info('Product created notification sent', [
            'product_id' => $event->product->id,
            'name' => $event->product->name,
        ]);
        
        // Aqui poderia enviar email, notificação push, etc
        // Mail::to($admins)->send(new ProductCreatedMail($event->product));
    }
}
