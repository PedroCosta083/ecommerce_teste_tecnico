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

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            return $this->error('Order not found', 404);
        }

        return $this->success(new OrderResource($order));
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $dto = CreateOrderDTO::fromRequest($request->validated());
        $order = $this->orderService->createOrder($dto);

        return $this->success(new OrderResource($order), 'Order created successfully', 201);
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        $dto = UpdateOrderDTO::fromRequest($request->validated());
        $order = $this->orderService->updateOrder($id, $dto);

        if (!$order) {
            return $this->error('Order not found', 404);
        }

        return $this->success(new OrderResource($order), 'Order updated successfully');
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:pendente,processando,enviado,entregue,cancelado']);
        
        $order = $this->orderService->updateOrderStatus($id, $request->status);

        if (!$order) {
            return $this->error('Order not found', 404);
        }

        return $this->success(new OrderResource($order), 'Order status updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->orderService->deleteOrder($id);

        if (!$deleted) {
            return $this->error('Order not found', 404);
        }

        return $this->success(null, 'Order deleted successfully');
    }
}