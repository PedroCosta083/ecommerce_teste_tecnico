<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\DTOs\Product\ProductFilterDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    public function findAll(): Collection
    {
        return Cache::tags(['products'])->remember('products.all', 3600, function () {
            return Product::with(['category', 'tags'])->get();
        });
    }

    public function findById(int $id): ?Product
    {
        return Cache::tags(['products'])->remember("products.{$id}", 3600, function () use ($id) {
            return Product::with(['category', 'tags', 'stockMovements'])->find($id);
        });
    }

    public function findBySlug(string $slug): ?Product
    {
        return Cache::tags(['products'])->remember("products.slug.{$slug}", 3600, function () use ($slug) {
            return Product::with(['category', 'tags', 'stockMovements'])->where('slug', $slug)->first();
        });
    }

    public function findActive(): Collection
    {
        return Cache::tags(['products'])->remember('products.active', 3600, function () {
            return Product::with(['category', 'tags'])->where('active', true)->get();
        });
    }

    public function findByCategory(int $categoryId): Collection
    {
        return Cache::tags(['products', 'categories'])->remember("products.category.{$categoryId}", 3600, function () use ($categoryId) {
            return Product::with(['category', 'tags'])->where('category_id', $categoryId)->where('active', true)->get();
        });
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
        $product = Product::create($data);
        $this->clearCache();
        return $product;
    }

    public function update(Product $product, array $data): bool
    {
        $result = $product->update($data);
        $this->clearCache();
        return $result;
    }

    public function delete(Product $product): bool
    {
        $result = $product->delete();
        $this->clearCache();
        return $result;
    }

    private function clearCache(): void
    {
        Cache::tags(['products'])->flush();
    }
}