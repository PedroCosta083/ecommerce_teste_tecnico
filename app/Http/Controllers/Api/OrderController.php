<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Order\CreateOrderDTO;
use App\DTOs\Order\UpdateOrderDTO;
use App\DTOs\Order\OrderFilterDTO;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Lista pedidos com filtros",
     *     description="Retorna lista paginada de pedidos com filtros por status, usuário e período",
     *     operationId="getOrders",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filtrar por ID do usuário",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por status do pedido",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"}, example="processing")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Data inicial (formato: Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Data final (formato: Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pedidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=42)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = OrderFilterDTO::fromRequest($request->all());
        $orders = $this->orderService->getOrdersWithFilters($filters);

        return $this->success([
            'data' => OrderResource::collection($orders->items()),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     tags={"Orders"},
     *     summary="Obtém detalhes de um pedido",
     *     description="Retorna informações completas de um pedido incluindo itens, totais e endereços",
     *     operationId="getOrder",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Pedido não encontrado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            return $this->error('Order not found', 404);
        }

        return $this->success(new OrderResource($order));
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Cria novo pedido",
     *     description="Cria um pedido validando estoque, calculando totais e disparando eventos (OrderCreated). Inicia processamento assíncrono via Jobs",
     *     operationId="createOrder",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do pedido",
     *         @OA\JsonContent(
     *             required={"items", "shipping_address", "billing_address"},
     *             @OA\Property(property="user_id", type="integer", example=1, description="ID do usuário (preenchido automaticamente se autenticado)"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 description="Lista de itens do pedido",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"product_id", "quantity"},
     *                     @OA\Property(property="product_id", type="integer", example=5),
     *                     @OA\Property(property="quantity", type="integer", example=2),
     *                     @OA\Property(property="price", type="number", format="float", example=1299.90, description="Preço unitário (opcional, usa preço atual se omitido)")
     *                 )
     *             ),
     *             @OA\Property(property="shipping_address", type="string", example="Rua ABC, 123, São Paulo, SP, 01234-567", description="Endereço de entrega completo"),
     *             @OA\Property(property="billing_address", type="string", example="Rua ABC, 123, São Paulo, SP, 01234-567", description="Endereço de cobrança"),
     *             @OA\Property(property="notes", type="string", example="Entregar no período da manhã", description="Observações adicionais")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pedido criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erro de validação ou estoque insuficiente"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        $dto = CreateOrderDTO::fromRequest($request->validated());
        $order = $this->orderService->createOrder($dto);

        return $this->success(new OrderResource($order), 'Order created successfully', 201);
    }

    /**
     * @OA\Put(
     *     path="/orders/{id}",
     *     tags={"Orders"},
     *     summary="Atualiza pedido existente",
     *     description="Atualiza dados de um pedido. Campos opcionais",
     *     operationId="updateOrder",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados para atualização",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"}, example="shipped"),
     *             @OA\Property(property="shipping_address", type="string", example="Novo endereço"),
     *             @OA\Property(property="billing_address", type="string", example="Novo endereço de cobrança"),
     *             @OA\Property(property="notes", type="string", example="Observações atualizadas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Pedido não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        $dto = UpdateOrderDTO::fromRequest($request->validated());
        $order = $this->orderService->updateOrder($id, $dto);

        if (!$order) {
            return $this->error('Order not found', 404);
        }

        return $this->success(new OrderResource($order), 'Order updated successfully');
    }

    /**
     * @OA\Patch(
     *     path="/orders/{id}/status",
     *     tags={"Orders"},
     *     summary="Atualiza status do pedido",
     *     description="Endpoint específico para atualizar apenas o status de um pedido",
     *     operationId="updateOrderStatus",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Novo status",
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"pendente", "processando", "enviado", "entregue", "cancelado"},
     *                 example="enviado",
     *                 description="Novo status do pedido"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order status updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Pedido não encontrado"),
     *     @OA\Response(response=422, description="Status inválido"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:pendente,processando,enviado,entregue,cancelado']);
        
        $order = $this->orderService->updateOrderStatus($id, $request->status);

        if (!$order) {
            return $this->error('Order not found', 404);
        }

        return $this->success(new OrderResource($order), 'Order status updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/orders/{id}",
     *     tags={"Orders"},
     *     summary="Remove pedido",
     *     description="Exclui permanentemente um pedido do sistema",
     *     operationId="deleteOrder",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Pedido não encontrado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->orderService->deleteOrder($id);

        if (!$deleted) {
            return $this->error('Order not found', 404);
        }

        return $this->success(null, 'Order deleted successfully');
    }
}