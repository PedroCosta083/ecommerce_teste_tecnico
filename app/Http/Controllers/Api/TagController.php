<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Tag\{CreateTagDTO, UpdateTagDTO};
use App\Http\Requests\Tag\{CreateTagRequest, UpdateTagRequest};
use App\Http\Resources\TagResource;
use App\Services\TagService;
use App\Http\Controllers\Traits\HasCrudResponses;
use Illuminate\Http\JsonResponse;

class TagController extends ApiController
{
    use HasCrudResponses;

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
        return $this->showResource($tag, TagResource::class, 'Tag not found');
    }

    public function store(CreateTagRequest $request): JsonResponse
    {
        $dto = CreateTagDTO::fromRequest($request->validated());
        $tag = $this->tagService->createTag($dto);
        return $this->storeResource($tag, TagResource::class, 'Tag created successfully');
    }

    public function update(UpdateTagRequest $request, int $id): JsonResponse
    {
        $dto = UpdateTagDTO::fromRequest($request->validated());
        $tag = $this->tagService->updateTag($id, $dto);
        return $this->updateResource($tag, TagResource::class, 'Tag updated successfully', 'Tag not found');
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->tagService->deleteTag($id);
        return $this->destroyResource($deleted, 'Tag deleted successfully', 'Tag not found');
    }
}
