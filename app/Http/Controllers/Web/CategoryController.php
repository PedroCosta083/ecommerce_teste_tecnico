<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use App\DTOs\Category\{CreateCategoryDTO, UpdateCategoryDTO};
use App\Http\Requests\Category\{CreateCategoryRequest, UpdateCategoryRequest};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(): Response
    {
        $categories = $this->categoryService->getAllCategories();
        
        return Inertia::render('categories/index', [
            'categories' => $categories->load(['parent', 'children']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('categories/create', [
            'categories' => $this->categoryService->getAllCategories(),
        ]);
    }

    public function store(CreateCategoryRequest $request)
    {
        $dto = CreateCategoryDTO::fromRequest($request->validated());
        $this->categoryService->createCategory($dto);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function show(Category $category): Response
    {
        return Inertia::render('categories/show', [
            'category' => $category->load(['parent', 'children', 'products']),
        ]);
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('categories/edit', [
            'category' => $category->load(['parent']),
            'categories' => $this->categoryService->getAllCategories()->where('id', '!=', $category->id)->values(),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $dto = UpdateCategoryDTO::fromRequest($request->validated());
        $this->categoryService->updateCategory($category->id, $dto);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $this->categoryService->deleteCategory($category->id);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}