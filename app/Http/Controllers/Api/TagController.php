<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Tag\CreateTagDTO;
use App\DTOs\Tag\UpdateTagDTO;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;

class TagController extends ApiController
{
    public function __construct(
        private TagService $tagService
    ) {}

    public function index(): JsonResponse
    {
        $tags = $this->tagService->getAllTags();
        return $this->success(TagResource::collection($tags));
    }

    public function show(int $id): JsonResponse
    {
        $tag = $this->tagService->getTagById($id);

        if (!$tag) {
            return $this->error('Tag not found', 404);
        }

        return $this->success(new TagResource($tag));
    }

    public function store(CreateTagRequest $request): JsonResponse
    {
        $dto = CreateTagDTO::fromRequest($request->validated());
        $tag = $this->tagService->createTag($dto);

        return $this->success(new TagResource($tag), 'Tag created successfully', 201);
    }

    public function update(UpdateTagRequest $request, int $id): JsonResponse
    {
        $dto = UpdateTagDTO::fromRequest($request->validated());
        $tag = $this->tagService->updateTag($id, $dto);

        if (!$tag) {
            return $this->error('Tag not found', 404);
        }

        return $this->success(new TagResource($tag), 'Tag updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->tagService->deleteTag($id);

        if (!$deleted) {
            return $this->error('Tag not found', 404);
        }

        return $this->success(null, 'Tag deleted successfully');
    }
}