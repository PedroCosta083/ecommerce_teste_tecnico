<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\DTOs\Product\{CreateProductDTO, UpdateProductDTO, ProductFilterDTO};
use App\Http\Requests\Product\{CreateProductRequest, UpdateProductRequest};
use App\Http\Resources\ProductResource;
use Illuminate\Http\{JsonResponse, Request};

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = ProductFilterDTO::fromRequest($request->all());
        $products = $this->productService->getProductsWithFilters($filters);

        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(new ProductResource($product));
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $dto = CreateProductDTO::fromRequest($request->validated());
        $product = $this->productService->createProduct($dto);

        return response()->json(new ProductResource($product), 201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $dto = UpdateProductDTO::fromRequest($request->validated());
        $product = $this->productService->updateProduct($id, $dto);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(new ProductResource($product));
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->productService->deleteProduct($id);

        if (!$deleted) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }
}