<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\DTOs\Order\{CreateOrderDTO, UpdateOrderDTO, OrderFilterDTO};
use App\Http\Requests\Order\{CreateOrderRequest, UpdateOrderRequest};
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(Request $request): Response
    {
        $filters = OrderFilterDTO::fromRequest($request->all());
        $orders = $this->orderService->getOrdersWithFilters($filters);
        
        $orders->getCollection()->load(['user', 'orderItems.product']);
        
        return Inertia::render('orders/index', [
            'orders' => [
                'data' => OrderResource::collection($orders->items())->resolve(),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ]
            ],
            'filters' => $filters,
        ]);
    }

    public function show(int $id): Response
    {
        $order = $this->orderService->getOrderById($id);
        
        if (!$order) {
            abort(404);
        }

        return Inertia::render('orders/show', [
            'order' => (new OrderResource($order->load(['user', 'orderItems.product'])))->resolve(),
        ]);
    }

    public function update(UpdateOrderRequest $request, int $id)
    {
        $dto = UpdateOrderDTO::fromRequest($request->validated());
        $order = $this->orderService->updateOrder($id, $dto);

        if (!$order) {
            abort(404);
        }

        return redirect()->route('orders.index')
            ->with('success', 'Pedido atualizado com sucesso!');
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered'
        ]);

        $order = $this->orderService->getOrderById($id);
        
        if (!$order) {
            abort(404);
        }

        $order->update(['status' => $request->status]);
        
        return redirect()->back()
            ->with('success', 'Status do pedido atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $deleted = $this->orderService->deleteOrder($id);

        if (!$deleted) {
            abort(404);
        }

        return redirect()->route('orders.index')
            ->with('success', 'Pedido excluído com sucesso!');
    }
}