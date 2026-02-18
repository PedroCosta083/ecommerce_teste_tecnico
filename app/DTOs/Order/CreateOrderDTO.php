<?php

namespace App\DTOs\Order;

class CreateOrderDTO
{
    public function __construct(
        public int $userId,
        public array $items,
        public string $shippingAddress,
        public string $billingAddress,
        public ?string $notes = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['user_id'],
            $data['items'],
            $data['shipping_address'],
            $data['billing_address'],
            $data['notes'] ?? null
        );
    }
}
