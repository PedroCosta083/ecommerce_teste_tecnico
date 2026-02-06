import { useState, useEffect } from 'react';
import { X, Plus, Minus, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { router } from '@inertiajs/react';

interface CartItem {
  id: number;
  product_id: number;
  quantity: number;
  product: {
    id: number;
    name: string;
    price: string;
    quantity: number;
  };
}

interface Cart {
  id: number;
  items: CartItem[];
  total: string;
}

interface Props {
  open: boolean;
  onClose: () => void;
  onUpdate?: () => void;
}

export default function CartSheet({ open, onClose, onUpdate }: Props) {
  const [cart, setCart] = useState<Cart | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (open) {
      loadCart();
    }
  }, [open]);

  const loadCart = async () => {
    try {
      const response = await fetch('/cart');
      const data = await response.json();
      setCart(data.data);
    } catch (error) {
      console.error('Erro ao carregar carrinho:', error);
    }
  };

  const updateQuantity = async (itemId: number, quantity: number) => {
    if (quantity < 1) return;
    
    setLoading(true);
    try {
      await fetch(`/cart/items/${itemId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ quantity }),
      });
      await loadCart();
      onUpdate?.();
    } catch (error) {
      console.error('Erro ao atualizar quantidade:', error);
    } finally {
      setLoading(false);
    }
  };

  const removeItem = async (itemId: number) => {
    setLoading(true);
    try {
      await fetch(`/cart/items/${itemId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      await loadCart();
      onUpdate?.();
    } catch (error) {
      console.error('Erro ao remover item:', error);
    } finally {
      setLoading(false);
    }
  };

  const checkout = () => {
    router.visit('/checkout');
  };

  const total = cart?.items.reduce((sum, item) => {
    return sum + parseFloat(item.product.price) * item.quantity;
  }, 0) || 0;

  return (
    <Sheet open={open} onOpenChange={onClose}>
      <SheetContent className="w-full sm:max-w-lg">
        <SheetHeader>
          <SheetTitle>Carrinho de Compras</SheetTitle>
        </SheetHeader>

        <div className="flex flex-col h-full mt-6">
          {!cart || cart.items.length === 0 ? (
            <div className="flex-1 flex items-center justify-center">
              <p className="text-gray-500">Seu carrinho está vazio</p>
            </div>
          ) : (
            <>
              <div className="flex-1 overflow-y-auto space-y-4">
                {cart.items.map((item) => (
                  <div key={item.id} className="flex gap-4 p-4 border rounded-lg">
                    <div className="w-20 h-20 bg-gray-200 rounded flex-shrink-0" />
                    
                    <div className="flex-1 min-w-0">
                      <h4 className="font-semibold truncate">{item.product.name}</h4>
                      <p className="text-green-600 font-bold">
                        R$ {parseFloat(item.product.price).toFixed(2)}
                      </p>
                      
                      <div className="flex items-center gap-2 mt-2">
                        <Button
                          variant="outline"
                          size="icon"
                          className="h-7 w-7"
                          onClick={() => updateQuantity(item.id, item.quantity - 1)}
                          disabled={loading || item.quantity <= 1}
                        >
                          <Minus className="h-3 w-3" />
                        </Button>
                        <span className="w-8 text-center">{item.quantity}</span>
                        <Button
                          variant="outline"
                          size="icon"
                          className="h-7 w-7"
                          onClick={() => updateQuantity(item.id, item.quantity + 1)}
                          disabled={loading || item.quantity >= item.product.quantity}
                        >
                          <Plus className="h-3 w-3" />
                        </Button>
                      </div>
                    </div>

                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => removeItem(item.id)}
                      disabled={loading}
                    >
                      <Trash2 className="h-4 w-4 text-red-500" />
                    </Button>
                  </div>
                ))}
              </div>

              <div className="border-t pt-4 mt-4 space-y-4">
                <div className="flex justify-between text-lg font-bold">
                  <span>Total:</span>
                  <span className="text-green-600">R$ {total.toFixed(2)}</span>
                </div>
                
                <Button className="w-full" size="lg" onClick={checkout}>
                  Finalizar Compra
                </Button>
              </div>
            </>
          )}
        </div>
      </SheetContent>
    </Sheet>
  );
}
