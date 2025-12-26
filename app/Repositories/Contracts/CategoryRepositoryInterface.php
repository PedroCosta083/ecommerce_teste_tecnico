<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function findAll(): Collection;
    public function findById(int $id): ?Category;
    public function findBySlug(string $slug): ?Category;
    public function findActive(): Collection;
    public function findRootCategories(): Collection;
    public function create(array $data): Category;
    public function update(Category $category, array $data): bool;
    public function delete(Category $category): bool;
}