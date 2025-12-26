<?php

namespace App\DTOs\Product;

class CreateProductDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public float $price,
        public float $costPrice,
        public int $quantity,
        public int $minQuantity,
        public int $categoryId,
        public bool $active = true,
        public array $tagIds = []
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $data['price'],
            $data['cost_price'],
            $data['quantity'],
            $data['min_quantity'],
            $data['category_id'],
            $data['active'] ?? true,
            $data['tag_ids'] ?? []
        );
    }
}