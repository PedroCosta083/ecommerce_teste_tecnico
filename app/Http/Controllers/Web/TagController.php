<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\TagService;
use App\DTOs\Tag\{CreateTagDTO, UpdateTagDTO};
use App\Http\Requests\Tag\{CreateTagRequest, UpdateTagRequest};
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function __construct(
        private TagService $tagService
    ) {
        $this->authorizeResource(Tag::class, 'tag');
    }

    public function index(): Response
    {
        $tags = $this->tagService->getAllTags();
        
        return Inertia::render('tags/index', [
            'tags' => $tags->toArray(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('tags/create');
    }

    public function store(CreateTagRequest $request)
    {
        $dto = CreateTagDTO::fromRequest($request->validated());
        $tag = $this->tagService->createTag($dto);

        return redirect()->route('tags.index')
            ->with('success', 'Tag criada com sucesso!');
    }

    public function show(Tag $tag): Response
    {
        return Inertia::render('tags/show', [
            'tag' => $tag->load('products'),
        ]);
    }

    public function edit(Tag $tag): Response
    {
        return Inertia::render('tags/edit', [
            'tag' => $tag,
        ]);
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $dto = UpdateTagDTO::fromRequest($request->validated());
        $this->tagService->updateTag($tag->id, $dto);

        return redirect()->route('tags.index')
            ->with('success', 'Tag atualizada com sucesso!');
    }

    public function destroy(Tag $tag)
    {
        $this->tagService->deleteTag($tag->id);

        return redirect()->route('tags.index')
            ->with('success', 'Tag excluída com sucesso!');
    }
}