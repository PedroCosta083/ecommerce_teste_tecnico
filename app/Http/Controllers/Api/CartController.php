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

    /**
     * @OA\Get(
     *     path="/cart",
     *     tags={"Cart"},
     *     summary="Obtém carrinho do usuário",
     *     description="Retorna o carrinho completo com itens, produtos e totais. Suporta busca por user_id ou session_id",
     *     operationId="getCart",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID do usuário (para usuários autenticados)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="session_id",
     *         in="query",
     *         description="ID da sessão (para usuários guest)",
     *         required=false,
     *         @OA\Schema(type="string", example="abc123xyz")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrinho encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Carrinho não encontrado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/cart/items",
     *     tags={"Cart"},
     *     summary="Adiciona item ao carrinho",
     *     description="Adiciona um produto ao carrinho com quantidade especificada. Valida estoque disponível",
     *     operationId="addCartItem",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do item",
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="user_id", type="integer", example=1, description="ID do usuário (obrigatório se autenticado)"),
     *             @OA\Property(property="session_id", type="string", example="abc123xyz", description="ID da sessão (para guests)"),
     *             @OA\Property(property="product_id", type="integer", example=5, description="ID do produto"),
     *             @OA\Property(property="quantity", type="integer", example=2, description="Quantidade desejada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item adicionado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Item added to cart"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="item_id", type="integer", example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erro ao adicionar (estoque insuficiente)"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/cart/items/{cartItemId}",
     *     tags={"Cart"},
     *     summary="Atualiza quantidade de item no carrinho",
     *     description="Modifica a quantidade de um item específico do carrinho. Valida estoque",
     *     operationId="updateCartItem",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="cartItemId",
     *         in="path",
     *         description="ID do item no carrinho",
     *         required=true,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Nova quantidade",
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer", example=5, description="Nova quantidade")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart item updated")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Item não encontrado"),
     *     @OA\Response(response=400, description="Estoque insuficiente"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/cart/items/{cartItemId}",
     *     tags={"Cart"},
     *     summary="Remove item do carrinho",
     *     description="Exclui um item específico do carrinho",
     *     operationId="removeCartItem",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="cartItemId",
     *         in="path",
     *         description="ID do item no carrinho",
     *         required=true,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Item removed from cart")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Item não encontrado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function removeItem(int $cartItemId): JsonResponse
    {
        $removed = $this->cartService->removeCartItem($cartItemId);

        if (!$removed) {
            return $this->error('Cart item not found', 404);
        }

        return $this->success(null, 'Item removed from cart');
    }

    /**
     * @OA\Delete(
     *     path="/cart/{cartId}",
     *     tags={"Cart"},
     *     summary="Limpa todo o carrinho",
     *     description="Remove todos os itens do carrinho especificado",
     *     operationId="clearCart",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="cartId",
     *         in="path",
     *         description="ID do carrinho",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrinho limpo com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart cleared")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Carrinho não encontrado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function clear(int $cartId): JsonResponse
    {
        $cleared = $this->cartService->clearCart($cartId);

        if (!$cleared) {
            return $this->error('Cart not found', 404);
        }

        return $this->success(null, 'Cart cleared');
    }
}