<?php

namespace App\DTOs\Product;

class ProductFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $categoryId = null,
        public ?array $tagIds = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ?bool $active = null,
        public ?string $sortBy = 'name',
        public ?string $sortDirection = 'asc',
        public int $perPage = 15
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['search'] ?? null,
            $data['category_id'] ?? null,
            $data['tag_ids'] ?? null,
            $data['min_price'] ?? null,
            $data['max_price'] ?? null,
            $data['active'] ?? null,
            $data['sort_by'] ?? 'name',
            $data['sort_direction'] ?? 'asc',
            $data['per_page'] ?? 15
        );
    }
}