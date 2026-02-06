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
            
            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('home')->with('error', 'Carrinho vazio');
            }

            $items = $cart->items->map(fn($item) => [
                'product' => $item->product,
                'quantity' => $item->quantity,
                'subtotal' => $item->product->price * $item->quantity,
            ]);

            $subtotal = $items->sum('subtotal');
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
        ]);

        $dto = new CreateOrderDTO(
            userId: auth()->id(),
            items: $validated['items'],
            shippingAddress: $validated['shipping_address'],
            billingAddress: $validated['billing_address'],
            notes: $request->input('notes')
        );

        $order = $this->orderService->createOrder($dto);

        if (!$request->input('direct_purchase')) {
            $cart = $this->cartService->getCartByUser(auth()->id());
            if ($cart) {
                $this->cartService->clearCart($cart->id);
            }
        }

        return Inertia::render('checkout/success', [
            'order' => $order,
        ]);
    }
}
