<?php

namespace App\Repositories\Eloquent;

use App\Models\OrderItem;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    public function findByOrder(int $orderId): Collection
    {
        return OrderItem::with(['product'])->where('order_id', $orderId)->get();
    }

    public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    public function createMany(array $items): bool
    {
        return OrderItem::insert($items);
    }

    public function update(OrderItem $orderItem, array $data): bool
    {
        return $orderItem->update($data);
    }

    public function delete(OrderItem $orderItem): bool
    {
        return $orderItem->delete();
    }
}