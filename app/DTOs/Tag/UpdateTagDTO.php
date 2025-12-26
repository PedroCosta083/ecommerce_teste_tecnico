<?php

namespace App\DTOs\Tag;

class UpdateTagDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'] ?? null,
            $data['slug'] ?? null
        );
    }
}