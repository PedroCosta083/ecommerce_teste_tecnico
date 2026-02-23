<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Notification;

class OrderObserver
{
    public function created(Order $order): void
    {
        Notification::create([
            'type' => 'order_created',
            'title' => 'Novo Pedido',
            'message' => "Novo pedido #{$order->id} criado no valor de R$ " . number_format($order->total, 2, ',', '.'),
            'data' => ['order_id' => $order->id, 'total' => $order->total],
        ]);
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $statusLabels = [
                'pending' => 'Pendente',
                'processing' => 'Processando',
                'shipped' => 'Enviado',
                'delivered' => 'Entregue',
                'cancelled' => 'Cancelado',
            ];
            
            Notification::create([
                'type' => 'order_status_changed',
                'title' => 'Status do Pedido Alterado',
                'message' => "Pedido #{$order->id} mudou para: " . ($statusLabels[$order->status] ?? $order->status),
                'data' => ['order_id' => $order->id, 'status' => $order->status],
            ]);
        }
    }
}
