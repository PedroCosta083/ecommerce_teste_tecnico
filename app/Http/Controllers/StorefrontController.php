<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\CategoryService;
use App\DTOs\Product\ProductFilterDTO;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StorefrontController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private CategoryService $categoryService
    ) {}

    public function index(Request $request): Response
    {
        $filters = ProductFilterDTO::fromRequest(array_merge(
            $request->all(),
            ['active' => true] // Apenas produtos ativos na vitrine
        ));
        $products = $this->productService->getProductsWithFilters($filters);
        
        // Filtrar produtos com estoque
        $products->getCollection()->load(['category', 'tags']);
        $filteredProducts = $products->getCollection()->filter(fn($product) => $product->quantity > 0);
        
        return Inertia::render('storefront/index', [
            'products' => [
                'data' => ProductResource::collection($filteredProducts)->resolve(),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $filteredProducts->count(),
                ]
            ],
            'categories' => $this->categoryService->getAllCategories(),
            'filters' => $filters,
        ]);
    }

    public function show(int $id): Response
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product) {
            abort(404);
        }

        return Inertia::render('storefront/product', [
            'product' => (new ProductResource($product->load(['category', 'tags'])))->resolve(),
        ]);
    }
}
