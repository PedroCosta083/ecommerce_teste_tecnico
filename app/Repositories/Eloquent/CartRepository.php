<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function findByUser(int $userId): ?Cart
    {
        return Cart::with(['items.product'])->where('user_id', $userId)->first();
    }

    public function findBySession(string $sessionId): ?Cart
    {
        return Cart::with(['items.product'])->where('session_id', $sessionId)->first();
    }

    public function findOrCreateByUser(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    public function findOrCreateBySession(string $sessionId): Cart
    {
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function addItem(Cart $cart, int $productId, int $quantity): CartItem
    {
        return CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $productId],
            ['quantity' => $quantity]
        );
    }

    public function updateItem(CartItem $cartItem, int $quantity): bool
    {
        return $cartItem->update(['quantity' => $quantity]);
    }

    public function removeItem(CartItem $cartItem): bool
    {
        return $cartItem->delete();
    }

    public function clearCart(Cart $cart): bool
    {
        return $cart->items()->delete();
    }

    public function delete(Cart $cart): bool
    {
        return $cart->delete();
    }
}
