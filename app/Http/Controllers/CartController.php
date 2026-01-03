<?php

namespace App\Http\Controllers;

use App\DTOs\Cart\AddToCartDTO;
use App\DTOs\Cart\UpdateCartItemDTO;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function show(Request $request): JsonResponse
    {
        // dd('chegou aqui');
        // dd( $request->all());
        $userId = $request->query('user_id') == null ? (int) $request->user_id : null;
        $sessionId = $request->query('session_id') == null ? $request->session_id : null;

        // dd($userId, $sessionId);
        $cart = $userId 
            ? $this->cartService->getCartByUser($userId)
            : $this->cartService->getCartBySession($sessionId);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        return response()->json(new CartResource($cart));
    }

    public function addItem(AddToCartRequest $request): JsonResponse
    {
        try {
            $dto = AddToCartDTO::fromRequest($request->validated());
            $cartItem = $this->cartService->addToCart($dto);

            return response()->json(['message' => 'Item added to cart', 'item_id' => $cartItem->id], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateItem(UpdateCartItemRequest $request, int $cartItemId): JsonResponse
    {
        try {
            $dto = UpdateCartItemDTO::fromRequest($request->validated());
            $updated = $this->cartService->updateCartItem($cartItemId, $dto);

            if (!$updated) {
                return response()->json(['message' => 'Cart item not found'], 404);
            }

            return response()->json(['message' => 'Cart item updated']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function removeItem(int $cartItemId): JsonResponse
    {
        $removed = $this->cartService->removeCartItem($cartItemId);

        if (!$removed) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        return response()->json(['message' => 'Item removed from cart']);
    }

    public function clear(int $cartId): JsonResponse
    {
        $cleared = $this->cartService->clearCart($cartId);

        if (!$cleared) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        return response()->json(['message' => 'Cart cleared']);
    }
}