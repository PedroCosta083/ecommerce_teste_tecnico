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

    /**
     * @OA\Get(
     *     path="/api/v1/tags",
     *     tags={"Tags"},
     *     summary="Lista todas as tags",
     *     description="Retorna lista completa de tags disponíveis no sistema",
     *     operationId="getTags",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tags",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Tag"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index(): JsonResponse
    {
        $tags = $this->tagService->getAllTags();
        return $this->success(TagResource::collection($tags));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tags/{id}",
     *     tags={"Tags"},
     *     summary="Obtém detalhes de uma tag",
     *     description="Retorna informações de uma tag específica",
     *     operationId="getTag",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tag",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Tag")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tag não encontrada"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $tag = $this->tagService->getTagById($id);
        return $this->showResource($tag, TagResource::class, 'Tag not found');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tags",
     *     tags={"Tags"},
     *     summary="Cria nova tag",
     *     description="Cadastra uma nova tag no sistema",
     *     operationId="createTag",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados da tag",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Promoção", description="Nome da tag"),
     *             @OA\Property(property="slug", type="string", example="promocao", description="Slug único (gerado automaticamente se omitido)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tag criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tag created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Tag")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function store(CreateTagRequest $request): JsonResponse
    {
        $dto = CreateTagDTO::fromRequest($request->validated());
        $tag = $this->tagService->createTag($dto);
        return $this->storeResource($tag, TagResource::class, 'Tag created successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/tags/{id}",
     *     tags={"Tags"},
     *     summary="Atualiza tag existente",
     *     description="Atualiza dados de uma tag. Todos os campos são opcionais",
     *     operationId="updateTag",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tag",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados para atualização",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Super Promoção"),
     *             @OA\Property(property="slug", type="string", example="super-promocao")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tag updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Tag")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tag não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function update(UpdateTagRequest $request, int $id): JsonResponse
    {
        $dto = UpdateTagDTO::fromRequest($request->validated());
        $tag = $this->tagService->updateTag($id, $dto);
        return $this->updateResource($tag, TagResource::class, 'Tag updated successfully', 'Tag not found');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/tags/{id}",
     *     tags={"Tags"},
     *     summary="Remove tag",
     *     description="Exclui permanentemente uma tag do sistema",
     *     operationId="deleteTag",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tag",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tag deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tag não encontrada"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->tagService->deleteTag($id);
        return $this->destroyResource($deleted, 'Tag deleted successfully', 'Tag not found');
    }
}
