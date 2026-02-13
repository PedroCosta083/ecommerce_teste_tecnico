<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function findAll(): Collection
    {
        return Cache::tags(['categories'])->remember('categories.all', 3600, function () {
            return Category::with(['parent', 'children'])->get();
        });
    }

    public function findById(int $id): ?Category
    {
        return Cache::tags(['categories'])->remember("categories.{$id}", 3600, function () use ($id) {
            return Category::with(['parent', 'children', 'products'])->find($id);
        });
    }

    public function findBySlug(string $slug): ?Category
    {
        return Cache::tags(['categories'])->remember("categories.slug.{$slug}", 3600, function () use ($slug) {
            return Category::with(['parent', 'children', 'products'])->where('slug', $slug)->first();
        });
    }

    public function findActive(): Collection
    {
        return Cache::tags(['categories'])->remember('categories.active', 3600, function () {
            return Category::with(['parent', 'children'])->where('active', true)->get();
        });
    }

    public function findRootCategories(): Collection
    {
        return Cache::tags(['categories'])->remember('categories.root', 3600, function () {
            return Category::with(['children'])->whereNull('parent_id')->where('active', true)->get();
        });
    }

    public function create(array $data): Category
    {
        $category = Category::create($data);
        $this->clearCache();
        return $category;
    }

    public function update(Category $category, array $data): bool
    {
        $result = $category->update($data);
        $this->clearCache();
        return $result;
    }

    public function delete(Category $category): bool
    {
        $result = $category->delete();
        $this->clearCache();
        return $result;
    }

    private function clearCache(): void
    {
        Cache::tags(['categories'])->flush();
        Cache::tags(['products'])->flush();
    }
}