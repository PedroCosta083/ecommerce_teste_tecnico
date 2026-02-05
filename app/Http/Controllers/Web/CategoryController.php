<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
    ) {}

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

    public function show(int $id): Response
    {
        $category = $this->categoryService->getCategoryById($id);
        
        if (!$category) {
            abort(404);
        }

        return Inertia::render('categories/show', [
            'category' => $category->load(['parent', 'children', 'products']),
        ]);
    }

    public function edit(int $id): Response
    {
        $category = $this->categoryService->getCategoryById($id);
        
        if (!$category) {
            abort(404);
        }

        return Inertia::render('categories/edit', [
            'category' => $category->load(['parent']),
            'categories' => $this->categoryService->getAllCategories()->where('id', '!=', $id)->values(),
        ]);
    }

    public function update(UpdateCategoryRequest $request, int $id)
    {
        $dto = UpdateCategoryDTO::fromRequest($request->validated());
        $category = $this->categoryService->updateCategory($id, $dto);

        if (!$category) {
            abort(404);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(int $id)
    {
        $deleted = $this->categoryService->deleteCategory($id);

        if (!$deleted) {
            abort(404);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}