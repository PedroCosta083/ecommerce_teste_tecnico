<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function findAll(): Collection
    {
        return Category::with(['parent', 'children'])->get();
    }

    public function findById(int $id): ?Category
    {
        return Category::with(['parent', 'children', 'products'])->find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::with(['parent', 'children', 'products'])->where('slug', $slug)->first();
    }

    public function findActive(): Collection
    {
        return Category::with(['parent', 'children'])->where('active', true)->get();
    }

    public function findRootCategories(): Collection
    {
        return Category::with(['children'])->whereNull('parent_id')->where('active', true)->get();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}