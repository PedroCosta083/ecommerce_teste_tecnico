<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TagRepository implements TagRepositoryInterface
{
    public function findAll(): Collection
    {
        return Cache::tags(['tags'])->remember('tags.all', 3600, function () {
            return Tag::all();
        });
    }

    public function findById(int $id): ?Tag
    {
        return Cache::tags(['tags'])->remember("tags.{$id}", 3600, function () use ($id) {
            return Tag::with(['products'])->find($id);
        });
    }

    public function findBySlug(string $slug): ?Tag
    {
        return Cache::tags(['tags'])->remember("tags.slug.{$slug}", 3600, function () use ($slug) {
            return Tag::with(['products'])->where('slug', $slug)->first();
        });
    }

    public function findByIds(array $ids): Collection
    {
        return Tag::whereIn('id', $ids)->get();
    }

    public function create(array $data): Tag
    {
        $tag = Tag::create($data);
        $this->clearCache();
        return $tag;
    }

    public function update(Tag $tag, array $data): bool
    {
        $result = $tag->update($data);
        $this->clearCache();
        return $result;
    }

    public function delete(Tag $tag): bool
    {
        $result = $tag->delete();
        $this->clearCache();
        return $result;
    }

    private function clearCache(): void
    {
        Cache::tags(['tags'])->flush();
    }
}