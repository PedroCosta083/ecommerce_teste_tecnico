<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\DTOs\Product\ProductFilterDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function findAll(): Collection
    {
        return Product::with(['category', 'tags'])->get();
    }

    public function findById(int $id): ?Product
    {
        return Product::with(['category', 'tags', 'stockMovements'])->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::with(['category', 'tags', 'stockMovements'])->where('slug', $slug)->first();
    }

    public function findActive(): Collection
    {
        return Product::with(['category', 'tags'])->where('active', true)->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return Product::with(['category', 'tags'])->where('category_id', $categoryId)->where('active', true)->get();
    }

    public function findWithFilters(ProductFilterDTO $filters): LengthAwarePaginator
    {
        $query = Product::with(['category', 'tags']);

        if ($filters->search) {
            $query->where('name', 'like', '%' . $filters->search . '%');
        }

        if ($filters->categoryId) {
            $query->where('category_id', $filters->categoryId);
        }

        if ($filters->tagIds) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->whereIn('tags.id', $filters->tagIds);
            });
        }

        if ($filters->minPrice) {
            $query->where('price', '>=', $filters->minPrice);
        }

        if ($filters->maxPrice) {
            $query->where('price', '<=', $filters->maxPrice);
        }

        if ($filters->active !== null) {
            $query->where('active', $filters->active);
        }

        $query->orderBy($filters->sortBy, $filters->sortDirection);

        return $query->paginate($filters->perPage);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}