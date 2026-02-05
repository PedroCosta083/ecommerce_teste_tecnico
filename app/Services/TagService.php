<?php

namespace App\Services;

use App\Models\Tag;
use App\DTOs\Tag\{CreateTagDTO, UpdateTagDTO};
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    public function getAllTags(): Collection
    {
        return Tag::orderBy('name')->get();
    }

    public function getTagById(int $id): ?Tag
    {
        return Tag::find($id);
    }

    public function createTag(CreateTagDTO $dto): Tag
    {
        return Tag::create([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'color' => $dto->color,
            'active' => $dto->active,
        ]);
    }

    public function updateTag(int $id, UpdateTagDTO $dto): ?Tag
    {
        $tag = $this->getTagById($id);
        
        if (!$tag) {
            return null;
        }

        $updateData = array_filter([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'color' => $dto->color,
            'active' => $dto->active,
        ], fn($value) => $value !== null);

        $tag->update($updateData);
        
        return $tag->fresh();
    }

    public function deleteTag(int $id): bool
    {
        $tag = $this->getTagById($id);
        
        if (!$tag) {
            return false;
        }

        return $tag->delete();
    }
}