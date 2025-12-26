<?php

namespace App\Repositories\Eloquent;

use App\Models\StockMovement;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StockMovementRepository implements StockMovementRepositoryInterface
{
    public function findByProduct(int $productId): Collection
    {
        return StockMovement::with(['product'])->where('product_id', $productId)->orderBy('created_at', 'desc')->get();
    }

    public function findByType(string $type): Collection
    {
        return StockMovement::with(['product'])->where('type', $type)->orderBy('created_at', 'desc')->get();
    }

    public function findRecent(int $limit = 50): Collection
    {
        return StockMovement::with(['product'])->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function create(array $data): StockMovement
    {
        return StockMovement::create($data);
    }

    public function getTotalByProductAndType(int $productId, string $type): int
    {
        return StockMovement::where('product_id', $productId)
            ->where('type', $type)
            ->sum('quantity');
    }
}