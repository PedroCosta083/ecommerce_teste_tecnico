<?php

namespace App\DTOs\Cart;

class UpdateCartItemDTO
{
    public function __construct(
        public int $quantity
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['quantity']
        );
    }
}