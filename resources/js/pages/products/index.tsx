import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Trash2, Edit, Eye, Plus, Search, Package } from 'lucide-react';

interface Product {
  id: number;
  name: string;
  slug: string;
  description?: string;
  image?: string;
  price: number;
  quantity: number;
  active: boolean;
  category?: {
    id: number;
    name: string;
  };
  tags?: Array<{
    id: number;
    name: string;
  }>;
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
  filters: {
    search?: string;
  };
}

export default function ProductsIndex({ products, filters }: Props) {
  const [search, setSearch] = useState(filters.search || '');

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    router.get('/products', { search }, { preserveState: true });
  };

  const handleDelete = (id: number) => {
    if (confirm('Tem certeza que deseja excluir este produto?')) {
      router.delete(`/products/${id}`);
    }
  };

  return (
    <AppLayout>
      <Head title="Produtos" />
      
      <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div className="max-w-7xl mx-auto p-6 space-y-6">
          {/* Header */}
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <h1 className="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                Produtos
              </h1>
              <p className="text-gray-600 mt-1">Gerencie seu catálogo de produtos</p>
            </div>
            <Link href="/products/create">
              <Button className="cursor-pointer shadow-lg hover:shadow-xl transition-shadow">
                <Plus className="h-4 w-4 mr-2" />
                Novo Produto
              </Button>
            </Link>
          </div>

          {/* Search Bar */}
          <Card className="border-0 shadow-md">
            <CardContent className="p-6">
              <form onSubmit={handleSearch} className="flex gap-3">
                <div className="relative flex-1">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" />
                  <Input
                    placeholder="Buscar produtos por nome..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className="pl-10 h-11"
                  />
                </div>
                <Button type="submit" className="h-11 px-6">Buscar</Button>
              </form>
            </CardContent>
          </Card>

          {/* Products Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {products.data.map((product) => (
              <Card key={product.id} className="group hover:shadow-xl transition-all duration-300 border-0 shadow-md overflow-hidden">
                <div className="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                  {product.image ? (
                    <img 
                      src={product.image} 
                      alt={product.name} 
                      className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                    />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center">
                      <Package className="h-16 w-16 text-gray-300" />
                    </div>
                  )}
                  <div className="absolute top-3 right-3 flex gap-2">
                    <Badge variant={product.active ? 'default' : 'secondary'} className="shadow-sm">
                      {product.active ? 'Ativo' : 'Inativo'}
                    </Badge>
                  </div>
                </div>
                
                <CardContent className="p-4 space-y-3">
                  <div>
                    <h3 className="font-semibold text-lg line-clamp-1 mb-1">{product.name}</h3>
                    {product.category && (
                      <Badge variant="outline" className="text-xs">
                        {product.category.name}
                      </Badge>
                    )}
                  </div>
                  
                  {product.description && (
                    <p className="text-sm text-gray-600 line-clamp-2 min-h-[40px]">
                      {product.description}
                    </p>
                  )}
                  
                  <div className="flex justify-between items-center pt-2 border-t">
                    <div>
                      <p className="text-xs text-gray-500">Preço</p>
                      <p className="text-xl font-bold text-green-600">
                        R$ {product.price.toFixed(2)}
                      </p>
                    </div>
                    <div className="text-right">
                      <p className="text-xs text-gray-500">Estoque</p>
                      <p className="text-lg font-semibold text-gray-700">
                        {product.quantity}
                      </p>
                    </div>
                  </div>
                  
                  {product.tags && product.tags.length > 0 && (
                    <div className="flex flex-wrap gap-1">
                      {product.tags.slice(0, 2).map((tag) => (
                        <Badge key={tag.id} variant="outline" className="text-xs">
                          {tag.name}
                        </Badge>
                      ))}
                      {product.tags.length > 2 && (
                        <Badge variant="outline" className="text-xs">
                          +{product.tags.length - 2}
                        </Badge>
                      )}
                    </div>
                  )}
                  
                  <div className="flex gap-2 pt-2">
                    <Link href={`/products/${product.id}`} className="flex-1">
                      <Button variant="outline" size="sm" className="w-full cursor-pointer">
                        <Eye className="h-3.5 w-3.5 mr-1" />
                        Ver
                      </Button>
                    </Link>
                    <Link href={`/products/${product.id}/edit`} className="flex-1">
                      <Button variant="outline" size="sm" className="w-full cursor-pointer">
                        <Edit className="h-3.5 w-3.5 mr-1" />
                        Editar
                      </Button>
                    </Link>
                    <Button 
                      variant="destructive" 
                      size="sm"
                      onClick={() => handleDelete(product.id)}
                      className="px-3 cursor-pointer"
                    >
                      <Trash2 className="h-3.5 w-3.5" />
                    </Button>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>

          {/* Empty State */}
          {products.data.length === 0 && (
            <Card className="border-0 shadow-md">
              <CardContent className="p-12 text-center">
                <Package className="h-16 w-16 text-gray-300 mx-auto mb-4" />
                <h3 className="text-xl font-semibold text-gray-700 mb-2">Nenhum produto encontrado</h3>
                <p className="text-gray-500 mb-6">Comece criando seu primeiro produto</p>
                <Link href="/products/create">
                  <Button className="cursor-pointer">
                    <Plus className="h-4 w-4 mr-2" />
                    Criar Primeiro Produto
                  </Button>
                </Link>
              </CardContent>
            </Card>
          )}

          {/* Pagination */}
          {products.meta.last_page > 1 && (
            <div className="flex justify-center gap-2">
              {Array.from({ length: products.meta.last_page }, (_, i) => i + 1).map((page) => (
                <Button
                  key={page}
                  variant={page === products.meta.current_page ? 'default' : 'outline'}
                  size="sm"
                  onClick={() => router.get('/products', { ...filters, page })}
                  className="min-w-[40px]"
                >
                  {page}
                </Button>
              ))}
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}