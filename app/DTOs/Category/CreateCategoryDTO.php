<?php

namespace App\DTOs\Category;

class CreateCategoryDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public ?int $parentId,
        public bool $active = true
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            isset($data['parent_id']) && $data['parent_id'] !== 'none' ? (int)$data['parent_id'] : null,
            $data['active'] ?? true
        );
    }
}