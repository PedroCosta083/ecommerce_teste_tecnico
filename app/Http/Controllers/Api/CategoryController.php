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

    /**
     * @OA\Get(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Lista todas as categorias",
     *     description="Retorna lista completa de categorias com estrutura hierárquica (parent/children)",
     *     operationId="getCategories",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorias",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return $this->success(CategoryResource::collection($categories));
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Obtém detalhes de uma categoria",
     *     description="Retorna informações de uma categoria específica incluindo subcategorias",
     *     operationId="getCategory",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da categoria",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoria encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Categoria não encontrada"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        return $this->showResource($category, CategoryResource::class, 'Category not found');
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Cria nova categoria",
     *     description="Cadastra uma nova categoria com suporte a hierarquia (parent_id)",
     *     operationId="createCategory",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados da categoria",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Eletrônicos", description="Nome da categoria"),
     *             @OA\Property(property="slug", type="string", example="eletronicos", description="Slug único (gerado automaticamente se omitido)"),
     *             @OA\Property(property="description", type="string", example="Produtos eletrônicos e tecnologia", description="Descrição"),
     *             @OA\Property(property="parent_id", type="integer", example=null, description="ID da categoria pai (null para categoria raiz)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoria criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $dto = CreateCategoryDTO::fromRequest($request->validated());
        $category = $this->categoryService->createCategory($dto);
        return $this->storeResource($category, CategoryResource::class, 'Category created successfully');
    }

    /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Atualiza categoria existente",
     *     description="Atualiza dados de uma categoria. Todos os campos são opcionais",
     *     operationId="updateCategory",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da categoria",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados para atualização",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Eletrônicos e Informática"),
     *             @OA\Property(property="slug", type="string", example="eletronicos-informatica"),
     *             @OA\Property(property="description", type="string", example="Descrição atualizada"),
     *             @OA\Property(property="parent_id", type="integer", example=2, description="Use 'none' para remover parent")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoria atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Categoria não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        if ($request->parent_id === 'none') {
            $request->merge(['parent_id' => null]);
        }

        $dto = UpdateCategoryDTO::fromRequest($request->validated());
        $category = $this->categoryService->updateCategory($id, $dto);
        return $this->updateResource($category, CategoryResource::class, 'Category updated successfully', 'Category not found');
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Remove categoria",
     *     description="Exclui permanentemente uma categoria do sistema",
     *     operationId="deleteCategory",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da categoria",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoria removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Categoria não encontrada"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->categoryService->deleteCategory($id);
        return $this->destroyResource($deleted, 'Category deleted successfully', 'Category not found');
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}/products",
     *     tags={"Categories"},
     *     summary="Lista produtos de uma categoria",
     *     description="Retorna lista paginada de produtos pertencentes a uma categoria específica",
     *     operationId="getCategoryProducts",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da categoria",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos da categoria",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Categoria não encontrada"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function products(int $category): JsonResponse
    {
        $products = $this->categoryService->getCategoryProducts($category);
        
        if ($products === null) {
            return $this->error('Category not found', 404);
        }

        return $this->paginatedResponse($products, ProductResource::class);
    }
}
