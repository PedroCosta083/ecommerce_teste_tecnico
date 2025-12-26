<?php

namespace App\DTOs\Order;

class UpdateOrderDTO
{
    public function __construct(
        public ?string $status = null,
        public ?array $shippingAddress = null,
        public ?array $billingAddress = null,
        public ?string $notes = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['status'] ?? null,
            $data['shipping_address'] ?? null,
            $data['billing_address'] ?? null,
            $data['notes'] ?? null
        );
    }
}