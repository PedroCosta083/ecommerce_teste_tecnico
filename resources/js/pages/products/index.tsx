import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Trash2 } from 'lucide-react';

interface Product {
  id: number;
  name: string;
  slug: string;
  description?: string;
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
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Produtos</h1>
          <Link href="/products/create">
            <Button className="cursor-pointer">Novo Produto</Button>
          </Link>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Filtros</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSearch} className="flex gap-4">
              <Input
                placeholder="Buscar produtos..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="flex-1"
              />
              <Button type="submit">Buscar</Button>
            </form>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {products.data.map((product) => (
            <Card key={product.id} className="hover:shadow-lg transition-shadow">
              <CardHeader>
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <CardTitle className="text-lg mb-2">{product.name}</CardTitle>
                    <div className="flex items-center gap-2 mb-2">
                      <Badge variant={product.active ? 'default' : 'secondary'}>
                        {product.active ? 'Ativo' : 'Inativo'}
                      </Badge>
                      {product.category && (
                        <Badge variant="outline">
                          {product.category.name}
                        </Badge>
                      )}
                    </div>
                  </div>
                </div>
              </CardHeader>
              
              <CardContent className="space-y-4">
                {product.description && (
                  <p className="text-sm text-gray-600 line-clamp-2">
                    {product.description}
                  </p>
                )}
                
                <div className="flex justify-between items-center text-sm">
                  <span className="font-semibold text-green-600">
                    R$ {product.price.toFixed(2)}
                  </span>
                  <span className="text-gray-500">
                    Estoque: {product.quantity}
                  </span>
                </div>
                
                {product.tags && product.tags.length > 0 && (
                  <div className="flex flex-wrap gap-1">
                    {product.tags.slice(0, 3).map((tag) => (
                      <Badge key={tag.id} variant="outline" className="text-xs">
                        {tag.name}
                      </Badge>
                    ))}
                    {product.tags.length > 3 && (
                      <Badge variant="outline" className="text-xs">
                        +{product.tags.length - 3}
                      </Badge>
                    )}
                  </div>
                )}
                
                <div className="flex gap-2 pt-2">
                  <Link href={`/products/${product.id}`} className="flex-1">
                    <Button variant="outline" size="sm" className="w-full cursor-pointer">
                      Ver
                    </Button>
                  </Link>
                  <Link href={`/products/${product.id}/edit`} className="flex-1">
                    <Button variant="outline" size="sm" className="w-full cursor-pointer">
                      Editar
                    </Button>
                  </Link>
                  <Button 
                    variant="destructive" 
                    size="sm"
                    onClick={() => handleDelete(product.id)}
                    className="px-3 cursor-pointer"
                  >
                    <Trash2 className="h-4 w-4" />
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {products.data.length === 0 && (
          <Card>
            <CardContent className="p-12 text-center">
              <p className="text-gray-500 mb-4">Nenhum produto encontrado.</p>
              <Link href="/products/create">
                <Button className="cursor-pointer">Criar Primeiro Produto</Button>
              </Link>
            </CardContent>
          </Card>
        )}

        {products.meta.last_page > 1 && (
          <div className="flex justify-center gap-2">
            {Array.from({ length: products.meta.last_page }, (_, i) => i + 1).map((page) => (
              <Button
                key={page}
                variant={page === products.meta.current_page ? 'default' : 'outline'}
                size="sm"
                onClick={() => router.get('/products', { ...filters, page })}
              >
                {page}
              </Button>
            ))}
          </div>
        )}
      </div>
    </AppLayout>
  );
}