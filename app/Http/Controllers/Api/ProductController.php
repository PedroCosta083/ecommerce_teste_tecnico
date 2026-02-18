<?php

namespace App\Http\Controllers\Api;

use App\Services\ProductService;
use App\DTOs\Product\{CreateProductDTO, UpdateProductDTO, ProductFilterDTO};
use App\Http\Requests\Product\{CreateProductRequest, UpdateProductRequest};
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Traits\HasCrudResponses;
use Illuminate\Http\{JsonResponse, Request};

class ProductController extends ApiController
{
    use HasCrudResponses;

    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * @OA\Get(
     *     path="/products",
     *     tags={"Products"},
     *     summary="Lista produtos com filtros e paginação",
     *     description="Retorna lista paginada de produtos com opções de busca por termo, categoria e ordenação",
     *     operationId="getProducts",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Termo de busca (nome ou descrição)",
     *         required=false,
     *         @OA\Schema(type="string", example="notebook")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="ID da categoria para filtrar",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Preço mínimo",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=100.00)
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Preço máximo",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=5000.00)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Campo para ordenação",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "price", "created_at"}, example="price")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Direção da ordenação",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=73)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = ProductFilterDTO::fromRequest($request->all());
        $products = $this->productService->getProductsWithFilters($filters);
        return $this->paginatedResponse($products, ProductResource::class);
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Obtém detalhes de um produto",
     *     description="Retorna informações completas de um produto específico incluindo categoria e estoque",
     *     operationId="getProduct",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do produto",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        return $this->showResource($product, ProductResource::class, 'Product not found');
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     tags={"Products"},
     *     summary="Cria novo produto",
     *     description="Cadastra um novo produto no sistema com validação completa e dispara evento ProductCreated",
     *     operationId="createProduct",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do produto",
     *         @OA\JsonContent(
     *             required={"name", "price", "quantity", "category_id"},
     *             @OA\Property(property="name", type="string", example="Notebook Dell Inspiron", description="Nome do produto"),
     *             @OA\Property(property="slug", type="string", example="notebook-dell-inspiron", description="Slug único (gerado automaticamente se omitido)"),
     *             @OA\Property(property="description", type="string", example="Notebook com processador Intel i7, 16GB RAM", description="Descrição detalhada"),
     *             @OA\Property(property="price", type="number", format="float", example=3499.90, description="Preço de venda"),
     *             @OA\Property(property="cost_price", type="number", format="float", example=2800.00, description="Preço de custo"),
     *             @OA\Property(property="quantity", type="integer", example=50, description="Quantidade em estoque"),
     *             @OA\Property(property="min_quantity", type="integer", example=10, description="Estoque mínimo (alerta)"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="ID da categoria"),
     *             @OA\Property(property="active", type="boolean", example=true, description="Produto ativo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        $dto = CreateProductDTO::fromRequest($request->validated());
        $product = $this->productService->createProduct($dto);
        return $this->storeResource($product, ProductResource::class, 'Product created successfully');
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Atualiza produto existente",
     *     description="Atualiza dados de um produto. Todos os campos são opcionais",
     *     operationId="updateProduct",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do produto",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados para atualização (campos opcionais)",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Notebook Dell Inspiron 15"),
     *             @OA\Property(property="slug", type="string", example="notebook-dell-inspiron-15"),
     *             @OA\Property(property="description", type="string", example="Descrição atualizada"),
     *             @OA\Property(property="price", type="number", format="float", example=3299.90),
     *             @OA\Property(property="cost_price", type="number", format="float", example=2600.00),
     *             @OA\Property(property="quantity", type="integer", example=45),
     *             @OA\Property(property="min_quantity", type="integer", example=8),
     *             @OA\Property(property="category_id", type="integer", example=2),
     *             @OA\Property(property="active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $dto = UpdateProductDTO::fromRequest($request->validated());
        $product = $this->productService->updateProduct($id, $dto);
        return $this->updateResource($product, ProductResource::class, 'Product updated successfully', 'Product not found');
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Remove produto",
     *     description="Exclui permanentemente um produto do sistema",
     *     operationId="deleteProduct",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do produto",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->productService->deleteProduct($id);
        return $this->destroyResource($deleted, 'Product deleted successfully', 'Product not found');
    }
}
