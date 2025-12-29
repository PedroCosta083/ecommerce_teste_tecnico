<?php

namespace App\Services;

use App\DTOs\StockMovement\CreateStockMovementDTO;
use App\Models\StockMovement;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    public function __construct(
        private StockMovementRepositoryInterface $stockMovementRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getMovementsByProduct(int $productId): Collection
    {
        return $this->stockMovementRepository->findByProduct($productId);
    }

    public function getMovementsByType(string $type): Collection
    {
        return $this->stockMovementRepository->findByType($type);
    }

    public function getRecentMovements(int $limit = 50): Collection
    {
        return $this->stockMovementRepository->findRecent($limit);
    }

    public function createMovement(CreateStockMovementDTO $dto): StockMovement
    {
        return DB::transaction(function () use ($dto) {
            $product = $this->productRepository->findById($dto->productId);
            
            if (!$product) {
                throw new \Exception('Product not found');
            }

            // Criar movimentação
            $data = [
                'product_id' => $dto->productId,
                'type' => $dto->type,
                'quantity' => $dto->quantity,
                'reason' => $dto->reason,
                'reference_type' => $dto->referenceType,
                'reference_id' => $dto->referenceId,
            ];

            $movement = $this->stockMovementRepository->create($data);

            // Atualizar quantidade do produto
            $this->updateProductQuantity($product->id, $dto->type, $dto->quantity);

            return $movement;
        });
    }

    private function updateProductQuantity(int $productId, string $type, int $quantity): void
    {
        $product = $this->productRepository->findById($productId);
        
        $newQuantity = match($type) {
            'entrada', 'devolucao' => $product->quantity + $quantity,
            'saida', 'venda' => $product->quantity - $quantity,
            'ajuste' => $quantity, // Ajuste define a quantidade absoluta
            default => $product->quantity
        };

        $this->productRepository->update($product, ['quantity' => max(0, $newQuantity)]);
    }

    public function getProductStockSummary(int $productId): array
    {
        $entradas = $this->stockMovementRepository->getTotalByProductAndType($productId, 'entrada');
        $saidas = $this->stockMovementRepository->getTotalByProductAndType($productId, 'saida');
        $vendas = $this->stockMovementRepository->getTotalByProductAndType($productId, 'venda');
        $devolucoes = $this->stockMovementRepository->getTotalByProductAndType($productId, 'devolucao');

        return [
            'entradas' => $entradas,
            'saidas' => $saidas,
            'vendas' => $vendas,
            'devolucoes' => $devolucoes,
            'saldo' => $entradas + $devolucoes - $saidas - $vendas,
        ];
    }
}