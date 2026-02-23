<?php

namespace App\Services;

use App\DTOs\Product\CreateProductDTO;
use App\DTOs\Product\UpdateProductDTO;
use App\DTOs\Product\ProductFilterDTO;
use App\Events\ProductCreated;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getAllProducts(): Collection
    {
        return $this->productRepository->findAll();
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function getProductBySlug(string $slug): ?Product
    {
        return $this->productRepository->findBySlug($slug);
    }

    public function getActiveProducts(): Collection
    {
        return $this->productRepository->findActive();
    }

    public function getProductsByCategory(int $categoryId): Collection
    {
        return $this->productRepository->findByCategory($categoryId);
    }

    public function getProductsWithFilters(ProductFilterDTO $filters): LengthAwarePaginator
    {
        return $this->productRepository->findWithFilters($filters);
    }

    public function createProduct(CreateProductDTO $dto): Product
    {
        $data = [
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description,
            'price' => $dto->price,
            'cost_price' => $dto->costPrice,
            'quantity' => $dto->quantity,
            'min_quantity' => $dto->minQuantity,
            'category_id' => $dto->categoryId,
            'active' => $dto->active,
        ];

        if ($dto->image) {
            $filename = time() . '_' . str_replace(' ', '_', $dto->image->getClientOriginalName());
            $path = storage_path('app/public/products/' . $filename);
            
            if (!file_exists(storage_path('app/public/products'))) {
                mkdir(storage_path('app/public/products'), 0755, true);
            }
            
            move_uploaded_file($dto->image->getRealPath(), $path);
            $data['image'] = 'products/' . $filename;
        }

        $product = $this->productRepository->create($data);

        if (!empty($dto->tagIds)) {
            $product->tags()->sync($dto->tagIds);
        }

        // Disparar evento ProductCreated
        ProductCreated::dispatch($product);

        return $product;
    }

    public function updateProduct(int $id, UpdateProductDTO $dto): ?Product
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product) {
            return null;
        }

        $data = array_filter([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description,
            'price' => $dto->price,
            'cost_price' => $dto->costPrice,
            'quantity' => $dto->quantity,
            'min_quantity' => $dto->minQuantity,
            'category_id' => $dto->categoryId,
            'active' => $dto->active,
        ], fn($value) => $value !== null);

        if ($dto->image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $filename = time() . '_' . str_replace(' ', '_', $dto->image->getClientOriginalName());
            $path = storage_path('app/public/products/' . $filename);
            
            if (!file_exists(storage_path('app/public/products'))) {
                mkdir(storage_path('app/public/products'), 0755, true);
            }
            
            move_uploaded_file($dto->image->getRealPath(), $path);
            $data['image'] = 'products/' . $filename;
        }
        
        $this->productRepository->update($product, $data);

        if ($dto->tagIds !== null) {
            $product->tags()->sync($dto->tagIds);
        }

        return $product->fresh();
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product) {
            return false;
        }

        // Deletar imagem ao excluir produto
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return $this->productRepository->delete($product);
    }
}