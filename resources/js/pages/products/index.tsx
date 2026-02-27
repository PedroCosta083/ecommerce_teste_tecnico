import { Head, Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
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
    category_id?: number;
  };
  categories: Array<{
    id: number;
    name: string;
  }>;
}

export default function ProductsIndex({ products, filters, categories }: Props) {
  const [search, setSearch] = useState(filters.search || '');
  const [categoryId, setCategoryId] = useState(filters.category_id?.toString() || 'all');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(false);
  }, []);

  useEffect(() => {
    setLoading(true);
    const timer = setTimeout(() => {
      const params: any = { search };
      if (categoryId !== 'all') {
        params.category_id = categoryId;
      }
      router.get('/products', params, { 
        preserveState: true, 
        preserveScroll: true,
        onFinish: () => setLoading(false)
      });
    }, 300);

    return () => clearTimeout(timer);
  }, [search, categoryId]);

  const handleDelete = (id: number) => {
    if (confirm('Tem certeza que deseja excluir este produto?')) {
      router.delete(`/products/${id}`);
    }
  };

  return (
    <AppLayout>
      <Head title="Produtos" />
      
      <div className="min-h-screen bg-background">
        <div className="max-w-7xl mx-auto p-6 space-y-6">
          {/* Header */}
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <h1 className="text-4xl font-bold text-foreground">
                Produtos
              </h1>
              <p className="text-muted-foreground mt-1">Gerencie seu catálogo de produtos</p>
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
              <div className="flex gap-3">
                <div className="relative flex-1">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" />
                  <Input
                    placeholder="Buscar produtos por nome..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className="pl-10 h-11"
                  />
                </div>
                <Select value={categoryId} onValueChange={setCategoryId}>
                  <SelectTrigger className="w-[220px] h-11">
                    <SelectValue placeholder="Categoria" />
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
            </CardContent>
          </Card>

          {/* Products Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {loading ? (
              Array.from({ length: 8 }).map((_, i) => (
                <Card key={i} className="border-0 shadow-md overflow-hidden">
                  <Skeleton className="h-48 w-full" />
                  <CardContent className="p-4 space-y-3">
                    <Skeleton className="h-6 w-3/4" />
                    <Skeleton className="h-4 w-1/2" />
                    <Skeleton className="h-10 w-full" />
                    <Skeleton className="h-8 w-full" />
                    <div className="flex gap-2 pt-2">
                      <Skeleton className="h-9 flex-1" />
                      <Skeleton className="h-9 flex-1" />
                      <Skeleton className="h-9 w-12" />
                    </div>
                  </CardContent>
                </Card>
              ))
            ) : (
              products.data.map((product) => (
              <Card key={product.id} className="group hover:shadow-xl transition-all duration-300 border-0 shadow-md overflow-hidden">
                <div className="relative h-48 bg-muted overflow-hidden">
                  {product.image ? (
                    <img 
                      src={product.image} 
                      alt={product.name} 
                      className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                    />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center">
                      <Package className="h-16 w-16 text-muted-foreground" />
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
                    <p className="text-sm text-muted-foreground line-clamp-2 min-h-[40px]">
                      {product.description}
                    </p>
                  )}
                  
                  <div className="flex justify-between items-center pt-2 border-t">
                    <div>
                      <p className="text-xs text-muted-foreground">Preço</p>
                      <p className="text-xl font-bold text-green-600 dark:text-green-400">
                        R$ {product.price.toFixed(2)}
                      </p>
                    </div>
                    <div className="text-right">
                      <p className="text-xs text-muted-foreground">Estoque</p>
                      <p className="text-lg font-semibold text-foreground">
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
                      <Button variant="outline" size="sm" className="w-full cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-950">
                        <Eye className="h-4 w-4" />
                      </Button>
                    </Link>
                    <Link href={`/products/${product.id}/edit`} className="flex-1">
                      <Button variant="outline" size="sm" className="w-full cursor-pointer hover:bg-amber-50 dark:hover:bg-amber-950">
                        <Edit className="h-4 w-4" />
                      </Button>
                    </Link>
                    <Button 
                      variant="outline" 
                      size="sm"
                      onClick={() => handleDelete(product.id)}
                      className="flex-1 cursor-pointer text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 hover:border-red-300 dark:hover:border-red-800"
                    >
                      <Trash2 className="h-4 w-4" />
                    </Button>
                  </div>
                </CardContent>
              </Card>
              ))
            )}
          </div>

          {/* Empty State */}
          {products.data.length === 0 && (
            <Card className="border-0 shadow-md">
              <CardContent className="p-12 text-center">
                <Package className="h-16 w-16 text-muted-foreground mx-auto mb-4" />
                <h3 className="text-xl font-semibold text-foreground mb-2">Nenhum produto encontrado</h3>
                <p className="text-muted-foreground mb-6">Comece criando seu primeiro produto</p>
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