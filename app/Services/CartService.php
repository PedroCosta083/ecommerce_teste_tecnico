<?php

namespace App\Services;

use App\DTOs\Cart\AddToCartDTO;
use App\DTOs\Cart\UpdateCartItemDTO;
use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

class CartService
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getCartByUser(int $userId): ?Cart
    {
        return $this->cartRepository->findByUser($userId);
    }

    public function getCartBySession(string $sessionId): ?Cart
    {
        return $this->cartRepository->findBySession($sessionId);
    }

    public function addToCart(AddToCartDTO $dto): CartItem
    {
        $product = $this->productRepository->findById($dto->productId);
        
        if (!$product) {
            throw new \Exception('Product not found');
        }

        if ($product->quantity < $dto->quantity) {
            throw new \Exception('Insufficient stock');
        }

        $cart = $dto->userId 
            ? $this->cartRepository->findOrCreateByUser($dto->userId)
            : $this->cartRepository->findOrCreateBySession($dto->sessionId);

        return $this->cartRepository->addItem($cart, $dto->productId, $dto->quantity);
    }

    public function updateCartItem(int $cartItemId, UpdateCartItemDTO $dto): bool
    {
        $cartItem = CartItem::find($cartItemId);
        
        if (!$cartItem) {
            return false;
        }

        $product = $this->productRepository->findById($cartItem->product_id);
        
        if ($product->quantity < $dto->quantity) {
            throw new \Exception('Insufficient stock');
        }

        return $this->cartRepository->updateItem($cartItem, $dto->quantity);
    }

    public function removeCartItem(int $cartItemId): bool
    {
        $cartItem = CartItem::find($cartItemId);
        
        if (!$cartItem) {
            return false;
        }

        return $this->cartRepository->removeItem($cartItem);
    }

    public function clearCart(int $cartId): bool
    {
        $cart = Cart::find($cartId);
        
        if (!$cart) {
            return false;
        }

        return $this->cartRepository->clearCart($cart);
    }

    public function mergeGuestCart(int $userId, string $sessionId): void
    {
        $guestCart = $this->cartRepository->findBySession($sessionId);
        
        if (!$guestCart) {
            return;
        }

        $userCart = $this->cartRepository->findOrCreateByUser($userId);

        foreach ($guestCart->items as $item) {
            $existingItem = $userCart->items->firstWhere('product_id', $item->product_id);
            
            if ($existingItem) {
                $this->cartRepository->updateItem($existingItem, $existingItem->quantity + $item->quantity);
            } else {
                $this->cartRepository->addItem($userCart, $item->product_id, $item->quantity);
            }
        }

        $this->cartRepository->clearCart($guestCart);
        $guestCart->delete();
    }
}