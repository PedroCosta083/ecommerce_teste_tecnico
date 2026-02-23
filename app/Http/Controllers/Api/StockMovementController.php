<?php

namespace App\Http\Controllers\Api;

use App\DTOs\StockMovement\CreateStockMovementDTO;
use App\Http\Requests\StockMovement\CreateStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Services\StockMovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockMovementController extends ApiController
{
    public function __construct(
        private StockMovementService $stockMovementService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/stock-movements",
     *     tags={"Stock"},
     *     summary="Lista movimentações de estoque",
     *     description="Retorna lista de movimentações com filtros por produto, tipo ou movimentações recentes",
     *     operationId="getStockMovements",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Filtrar por ID do produto",
     *         required=false,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filtrar por tipo de movimentação",
     *         required=false,
     *         @OA\Schema(type="string", enum={"entrada", "saida", "ajuste", "venda", "devolucao"}, example="venda")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limite de registros (padrão: 50)",
     *         required=false,
     *         @OA\Schema(type="integer", example=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de movimentações",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/StockMovement"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $productId = $request->query('product_id');
        $type = $request->query('type');
        $limit = $request->query('limit', 50);

        if ($productId) {
            $movements = $this->stockMovementService->getMovementsByProduct($productId);
        } elseif ($type) {
            $movements = $this->stockMovementService->getMovementsByType($type);
        } else {
            $movements = $this->stockMovementService->getRecentMovements($limit);
        }

        return $this->success(StockMovementResource::collection($movements));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/stock-movements",
     *     tags={"Stock"},
     *     summary="Cria movimentação de estoque",
     *     description="Registra uma nova movimentação de estoque (entrada, saída, ajuste, venda, devolução) e atualiza quantidade do produto",
     *     operationId="createStockMovement",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados da movimentação",
     *         @OA\JsonContent(
     *             required={"product_id", "type", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=5, description="ID do produto"),
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 enum={"entrada", "saida", "ajuste", "venda", "devolucao"},
     *                 example="entrada",
     *                 description="Tipo de movimentação"
     *             ),
     *             @OA\Property(property="quantity", type="integer", example=10, description="Quantidade movimentada (positivo ou negativo)"),
     *             @OA\Property(property="reference_id", type="integer", example=1, description="ID de referência (ex: order_id)"),
     *             @OA\Property(property="notes", type="string", example="Entrada de estoque - Fornecedor XYZ", description="Observações")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movimentação criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Stock movement created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/StockMovement")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erro ao criar movimentação"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function store(CreateStockMovementRequest $request): JsonResponse
    {
        try {
            $dto = CreateStockMovementDTO::fromRequest($request->validated());
            $movement = $this->stockMovementService->createMovement($dto);

            return $this->success(new StockMovementResource($movement), 'Stock movement created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{productId}/stock-summary",
     *     tags={"Stock"},
     *     summary="Obtém resumo de estoque do produto",
     *     description="Retorna estatísticas e resumo das movimentações de estoque de um produto específico",
     *     operationId="getProductStockSummary",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         description="ID do produto",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resumo de estoque",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="product_id", type="integer", example=5),
     *                 @OA\Property(property="current_stock", type="integer", example=45),
     *                 @OA\Property(property="total_entries", type="integer", example=100),
     *                 @OA\Property(property="total_exits", type="integer", example=55),
     *                 @OA\Property(property="total_sales", type="integer", example=50),
     *                 @OA\Property(property="total_returns", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function summary(int $productId): JsonResponse
    {
        $summary = $this->stockMovementService->getProductStockSummary($productId);
        return $this->success($summary);
    }
}