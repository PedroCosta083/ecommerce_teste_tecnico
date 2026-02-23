<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\TagService;
use App\DTOs\Product\{CreateProductDTO, UpdateProductDTO, ProductFilterDTO};
use App\Http\Requests\Product\{CreateProductRequest, UpdateProductRequest};
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Resources\ProductResource;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private CategoryService $categoryService,
        private TagService $tagService
    ) {
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Request $request): Response
    {
        $filters = ProductFilterDTO::fromRequest($request->all());
        $products = $this->productService->getProductsWithFilters($filters);
        
        // Carregar relacionamentos para cada produto
        $products->getCollection()->load(['category', 'tags']);
        
        return Inertia::render('products/index', [
            'products' => [
                'data' => ProductResource::collection($products->items())->resolve(),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ],
            'filters' => $filters,
            'categories' => $this->categoryService->getAllCategories(),
            'tags' => $this->tagService->getAllTags(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('products/create', [
            'categories' => $this->categoryService->getAllCategories(),
            'tags' => $this->tagService->getAllTags(),
        ]);
    }

    public function store(CreateProductRequest $request)
    {
        $validated = $request->validated();

        // Adicionar arquivo manualmente se existir
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image');
        }
        
        $dto = CreateProductDTO::fromRequest($validated);
        $this->productService->createProduct($dto);

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function show(Product $product): Response
    {
        return Inertia::render('products/show', [
            'product' => (new ProductResource($product->load(['category', 'tags'])))->resolve(),
        ]);
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('products/edit', [
            'product' => (new ProductResource($product->load(['category', 'tags'])))->resolve(),
            'categories' => $this->categoryService->getAllCategories(),
            'tags' => $this->tagService->getAllTags(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        
        // Adicionar arquivo manualmente se existir
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image');
        }
        
        $dto = UpdateProductDTO::fromRequest($validated);
        $this->productService->updateProduct($product->id, $dto);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product->id);

        return redirect()->route('products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }
}