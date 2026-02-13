<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\DTOs\Order\OrderFilterDTO;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function findAll(): Collection
    {
        return Order::with(['user', 'orderItems.product'])->get();
    }

    public function findById(int $id): ?Order
    {
        return Order::with(['user', 'orderItems.product'])->find($id);
    }

    public function findByUser(int $userId): Collection
    {
        return Order::with(['orderItems.product'])->where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    }

    public function findWithFilters(OrderFilterDTO $filters): LengthAwarePaginator
    {
        $query = Order::query();

        if ($filters->userId) {
            $query->where('user_id', $filters->userId);
        }

        if ($filters->status) {
            $query->where('status', $filters->status);
        }

        if ($filters->dateFrom) {
            $query->whereDate('created_at', '>=', $filters->dateFrom);
        }

        if ($filters->dateTo) {
            $query->whereDate('created_at', '<=', $filters->dateTo);
        }

        if ($filters->minTotal) {
            $query->where('total', '>=', $filters->minTotal);
        }

        if ($filters->maxTotal) {
            $query->where('total', '<=', $filters->maxTotal);
        }

        $query->orderBy($filters->sortBy, $filters->sortDirection);

        return $query->with(['user', 'orderItems.product'])->paginate($filters->perPage);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }
}