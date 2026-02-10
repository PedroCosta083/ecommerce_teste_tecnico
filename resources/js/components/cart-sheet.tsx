import { useState, useEffect } from 'react';
import { Plus, Minus, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { router } from '@inertiajs/react';

interface CartItem {
  id: string | number;
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
  items: CartItem[];
  total: number;
}

interface Props {
  open: boolean;
  onClose: () => void;
  onUpdate?: () => void;
}

export default function CartSheet({ open, onClose, onUpdate }: Props) {
  const [cart, setCart] = useState<Cart | null>(null);
  const [loading, setLoading] = useState(false);
  const [selectedItems, setSelectedItems] = useState<Set<string | number>>(new Set());

  useEffect(() => {
    if (open) {
      loadCart();
    }
  }, [open]);

  const loadCart = async () => {
    try {
      const response = await fetch('/cart');
      const data = await response.json();
      
      const items = data.data?.items || [];
      setCart({
        items,
        total: data.data?.total_price || 0
      });
      // Selecionar todos os itens por padrão
      setSelectedItems(new Set(items.map((item: CartItem) => item.id)));
    } catch (error) {
      console.error('Erro ao carregar carrinho:', error);
      setCart({ items: [], total: 0 });
      setSelectedItems(new Set());
    }
  };

  const toggleItemSelection = (itemId: string | number) => {
    setSelectedItems(prev => {
      const newSet = new Set(prev);
      if (newSet.has(itemId)) {
        newSet.delete(itemId);
      } else {
        newSet.add(itemId);
      }
      return newSet;
    });
  };

  const toggleSelectAll = () => {
    if (selectedItems.size === cart?.items.length) {
      setSelectedItems(new Set());
    } else {
      setSelectedItems(new Set(cart?.items.map(item => item.id) || []));
    }
  };

  const updateQuantity = async (itemId: string | number, quantity: number) => {
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

  const removeItem = async (itemId: string | number) => {
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

  const checkout = async () => {
    if (selectedItems.size === 0) {
      alert('Selecione pelo menos um item para finalizar a compra');
      return;
    }

    try {
      // Migrar carrinho da sessão para o banco antes de ir para checkout
      await fetch('/cart/merge', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      
      // Passar itens selecionados para o checkout
      const selectedItemIds = Array.from(selectedItems).join(',');
      router.visit(`/checkout?selected=${selectedItemIds}`);
    } catch (error) {
      console.error('Erro ao sincronizar carrinho:', error);
      const selectedItemIds = Array.from(selectedItems).join(',');
      router.visit(`/checkout?selected=${selectedItemIds}`);
    }
  };

  const selectedTotal = cart?.items
    ?.filter(item => selectedItems.has(item.id))
    ?.reduce((sum, item) => sum + parseFloat(item.product.price) * item.quantity, 0) || 0;

  return (
    <Sheet open={open} onOpenChange={onClose}>
      <SheetContent className="w-full sm:max-w-lg">
        <SheetHeader>
          <SheetTitle>Carrinho de Compras</SheetTitle>
        </SheetHeader>

        <div className="flex flex-col h-full mt-6">
          {!cart || !cart.items || cart.items.length === 0 ? (
            <div className="flex-1 flex items-center justify-center">
              <p className="text-gray-500">Seu carrinho está vazio</p>
            </div>
          ) : (
            <>
              <div className="flex items-center gap-2 px-4 pb-2 border-b">
                <Checkbox
                  checked={selectedItems.size === cart.items.length && cart.items.length > 0}
                  onCheckedChange={toggleSelectAll}
                />
                <span className="text-sm font-medium">
                  Selecionar todos ({selectedItems.size}/{cart.items.length})
                </span>
              </div>

              <div className="flex-1 overflow-y-auto space-y-4 p-4">
                {cart.items.map((item) => (
                  <div key={item.id} className="flex gap-3 p-4 border rounded-lg">
                    <Checkbox
                      checked={selectedItems.has(item.id)}
                      onCheckedChange={() => toggleItemSelection(item.id)}
                      className="mt-1"
                    />
                    
                    <div className="w-16 h-16 bg-gray-200 rounded flex-shrink-0" />
                    
                    <div className="flex-1 min-w-0">
                      <h4 className="font-semibold truncate text-sm">{item.product.name}</h4>
                      <p className="text-green-600 font-bold text-sm">
                        R$ {parseFloat(item.product.price).toFixed(2)}
                      </p>
                      
                      <div className="flex items-center gap-2 mt-2">
                        <Button
                          variant="outline"
                          size="icon"
                          className="h-6 w-6"
                          onClick={() => updateQuantity(item.id, item.quantity - 1)}
                          disabled={loading || item.quantity <= 1}
                        >
                          <Minus className="h-3 w-3" />
                        </Button>
                        <span className="w-8 text-center text-sm">{item.quantity}</span>
                        <Button
                          variant="outline"
                          size="icon"
                          className="h-6 w-6"
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
                      className="h-8 w-8"
                      onClick={() => removeItem(item.id)}
                      disabled={loading}
                    >
                      <Trash2 className="h-4 w-4 text-red-500" />
                    </Button>
                  </div>
                ))}
              </div>

              <div className="border-t p-4 space-y-3">
                <div className="flex justify-between text-sm text-gray-600">
                  <span>Itens selecionados:</span>
                  <span>{selectedItems.size}</span>
                </div>
                <div className="flex justify-between text-lg font-bold">
                  <span>Total:</span>
                  <span className="text-green-600">R$ {selectedTotal.toFixed(2)}</span>
                </div>
                
                <Button 
                  className="w-full" 
                  size="lg" 
                  onClick={checkout}
                  disabled={selectedItems.size === 0}
                >
                  Finalizar Compra ({selectedItems.size} {selectedItems.size === 1 ? 'item' : 'itens'})
                </Button>
              </div>
            </>
          )}
        </div>
      </SheetContent>
    </Sheet>
  );
}
