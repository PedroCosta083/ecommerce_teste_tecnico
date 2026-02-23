<?php

namespace App\DTOs\Product;

use Illuminate\Http\UploadedFile;

class UpdateProductDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?UploadedFile $image = null,
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
        $image = null;
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $image = $data['image'];
        }
        
        return new self(
            $data['name'] ?? null,
            $data['slug'] ?? null,
            $data['description'] ?? null,
            $image,
            isset($data['price']) ? (float) $data['price'] : null,
            isset($data['cost_price']) ? (float) $data['cost_price'] : null,
            isset($data['quantity']) ? (int) $data['quantity'] : null,
            isset($data['min_quantity']) ? (int) $data['min_quantity'] : null,
            isset($data['category_id']) ? (int) $data['category_id'] : null,
            isset($data['active']) ? (bool) $data['active'] : null,
            $data['tag_ids'] ?? null
        );
    }
}