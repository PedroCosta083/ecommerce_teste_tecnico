<?php

namespace App\DTOs\Category;

class UpdateCategoryDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?int $parentId = null,
        public ?bool $active = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'] ?? null,
            $data['slug'] ?? null,
            $data['description'] ?? null,
            $data['parent_id'] ?? null,
            $data['active'] ?? null
        );
    }
}