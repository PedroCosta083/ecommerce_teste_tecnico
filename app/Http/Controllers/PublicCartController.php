<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\DTOs\Cart\AddToCartDTO;
use App\Http\Resources\CartResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicCartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function show(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        $cart = $userId 
            ? $this->cartService->getCartByUser($userId)
            : $this->cartService->getCartBySession($sessionId);

        if (!$cart) {
            return response()->json(['data' => null]);
        }

        return response()->json(new CartResource($cart));
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $dto = new AddToCartDTO(
            userId: auth()->id(),
            sessionId: session()->getId(),
            productId: $request->product_id,
            quantity: $request->quantity
        );

        $cartItem = $this->cartService->addToCart($dto);

        return response()->json([
            'message' => 'Produto adicionado ao carrinho',
            'item_id' => $cartItem->id
        ], 201);
    }

    public function updateItem(Request $request, int $cartItemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $updated = $this->cartService->updateCartItem($cartItemId, 
            new \App\DTOs\Cart\UpdateCartItemDTO($request->quantity)
        );

        if (!$updated) {
            return response()->json(['message' => 'Item não encontrado'], 404);
        }

        return response()->json(['message' => 'Carrinho atualizado']);
    }

    public function removeItem(int $cartItemId): JsonResponse
    {
        $removed = $this->cartService->removeCartItem($cartItemId);

        if (!$removed) {
            return response()->json(['message' => 'Item não encontrado'], 404);
        }

        return response()->json(['message' => 'Item removido']);
    }

    public function mergeOnLogin(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        $sessionId = $request->input('session_id');
        
        if ($sessionId) {
            $this->cartService->mergeGuestCart(auth()->id(), $sessionId);
        }

        return response()->json(['message' => 'Carrinho sincronizado']);
    }
}
