<?php

namespace App\DTOs\Product;

use Illuminate\Http\UploadedFile;

class CreateProductDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public ?UploadedFile $image,
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
        $image = null;
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $image = $data['image'];
        }
        
        return new self(
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $image,
            (float) $data['price'],
            (float) $data['cost_price'],
            (int) $data['quantity'],
            (int) $data['min_quantity'],
            (int) $data['category_id'],
            isset($data['active']) ? (bool) $data['active'] : true,
            $data['tag_ids'] ?? []
        );
    }
}