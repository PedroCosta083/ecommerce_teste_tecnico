<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TagRepository implements TagRepositoryInterface
{
    public function findAll(): Collection
    {
        return Tag::all();
    }

    public function findById(int $id): ?Tag
    {
        return Tag::with(['products'])->find($id);
    }

    public function findBySlug(string $slug): ?Tag
    {
        return Tag::with(['products'])->where('slug', $slug)->first();
    }

    public function findByIds(array $ids): Collection
    {
        return Tag::whereIn('id', $ids)->get();
    }

    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data): bool
    {
        return $tag->update($data);
    }

    public function delete(Tag $tag): bool
    {
        return $tag->delete();
    }
}