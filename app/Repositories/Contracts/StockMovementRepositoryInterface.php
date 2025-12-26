<?php

namespace App\Repositories\Contracts;

use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Collection;

interface StockMovementRepositoryInterface
{
    public function findByProduct(int $productId): Collection;
    public function findByType(string $type): Collection;
    public function findRecent(int $limit = 50): Collection;
    public function create(array $data): StockMovement;
    public function getTotalByProductAndType(int $productId, string $type): int;
}