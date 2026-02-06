<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Category\CreateCategoryDTO;
use App\DTOs\Category\UpdateCategoryDTO;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends ApiController
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return $this->success(CategoryResource::collection($categories));
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            return $this->error('Category not found', 404);
        }

        return $this->success(new CategoryResource($category));
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $dto = CreateCategoryDTO::fromRequest($request->validated());
        $category = $this->categoryService->createCategory($dto);

        return $this->success(new CategoryResource($category), 'Category created successfully', 201);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        if ($request->parent_id === 'none') {
            $request->merge(['parent_id' => null]);
        }

        $dto = UpdateCategoryDTO::fromRequest($request->validated());
        $category = $this->categoryService->updateCategory($id, $dto);

        if (!$category) {
            return $this->error('Category not found', 404);
        }

        return $this->success(new CategoryResource($category), 'Category updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->categoryService->deleteCategory($id);

        if (!$deleted) {
            return $this->error('Category not found', 404);
        }

        return $this->success(null, 'Category deleted successfully');
    }
}