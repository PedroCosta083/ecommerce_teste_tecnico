<?php

namespace App\DTOs\Cart;

class AddToCartDTO
{
    public function __construct(
        public int $productId,
        public int $quantity,
        public ?int $userId = null,
        public ?string $sessionId = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['product_id'],
            $data['quantity'],
            $data['user_id'] ?? null,
            $data['session_id'] ?? null
        );
    }
}