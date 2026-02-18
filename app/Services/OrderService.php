<?php

namespace App\Services;

use App\DTOs\Order\CreateOrderDTO;
use App\DTOs\Order\UpdateOrderDTO;
use App\DTOs\Order\OrderFilterDTO;
use App\Events\OrderCreated;
use App\Jobs\ProcessOrder;
use App\Jobs\SendOrderConfirmation;
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
        $order = DB::transaction(function () use ($dto) {
            $subtotal = 0;
            $orderItems = [];

            // Validar estoque e calcular subtotal
            foreach ($dto->items as $item) {
                $product = $this->productRepository->findById($item['product_id']);
                if (!$product) {
                    throw new \Exception("Product not found: {$item['product_id']}");
                }

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
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

            $tax = $subtotal * 0.1;
            $shippingCost = 15.00;
            $total = $subtotal + $tax + $shippingCost;

            $order = $this->orderRepository->create([
                'user_id' => $dto->userId,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_address' => $dto->shippingAddress,
                'billing_address' => $dto->billingAddress,
                'notes' => $dto->notes,
            ]);

            foreach ($orderItems as &$item) {
                $item['order_id'] = $order->id;
                $item['created_at'] = now();
                $item['updated_at'] = now();
            }

            $this->orderItemRepository->createMany($orderItems);

            return $order->fresh(['orderItems.product', 'user']);
        });

        // Disparar evento OrderCreated
        OrderCreated::dispatch($order);

        return $order;
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

    public function updateOrderStatus(int $id, string $status): ?Order
    {
        $order = $this->orderRepository->findById($id);
        
        if (!$order) {
            return null;
        }

        $this->orderRepository->update($order, ['status' => $status]);

        return $order->fresh();
    }
}