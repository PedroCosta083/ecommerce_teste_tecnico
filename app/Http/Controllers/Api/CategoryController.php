<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Category\{CreateCategoryDTO, UpdateCategoryDTO};
use App\Http\Requests\Category\{CreateCategoryRequest, UpdateCategoryRequest};
use App\Http\Resources\{CategoryResource, ProductResource};
use App\Services\CategoryService;
use App\Http\Controllers\Traits\HasCrudResponses;
use Illuminate\Http\JsonResponse;

class CategoryController extends ApiController
{
    use HasCrudResponses;

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
        return $this->showResource($category, CategoryResource::class, 'Category not found');
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $dto = CreateCategoryDTO::fromRequest($request->validated());
        $category = $this->categoryService->createCategory($dto);
        return $this->storeResource($category, CategoryResource::class, 'Category created successfully');
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        if ($request->parent_id === 'none') {
            $request->merge(['parent_id' => null]);
        }

        $dto = UpdateCategoryDTO::fromRequest($request->validated());
        $category = $this->categoryService->updateCategory($id, $dto);
        return $this->updateResource($category, CategoryResource::class, 'Category updated successfully', 'Category not found');
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->categoryService->deleteCategory($id);
        return $this->destroyResource($deleted, 'Category deleted successfully', 'Category not found');
    }

    public function products(int $category): JsonResponse
    {
        $products = $this->categoryService->getCategoryProducts($category);
        
        if ($products === null) {
            return $this->error('Category not found', 404);
        }

        return $this->paginatedResponse($products, ProductResource::class);
    }
}
