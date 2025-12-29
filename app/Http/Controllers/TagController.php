<?php

namespace App\Http\Controllers;

use App\DTOs\Tag\CreateTagDTO;
use App\DTOs\Tag\UpdateTagDTO;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function __construct(
        private TagService $tagService
    ) {}

    public function index(): JsonResponse
    {
        $tags = $this->tagService->getAllTags();
        return response()->json(TagResource::collection($tags));
    }

    public function show(int $id): JsonResponse
    {
        $tag = $this->tagService->getTagById($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        return response()->json(new TagResource($tag));
    }

    public function store(CreateTagRequest $request): JsonResponse
    {
        $dto = CreateTagDTO::fromRequest($request->validated());
        $tag = $this->tagService->createTag($dto);

        return response()->json(new TagResource($tag), 201);
    }

    public function update(UpdateTagRequest $request, int $id): JsonResponse
    {
        $dto = UpdateTagDTO::fromRequest($request->validated());
        $tag = $this->tagService->updateTag($id, $dto);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        return response()->json(new TagResource($tag));
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->tagService->deleteTag($id);

        if (!$deleted) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        return response()->json(['message' => 'Tag deleted successfully']);
    }
}