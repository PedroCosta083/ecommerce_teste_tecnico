<?php

namespace App\Repositories\Contracts;

use App\Models\Cart;
use App\Models\CartItem;

interface CartRepositoryInterface
{
    public function findByUser(int $userId): ?Cart;
    public function findBySession(string $sessionId): ?Cart;
    public function findOrCreateByUser(int $userId): Cart;
    public function findOrCreateBySession(string $sessionId): Cart;
    public function addItem(Cart $cart, int $productId, int $quantity): CartItem;
    public function updateItem(CartItem $cartItem, int $quantity): bool;
    public function removeItem(CartItem $cartItem): bool;
    public function clearCart(Cart $cart): bool;
    public function delete(Cart $cart): bool;
}