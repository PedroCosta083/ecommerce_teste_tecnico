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

    public function index(Request $request): JsonResponse
    {
        $filters = ProductFilterDTO::fromRequest($request->all());
        $products = $this->productService->getProductsWithFilters($filters);
        return $this->paginatedResponse($products, ProductResource::class);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        return $this->showResource($product, ProductResource::class, 'Product not found');
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $dto = CreateProductDTO::fromRequest($request->validated());
        $product = $this->productService->createProduct($dto);
        return $this->storeResource($product, ProductResource::class, 'Product created successfully');
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $dto = UpdateProductDTO::fromRequest($request->validated());
        $product = $this->productService->updateProduct($id, $dto);
        return $this->updateResource($product, ProductResource::class, 'Product updated successfully', 'Product not found');
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->productService->deleteProduct($id);
        return $this->destroyResource($deleted, 'Product deleted successfully', 'Product not found');
    }
}
