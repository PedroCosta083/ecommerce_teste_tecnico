<?php

namespace App\Repositories\Contracts;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

interface TagRepositoryInterface
{
    public function findAll(): Collection;
    public function findById(int $id): ?Tag;
    public function findBySlug(string $slug): ?Tag;
    public function findByIds(array $ids): Collection;
    public function create(array $data): Tag;
    public function update(Tag $tag, array $data): bool;
    public function delete(Tag $tag): bool;
}