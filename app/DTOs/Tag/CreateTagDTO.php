<?php

namespace App\DTOs\Tag;

class CreateTagDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $color = '#3B82F6',
        public bool $active = true
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'],
            $data['slug'],
            $data['color'] ?? '#3B82F6',
            $data['active'] ?? true
        );
    }
}