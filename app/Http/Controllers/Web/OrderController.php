<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
    ) {
        $this->authorizeResource(Order::class, 'order');
    }

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

    public function show(Order $order): Response
    {
        return Inertia::render('orders/show', [
            'order' => (new OrderResource($order->load(['user', 'orderItems.product'])))->resolve(),
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $dto = UpdateOrderDTO::fromRequest($request->validated());
        $this->orderService->updateOrder($order->id, $dto);

        return redirect()->route('orders.index')
            ->with('success', 'Pedido atualizado com sucesso!');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered'
        ]);

        $order->update(['status' => $request->status]);
        
        return redirect()->back()
            ->with('success', 'Status do pedido atualizado com sucesso!');
    }

    public function destroy(Order $order)
    {
        $this->orderService->deleteOrder($order->id);

        return redirect()->route('orders.index')
            ->with('success', 'Pedido excluído com sucesso!');
    }
}