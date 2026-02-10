import { Head, Link, router } from '@inertiajs/react';
import { ShoppingCart, User, ArrowLeft, Plus, Minus, CreditCard, Package } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { useState, useEffect } from 'react';
import CartSheet from '@/components/cart-sheet';

interface Product {
  id: number;
  name: string;
  slug: string;
  description: string;
  price: string;
  quantity: number;
  category: { id: number; name: string };
  tags: Array<{ id: number; name: string }>;
}

interface Props {
  product: Product;
  auth?: { user: any };
}

export default function StorefrontProduct({ product, auth }: Props) {
  const [quantity, setQuantity] = useState(1);
  const [loading, setLoading] = useState(false);
  const [cartOpen, setCartOpen] = useState(false);
  const [cartCount, setCartCount] = useState(0);

  useEffect(() => {
    loadCartCount();
  }, []);

  const loadCartCount = async () => {
    try {
      const response = await fetch('/cart');
      const data = await response.json();
      if (data.data?.items) {
        setCartCount(data.data.items.length);
      }
    } catch (error) {
      console.error('Erro ao carregar carrinho:', error);
    }
  };

  const addToCart = async () => {
    setLoading(true);
    try {
      const response = await fetch('/cart/items', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ product_id: product.id, quantity }),
      });
      
      if (response.ok) {
        await loadCartCount();
        setCartOpen(true);
      }
    } catch (error) {
      console.error('Erro ao adicionar ao carrinho:', error);
      alert('Erro ao adicionar ao carrinho');
    } finally {
      setLoading(false);
    }
  };

  const buyNow = () => {
    if (!auth?.user) {
      router.visit('/login');
    } else {
      router.visit(`/checkout?product=${product.id}&quantity=${quantity}`);
    }
  };

  return (
    <>
      <Head title={product.name} />
      
      <div className="min-h-screen bg-gray-50">
        {/* Header */}
        <header className="bg-white shadow-sm sticky top-0 z-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div className="flex items-center justify-between">
              <Link href="/" className="text-2xl font-bold text-gray-900">
                Loja
              </Link>

              <div className="flex items-center gap-4">
                <Link href="/meus-pedidos">
                  <Button variant="ghost" size="sm" className="cursor-pointer">
                    <Package className="h-5 w-5 mr-2" />
                    Meus Pedidos
                  </Button>
                </Link>
                
                {auth?.user ? (
                  <Link href="/dashboard">
                    <Button variant="ghost" size="sm">
                      <User className="h-5 w-5 mr-2" />
                      {auth.user.name}
                    </Button>
                  </Link>
                ) : (
                  <Link href="/login">
                    <Button variant="ghost" size="sm">
                      <User className="h-5 w-5 mr-2" />
                      Entrar
                    </Button>
                  </Link>
                )}

                <Button variant="outline" size="sm" className="relative" onClick={() => setCartOpen(true)}>
                  <ShoppingCart className="h-5 w-5" />
                  {cartCount > 0 && (
                    <Badge className="absolute -top-2 -right-2 h-5 w-5 flex items-center justify-center p-0">
                      {cartCount}
                    </Badge>
                  )}
                </Button>
              </div>
            </div>
          </div>
        </header>

        <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <Link href="/">
            <Button variant="ghost" size="sm" className="mb-6">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Voltar
            </Button>
          </Link>

          <div className="grid md:grid-cols-2 gap-8">
            {/* Product Image */}
            <div className="aspect-square bg-gray-200 rounded-lg flex items-center justify-center">
              <span className="text-gray-400 text-xl">Imagem do Produto</span>
            </div>

            {/* Product Info */}
            <div>
              <Badge className="mb-2">{product.category.name}</Badge>
              <h1 className="text-3xl font-bold mb-4">{product.name}</h1>
              
              <div className="flex items-baseline gap-2 mb-6">
                <span className="text-4xl font-bold text-green-600">
                  R$ {parseFloat(product.price).toFixed(2)}
                </span>
              </div>

              {product.quantity > 0 ? (
                <p className="text-green-600 mb-6">✓ {product.quantity} em estoque</p>
              ) : (
                <p className="text-red-600 mb-6">✗ Fora de estoque</p>
              )}

              <Card className="mb-6">
                <CardContent className="p-6">
                  <h3 className="font-semibold mb-2">Descrição</h3>
                  <p className="text-gray-600">{product.description || 'Sem descrição disponível'}</p>
                </CardContent>
              </Card>

              {product.tags.length > 0 && (
                <div className="mb-6">
                  <h3 className="font-semibold mb-2">Tags</h3>
                  <div className="flex flex-wrap gap-2">
                    {product.tags.map((tag) => (
                      <Badge key={tag.id} variant="outline">
                        {tag.name}
                      </Badge>
                    ))}
                  </div>
                </div>
              )}

              {/* Quantity Selector */}
              <div className="mb-6">
                <label className="block text-sm font-medium mb-2">Quantidade</label>
                <div className="flex items-center gap-3">
                  <Button
                    variant="outline"
                    size="icon"
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    disabled={quantity <= 1}
                  >
                    <Minus className="h-4 w-4" />
                  </Button>
                  <span className="text-xl font-semibold w-12 text-center">{quantity}</span>
                  <Button
                    variant="outline"
                    size="icon"
                    onClick={() => setQuantity(Math.min(product.quantity, quantity + 1))}
                    disabled={quantity >= product.quantity}
                  >
                    <Plus className="h-4 w-4" />
                  </Button>
                </div>
              </div>

              {/* Action Buttons */}
              <div className="flex gap-3">
                <Button
                  className="flex-1 cursor-pointer"
                  size="lg"
                  onClick={addToCart}
                  disabled={product.quantity === 0 || loading}
                >
                  <ShoppingCart className="h-5 w-5 mr-2" />
                  Adicionar ao Carrinho
                </Button>
                <Button
                  variant="outline"
                  className="flex-1 cursor-pointer"
                  size="lg"
                  onClick={buyNow}
                  disabled={product.quantity === 0}
                >
                  <CreditCard className="h-5 w-5 mr-2" />
                  Comprar Agora
                </Button>
              </div>
            </div>
          </div>
        </main>

        <CartSheet open={cartOpen} onClose={() => setCartOpen(false)} onUpdate={loadCartCount} />
      </div>
    </>
  );
}
