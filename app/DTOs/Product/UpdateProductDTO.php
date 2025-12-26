<?php

namespace App\DTOs\Product;

class UpdateProductDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?float $price = null,
        public ?float $costPrice = null,
        public ?int $quantity = null,
        public ?int $minQuantity = null,
        public ?int $categoryId = null,
        public ?bool $active = null,
        public ?array $tagIds = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'] ?? null,
            $data['slug'] ?? null,
            $data['description'] ?? null,
            $data['price'] ?? null,
            $data['cost_price'] ?? null,
            $data['quantity'] ?? null,
            $data['min_quantity'] ?? null,
            $data['category_id'] ?? null,
            $data['active'] ?? null,
            $data['tag_ids'] ?? null
        );
    }
}