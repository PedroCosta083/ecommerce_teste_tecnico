<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\DTOs\Order\OrderFilterDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function findAll(): Collection;
    public function findById(int $id): ?Order;
    public function findByUser(int $userId): Collection;
    public function findWithFilters(OrderFilterDTO $filters): LengthAwarePaginator;
    public function create(array $data): Order;
    public function update(Order $order, array $data): bool;
    public function delete(Order $order): bool;
}