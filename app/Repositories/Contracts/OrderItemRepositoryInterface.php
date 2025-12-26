<?php

namespace App\Repositories\Contracts;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

interface OrderItemRepositoryInterface
{
    public function findByOrder(int $orderId): Collection;
    public function create(array $data): OrderItem;
    public function createMany(array $items): bool;
    public function update(OrderItem $orderItem, array $data): bool;
    public function delete(OrderItem $orderItem): bool;
}