<?php

namespace App\DTOs\StockMovement;

class CreateStockMovementDTO
{
    public function __construct(
        public int $productId,
        public string $type,
        public int $quantity,
        public ?string $reason = null,
        public ?string $referenceType = null,
        public ?int $referenceId = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['product_id'],
            $data['type'],
            $data['quantity'],
            $data['reason'] ?? null,
            $data['reference_type'] ?? null,
            $data['reference_id'] ?? null
        );
    }
}