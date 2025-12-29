<?php

namespace App\Services;

use App\DTOs\Tag\CreateTagDTO;
use App\DTOs\Tag\UpdateTagDTO;
use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    public function __construct(
        private TagRepositoryInterface $tagRepository
    ) {}

    public function getAllTags(): Collection
    {
        return $this->tagRepository->findAll();
    }

    public function getTagById(int $id): ?Tag
    {
        return $this->tagRepository->findById($id);
    }

    public function getTagBySlug(string $slug): ?Tag
    {
        return $this->tagRepository->findBySlug($slug);
    }

    public function getTagsByIds(array $ids): Collection
    {
        return $this->tagRepository->findByIds($ids);
    }

    public function createTag(CreateTagDTO $dto): Tag
    {
        $data = [
            'name' => $dto->name,
            'slug' => $dto->slug,
        ];

        return $this->tagRepository->create($data);
    }

    public function updateTag(int $id, UpdateTagDTO $dto): ?Tag
    {
        $tag = $this->tagRepository->findById($id);
        
        if (!$tag) {
            return null;
        }

        $data = array_filter([
            'name' => $dto->name,
            'slug' => $dto->slug,
        ], fn($value) => $value !== null);

        $this->tagRepository->update($tag, $data);

        return $tag->fresh();
    }

    public function deleteTag(int $id): bool
    {
        $tag = $this->tagRepository->findById($id);
        
        if (!$tag) {
            return false;
        }

        return $this->tagRepository->delete($tag);
    }
}