<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Cart\AddToCartDTO;
use App\DTOs\Cart\UpdateCartItemDTO;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends ApiController
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function show(Request $request): JsonResponse
    {
        $userId = $request->query('user_id') ?? (int) $request->user_id ?? null;
        $sessionId = $request->query('session_id') ?? $request->session_id ?? null;

        $cart = $userId 
            ? $this->cartService->getCartByUser($userId)
            : $this->cartService->getCartBySession($sessionId);

        if (!$cart) {
            return $this->error('Cart not found', 404);
        }

        return $this->success(new CartResource($cart));
    }

    public function addItem(AddToCartRequest $request): JsonResponse
    {
        try {
            $dto = AddToCartDTO::fromRequest($request->validated());
            $cartItem = $this->cartService->addToCart($dto);

            return $this->success(['item_id' => $cartItem->id], 'Item added to cart', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function updateItem(UpdateCartItemRequest $request, int $cartItemId): JsonResponse
    {
        try {
            $dto = UpdateCartItemDTO::fromRequest($request->validated());
            $updated = $this->cartService->updateCartItem($cartItemId, $dto);

            if (!$updated) {
                return $this->error('Cart item not found', 404);
            }

            return $this->success(null, 'Cart item updated');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function removeItem(int $cartItemId): JsonResponse
    {
        $removed = $this->cartService->removeCartItem($cartItemId);

        if (!$removed) {
            return $this->error('Cart item not found', 404);
        }

        return $this->success(null, 'Item removed from cart');
    }

    public function clear(int $cartId): JsonResponse
    {
        $cleared = $this->cartService->clearCart($cartId);

        if (!$cleared) {
            return $this->error('Cart not found', 404);
        }

        return $this->success(null, 'Cart cleared');
    }
}