<?php

namespace App\Services;

use App\DTOs\Order\CreateOrderDTO;
use App\DTOs\Order\UpdateOrderDTO;
use App\DTOs\Order\OrderFilterDTO;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderItemRepositoryInterface $orderItemRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getAllOrders(): Collection
    {
        return $this->orderRepository->findAll();
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function getOrdersByUser(int $userId): Collection
    {
        return $this->orderRepository->findByUser($userId);
    }

    public function getOrdersWithFilters(OrderFilterDTO $filters): LengthAwarePaginator
    {
        return $this->orderRepository->findWithFilters($filters);
    }

    public function createOrder(CreateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {
            $subtotal = 0;
            $orderItems = [];

            // Calcular subtotal e preparar itens
            foreach ($dto->items as $item) {
                $product = $this->productRepository->findById($item['product_id']);
                if (!$product) {
                    throw new \Exception("Product not found: {$item['product_id']}");
                }

                $quantity = $item['quantity'];
                $unitPrice = $product->price;
                $totalPrice = $quantity * $unitPrice;
                $subtotal += $totalPrice;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            $tax = $subtotal * 0.1; // 10% tax
            $shippingCost = 15.00; // Fixed shipping
            $total = $subtotal + $tax + $shippingCost;

            // Criar pedido
            $orderData = [
                'user_id' => $dto->userId,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_address' => $dto->shippingAddress,
                'billing_address' => $dto->billingAddress,
                'notes' => $dto->notes,
            ];

            $order = $this->orderRepository->create($orderData);

            // Criar itens do pedido
            foreach ($orderItems as &$item) {
                $item['order_id'] = $order->id;
                $item['created_at'] = now();
                $item['updated_at'] = now();
            }

            $this->orderItemRepository->createMany($orderItems);

            return $order->fresh(['orderItems.product']);
        });
    }

    public function updateOrder(int $id, UpdateOrderDTO $dto): ?Order
    {
        $order = $this->orderRepository->findById($id);
        
        if (!$order) {
            return null;
        }

        $data = array_filter([
            'status' => $dto->status,
            'shipping_address' => $dto->shippingAddress,
            'billing_address' => $dto->billingAddress,
            'notes' => $dto->notes,
        ], fn($value) => $value !== null);

        $this->orderRepository->update($order, $data);

        return $order->fresh();
    }

    public function deleteOrder(int $id): bool
    {
        $order = $this->orderRepository->findById($id);
        
        if (!$order) {
            return false;
        }

        return $this->orderRepository->delete($order);
    }
}