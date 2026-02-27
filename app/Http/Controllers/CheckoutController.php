<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\DTOs\Order\CreateOrderDTO;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
        private ProductService $productService
    ) {}

    public function index(Request $request): Response
    {
        // Migrar carrinho da sessão para o banco se existir
        $guestCart = session()->get('guest_cart', []);
        $selectedIds = $request->query('selected'); // IDs dos itens selecionados da sessão
        $newSelectedIds = []; // Novos IDs após migração
        
        if (!empty($guestCart)) {
            $selectedSessionIds = $selectedIds ? explode(',', $selectedIds) : [];
            
            foreach ($guestCart as $item) {
                $dto = new \App\DTOs\Cart\AddToCartDTO(
                    userId: auth()->id(),
                    sessionId: null,
                    productId: $item['product_id'],
                    quantity: $item['quantity']
                );
                
                $cartItem = $this->cartService->addToCart($dto);
                
                // Se este item estava selecionado, mapear novo ID
                if (in_array($item['id'], $selectedSessionIds)) {
                    $newSelectedIds[] = $cartItem->id;
                }
            }
            
            // Atualizar selectedIds com os novos IDs do banco
            if (!empty($newSelectedIds)) {
                $selectedIds = implode(',', $newSelectedIds);
            }
        }

        $productId = $request->query('product');
        $quantity  = $request->query('quantity', 1);

        if ($productId) {
            $product = $this->productService->getProductById($productId);
            
            if (!$product) {
                abort(404);
            }

            $items = [[
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
            ]];

            $subtotal = $product->price * $quantity;
        } else {
            $cart = $this->cartService->getCartByUser(auth()->id());
            
            if (!$cart) {
                return Inertia::redirect('/')->with('error', 'Carrinho vazio');
            }

            // Carregar relacionamento items
            $cart->load('items.product');
            
            if ($cart->items->isEmpty()) {
                return Inertia::redirect('/')->with('error', 'Carrinho vazio');
            }

            // Filtrar apenas itens selecionados
            $selectedItemIds = $selectedIds ? explode(',', $selectedIds) : [];
            
            $cartItems = !empty($selectedItemIds)
                ? $cart->items->whereIn('id', $selectedItemIds)
                : $cart->items;

            if ($cartItems->isEmpty()) {
                return Inertia::redirect('/')->with('error', 'Nenhum item selecionado');
            }

            $items = $cartItems->map(fn($item) => [
                'product' => $item->product,
                'quantity' => $item->quantity,
                'subtotal' => $item->product->price * $item->quantity,
                'cart_item_id' => $item->id, // Para remover depois
            ])->values()->toArray();

            $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        }

        $tax = $subtotal * 0.1;
        $shipping = 15.00;
        $total = $subtotal + $tax + $shipping;

        return Inertia::render('checkout/index', [
            'items' => $items,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'tax' => number_format($tax, 2, '.', ''),
            'shipping' => number_format($shipping, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
            'directPurchase' => (bool) $productId,
            'selectedItemIds' => $selectedIds,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|array',
            'billing_address' => 'required|array',
            'cart_item_ids' => 'nullable|array',
        ]);

        $dto = new CreateOrderDTO(
            userId: auth()->id(),
            items: $validated['items'],
            shippingAddress: json_encode($validated['shipping_address']),
            billingAddress: json_encode($validated['billing_address']),
            notes: $request->input('notes')
        );

        $order = $this->orderService->createOrder($dto);

        // Remover APENAS os itens comprados do carrinho
        if (!$request->input('direct_purchase')) {
            $cartItemIds = $request->input('cart_item_ids', []);
            
            if (!empty($cartItemIds)) {
                \App\Models\CartItem::whereIn('id', $cartItemIds)
                    ->where('cart_id', function($query) {
                        $query->select('id')
                            ->from('carts')
                            ->where('user_id', auth()->id())
                            ->limit(1);
                    })
                    ->delete();
            }
        }
        
        session()->forget('guest_cart');

        return Inertia::render('checkout/success', [
            'order' => $order,
        ]);
    }
}
