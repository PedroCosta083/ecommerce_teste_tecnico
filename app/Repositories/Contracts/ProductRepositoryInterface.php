<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use App\DTOs\Product\ProductFilterDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function findAll(): Collection;
    public function findById(int $id): ?Product;
    public function findBySlug(string $slug): ?Product;
    public function findActive(): Collection;
    public function findByCategory(int $categoryId): Collection;
    public function findWithFilters(ProductFilterDTO $filters): LengthAwarePaginator;
    public function create(array $data): Product;
    public function update(Product $product, array $data): bool;
    public function delete(Product $product): bool;
}