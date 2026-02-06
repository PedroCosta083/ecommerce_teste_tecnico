import { Head, Link, router } from '@inertiajs/react';
import { ShoppingCart, User, Search, CreditCard, Package } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
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
  products: {
    data: Product[];
    meta: {
      current_page: number;
      last_page: number;
      per_page: number;
      total: number;
    };
  };
  categories: Array<{ id: number; name: string }>;
  filters: any;
  auth?: { user: any };
}

export default function StorefrontIndex({ products, categories, filters, auth }: Props) {
  const [cartCount, setCartCount] = useState(0);
  const [search, setSearch] = useState(filters?.search || '');
  const [cartOpen, setCartOpen] = useState(false);

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

  const addToCart = async (productId: number) => {
    try {
      await fetch('/cart/items', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 }),
      });
      loadCartCount();
      alert('Produto adicionado ao carrinho!');
    } catch (error) {
      console.error('Erro ao adicionar ao carrinho:', error);
    }
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    router.get('/', { search }, { preserveState: true });
  };

  return (
    <>
      <Head title="Loja" />
      
      <div className="min-h-screen bg-gray-50">
        {/* Header */}
        <header className="bg-white shadow-sm sticky top-0 z-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div className="flex items-center justify-between">
              <Link href="/" className="text-2xl font-bold text-gray-900">
                Loja
              </Link>

              <form onSubmit={handleSearch} className="flex-1 max-w-lg mx-8">
                <div className="relative">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" />
                  <Input
                    type="search"
                    placeholder="Buscar produtos..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className="pl-10"
                  />
                </div>
              </form>

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

        {/* Categories */}
        <div className="bg-white border-b">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div className="flex items-center gap-4">
              <span className="text-sm font-medium">Categorias:</span>
              <Select
                value={filters?.category_id?.toString() || 'all'}
                onValueChange={(value) => {
                  if (value === 'all') {
                    router.get('/', {}, { preserveState: true });
                  } else {
                    router.get('/', { category_id: value }, { preserveState: true });
                  }
                }}
              >
                <SelectTrigger className="w-[200px]">
                  <SelectValue placeholder="Todas as categorias" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Todas as categorias</SelectItem>
                  {categories.map((category) => (
                    <SelectItem key={category.id} value={category.id.toString()}>
                      {category.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>

        {/* Products Grid */}
        <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {products.data.map((product) => (
              <Card key={product.id} className="overflow-hidden hover:shadow-lg transition-shadow">
                <Link href={`/produto/${product.id}`}>
                  <div className="aspect-square bg-gray-200 flex items-center justify-center">
                    <span className="text-gray-400">Imagem</span>
                  </div>
                </Link>
                
                <CardContent className="p-4">
                  <Link href={`/produto/${product.id}`}>
                    <h3 className="font-semibold text-lg mb-1 hover:text-blue-600">
                      {product.name}
                    </h3>
                  </Link>
                  <p className="text-sm text-gray-600 mb-2">{product.category.name}</p>
                  <p className="text-2xl font-bold text-green-600">
                    R$ {parseFloat(product.price).toFixed(2)}
                  </p>
                  {product.quantity > 0 ? (
                    <p className="text-sm text-gray-500 mt-1">{product.quantity} em estoque</p>
                  ) : (
                    <p className="text-sm text-red-500 mt-1">Fora de estoque</p>
                  )}
                </CardContent>

                <CardFooter className="p-4 pt-0 flex gap-2">
                  <Button
                    className="flex-1 cursor-pointer"
                    onClick={() => addToCart(product.id)}
                    disabled={product.quantity === 0}
                  >
                    <ShoppingCart className="h-4 w-4 mr-2" />
                    Adicionar
                  </Button>
                  <Button
                    variant="outline"
                    className="flex-1 cursor-pointer"
                    onClick={() => {
                      if (!auth?.user) {
                        router.visit('/login');
                      } else {
                        router.visit(`/checkout?product=${product.id}`);
                      }
                    }}
                    disabled={product.quantity === 0}
                  >
                    <CreditCard className="h-4 w-4 mr-2" />
                    Comprar
                  </Button>
                </CardFooter>
              </Card>
            ))}
          </div>

          {/* Pagination */}
          {products.meta.last_page > 1 && (
            <div className="flex justify-center gap-2 mt-8">
              {Array.from({ length: products.meta.last_page }, (_, i) => i + 1).map((page) => (
                <Button
                  key={page}
                  variant={page === products.meta.current_page ? 'default' : 'outline'}
                  size="sm"
                  className="cursor-pointer"
                  onClick={() => router.get('/', { ...filters, page }, { preserveState: true })}
                >
                  {page}
                </Button>
              ))}
            </div>
          )}
        </main>

        <CartSheet open={cartOpen} onClose={() => setCartOpen(false)} onUpdate={loadCartCount} />
      </div>
    </>
  );
}
