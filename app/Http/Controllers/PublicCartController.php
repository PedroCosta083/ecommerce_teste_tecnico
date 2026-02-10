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
        if (auth()->check()) {
            $cart = $this->cartService->getCartByUser(auth()->id());
            
            if (!$cart) {
                return response()->json([
                    'data' => [
                        'items' => [],
                        'total_items' => 0,
                        'total_price' => 0
                    ]
                ]);
            }
            
            return response()->json(['data' => (new CartResource($cart))->resolve()]);
        }
        
        // Guest cart from session
        $guestCart = session()->get('guest_cart', []);
        
        return response()->json([
            'data' => [
                'items' => $guestCart,
                'total_items' => collect($guestCart)->sum('quantity'),
                'total_price' => collect($guestCart)->sum(fn($item) => ($item['product']['price'] ?? 0) * $item['quantity'])
            ]
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if (auth()->check()) {
            $dto = new AddToCartDTO(
                userId: auth()->id(),
                sessionId: null,
                productId: $request->product_id,
                quantity: $request->quantity
            );

            $cartItem = $this->cartService->addToCart($dto);

            return response()->json([
                'message' => 'Produto adicionado ao carrinho',
                'item_id' => $cartItem->id
            ], 201);
        }
        
        // Guest cart in session
        $product = \App\Models\Product::findOrFail($request->product_id);
        $guestCart = session()->get('guest_cart', []);
        
        $existingIndex = collect($guestCart)->search(fn($item) => $item['product_id'] == $product->id);
        
        if ($existingIndex !== false) {
            $guestCart[$existingIndex]['quantity'] += $request->quantity;
        } else {
            $guestCart[] = [
                'id' => uniqid(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                ]
            ];
        }
        
        session()->put('guest_cart', $guestCart);
        
        return response()->json([
            'message' => 'Produto adicionado ao carrinho',
            'item_id' => end($guestCart)['id']
        ], 201);
    }

    public function updateItem(Request $request, $cartItemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (auth()->check()) {
            $updated = $this->cartService->updateCartItem($cartItemId, 
                new \App\DTOs\Cart\UpdateCartItemDTO($request->quantity)
            );

            if (!$updated) {
                return response()->json(['message' => 'Item não encontrado'], 404);
            }

            return response()->json(['message' => 'Carrinho atualizado']);
        }
        
        // Guest cart
        $guestCart = session()->get('guest_cart', []);
        $index = collect($guestCart)->search(fn($item) => $item['id'] == $cartItemId);
        
        if ($index === false) {
            return response()->json(['message' => 'Item não encontrado'], 404);
        }
        
        $guestCart[$index]['quantity'] = $request->quantity;
        session()->put('guest_cart', $guestCart);
        
        return response()->json(['message' => 'Carrinho atualizado']);
    }

    public function removeItem($cartItemId): JsonResponse
    {
        if (auth()->check()) {
            $removed = $this->cartService->removeCartItem($cartItemId);

            if (!$removed) {
                return response()->json(['message' => 'Item não encontrado'], 404);
            }

            return response()->json(['message' => 'Item removido']);
        }
        
        // Guest cart
        $guestCart = session()->get('guest_cart', []);
        $index = collect($guestCart)->search(fn($item) => $item['id'] == $cartItemId);
        
        if ($index === false) {
            return response()->json(['message' => 'Item não encontrado'], 404);
        }
        
        unset($guestCart[$index]);
        session()->put('guest_cart', array_values($guestCart));
        
        return response()->json(['message' => 'Item removido']);
    }

    public function mergeOnLogin(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        $guestCart = session()->get('guest_cart', []);
        
        if (!empty($guestCart)) {
            foreach ($guestCart as $item) {
                $dto = new AddToCartDTO(
                    userId: auth()->id(),
                    sessionId: null,
                    productId: $item['product_id'],
                    quantity: $item['quantity']
                );
                
                $this->cartService->addToCart($dto);
            }
            
            session()->forget('guest_cart');
        }

        return response()->json(['message' => 'Carrinho sincronizado']);
    }
}
