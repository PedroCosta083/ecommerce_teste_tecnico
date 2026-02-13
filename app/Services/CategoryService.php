<?php

namespace App\Services;

use App\DTOs\Category\CreateCategoryDTO;
use App\DTOs\Category\UpdateCategoryDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->findAll();
    }

    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function getCategoryBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    public function getActiveCategories(): Collection
    {
        return $this->categoryRepository->findActive();
    }

    public function getRootCategories(): Collection
    {
        return $this->categoryRepository->findRootCategories();
    }

    public function getCategoryProducts(int $categoryId)
    {
        $category = $this->categoryRepository->findById($categoryId);
        
        if (!$category) {
            return null;
        }

        return $category->products()->with(['category', 'tags'])->paginate(15);
    }

    public function createCategory(CreateCategoryDTO $dto): Category
    {
        $data = [
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description,
            'parent_id' => $dto->parentId,
            'active' => $dto->active,
        ];

        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, UpdateCategoryDTO $dto): ?Category
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            return null;
        }


        $data = array_filter([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description,
            'parent_id' => $dto->parentId != 'none' ? $dto->parentId : null,
            'active' => $dto->active,
        ], fn($value) => $value !== null);

        $this->categoryRepository->update($category, $data);

        return $category->fresh();
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            return false;
        }

        return $this->categoryRepository->delete($category);
    }
}