import { Head, Link, router } from '@inertiajs/react';
import { ShoppingCart, User, Search, CreditCard, Package, Store, Heart } from 'lucide-react';
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
  image?: string;
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
      const response = await fetch('/cart/items', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 }),
      });
      
      if (response.ok) {
        await loadCartCount();
        setCartOpen(true);
      }
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
      
      <div className="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        {/* Header */}
        <header className="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div className="flex items-center justify-between gap-4">
              <Link href="/" className="flex items-center gap-2">
                <Store className="h-8 w-8 text-primary" />
                <span className="text-2xl font-bold bg-gradient-to-r from-primary to-primary/60 bg-clip-text text-transparent">
                  Loja
                </span>
              </Link>

              <form onSubmit={handleSearch} className="flex-1 max-w-2xl">
                <div className="relative">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" />
                  <Input
                    type="search"
                    placeholder="Buscar produtos..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className="pl-10 h-11 bg-gray-50 border-gray-200 focus:bg-white transition-colors"
                  />
                </div>
              </form>

              <div className="flex items-center gap-2">
                <Link href="/meus-pedidos">
                  <Button variant="ghost" size="sm" className="cursor-pointer">
                    <Package className="h-5 w-5 sm:mr-2" />
                    <span className="hidden sm:inline">Pedidos</span>
                  </Button>
                </Link>
                
                {auth?.user ? (
                  <Link href="/dashboard">
                    <Button variant="ghost" size="sm">
                      <User className="h-5 w-5 sm:mr-2" />
                      <span className="hidden sm:inline">{auth.user.name}</span>
                    </Button>
                  </Link>
                ) : (
                  <Link href="/login">
                    <Button variant="ghost" size="sm">
                      <User className="h-5 w-5 sm:mr-2" />
                      <span className="hidden sm:inline">Entrar</span>
                    </Button>
                  </Link>
                )}

                <Button variant="outline" size="sm" className="relative" onClick={() => setCartOpen(true)}>
                  <ShoppingCart className="h-5 w-5" />
                  {cartCount > 0 && (
                    <Badge className="absolute -top-2 -right-2 h-5 w-5 flex items-center justify-center p-0 text-xs">
                      {cartCount}
                    </Badge>
                  )}
                </Button>
              </div>
            </div>
          </div>
        </header>

        {/* Categories */}
        <div className="bg-white border-b shadow-sm">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div className="flex items-center gap-4">
              <span className="text-sm font-medium text-gray-700">Categorias:</span>
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
                <SelectTrigger className="w-[220px] bg-gray-50">
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
          <div className="mb-6">
            <h2 className="text-2xl font-bold text-gray-900">Produtos em Destaque</h2>
            <p className="text-gray-600 mt-1">Encontre os melhores produtos para você</p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {products.data.map((product) => (
              <Card key={product.id} className="group overflow-hidden hover:shadow-2xl transition-all duration-300 border-0 shadow-md">
                <Link href={`/produto/${product.id}`}>
                  <div className="relative aspect-square bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                    {product.image ? (
                      <img 
                        src={product.image} 
                        alt={product.name} 
                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                      />
                    ) : (
                      <div className="w-full h-full flex items-center justify-center">
                        <Package className="h-20 w-20 text-gray-300" />
                      </div>
                    )}
                  </div>
                </Link>
                
                <CardContent className="p-4 space-y-3">
                  <Link href={`/produto/${product.id}`}>
                    <h3 className="font-semibold text-lg hover:text-primary transition-colors line-clamp-2 min-h-[56px]">
                      {product.name}
                    </h3>
                  </Link>
                  <p className="text-sm text-gray-600">{product.category.name}</p>
                  
                  <div className="flex items-baseline gap-2">
                    <p className="text-2xl font-bold text-primary">
                      R$ {parseFloat(product.price).toFixed(2)}
                    </p>
                  </div>
                  
                  <p className="text-xs text-green-600 font-medium">✓ {product.quantity} em estoque</p>
                </CardContent>

                <CardFooter className="p-4 pt-0 flex gap-2">
                  <Button
                    className="flex-1 cursor-pointer shadow-sm hover:shadow-md transition-shadow"
                    onClick={() => addToCart(product.id)}
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
            <div className="flex justify-center gap-2 mt-12">
              {Array.from({ length: products.meta.last_page }, (_, i) => i + 1).map((page) => (
                <Button
                  key={page}
                  variant={page === products.meta.current_page ? 'default' : 'outline'}
                  size="sm"
                  className="cursor-pointer min-w-[40px]"
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
