<?php

namespace App\DTOs\Tag;

class CreateTagDTO
{
    public function __construct(
        public string $name,
        public string $slug
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'],
            $data['slug']
        );
    }
}