<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MyOrdersController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(): Response
    {
        $orders = $this->orderService->getOrdersByUser(auth()->id());
        
        return Inertia::render('my-orders/index', [
            'orders' => OrderResource::collection($orders->load(['orderItems.product']))->resolve(),
        ]);
    }

    public function show(int $id): Response
    {
        $order = $this->orderService->getOrderById($id);
        
        if (!$order || $order->user_id !== auth()->id()) {
            abort(404);
        }

        return Inertia::render('my-orders/show', [
            'order' => (new OrderResource($order->load(['orderItems.product'])))->resolve(),
        ]);
    }
}
