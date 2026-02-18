<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\ProcessOrder;
use App\Jobs\SendOrderConfirmation;

class ProcessOrderCreated
{
    public function handle(OrderCreated $event): void
    {
        // Dispatch jobs para processar pedido
        ProcessOrder::dispatch($event->order);
        SendOrderConfirmation::dispatch($event->order);
    }
}
