import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Product {
  id: number;
  name: string;
  slug: string;
  description?: string;
  price: number;
  cost_price: number;
  quantity: number;
  min_quantity: number;
  active: boolean;
  category?: {
    id: number;
    name: string;
  };
  tags?: Array<{
    id: number;
    name: string;
  }>;
  created_at: string;
  updated_at: string;
}

interface Props {
  product: Product;
}

export default function ProductsShow({ product }: Props) {
  return (
    <AppLayout>
      <Head title={`Produto: ${product.name}`} />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">{product.name}</h1>
          <div className="flex gap-2">
            <Link href={`/products/${product.id}/edit`}>
              <Button>Editar</Button>
            </Link>
            <Link href="/products">
              <Button variant="outline">Voltar</Button>
            </Link>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Card>
            <CardHeader>
              <CardTitle>Informações Básicas</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <label className="text-sm font-medium text-gray-500">Nome</label>
                <p className="text-lg">{product.name}</p>
              </div>
              
              <div>
                <label className="text-sm font-medium text-gray-500">Slug</label>
                <p className="text-sm text-gray-700">{product.slug}</p>
              </div>
              
              {product.description && (
                <div>
                  <label className="text-sm font-medium text-gray-500">Descrição</label>
                  <p className="text-gray-700">{product.description}</p>
                </div>
              )}
              
              <div>
                <label className="text-sm font-medium text-gray-500">Status</label>
                <div className="mt-1">
                  <Badge variant={product.active ? 'default' : 'secondary'}>
                    {product.active ? 'Ativo' : 'Inativo'}
                  </Badge>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Preços e Estoque</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="text-sm font-medium text-gray-500">Preço de Venda</label>
                  <p className="text-lg font-semibold text-green-600">
                    R$ {product.price.toFixed(2)}
                  </p>
                </div>
                
                <div>
                  <label className="text-sm font-medium text-gray-500">Preço de Custo</label>
                  <p className="text-lg">R$ {product.cost_price.toFixed(2)}</p>
                </div>
              </div>
              
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="text-sm font-medium text-gray-500">Quantidade</label>
                  <p className="text-lg font-semibold">{product.quantity}</p>
                </div>
                
                <div>
                  <label className="text-sm font-medium text-gray-500">Quantidade Mínima</label>
                  <p className="text-lg">{product.min_quantity}</p>
                </div>
              </div>
              
              {product.quantity <= product.min_quantity && (
                <div className="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                  <p className="text-sm text-yellow-800">
                    ⚠️ Estoque baixo! Quantidade atual está no limite mínimo.
                  </p>
                </div>
              )}
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Categoria</CardTitle>
            </CardHeader>
            <CardContent>
              {product.category ? (
                <Badge variant="outline" className="text-sm">
                  {product.category.name}
                </Badge>
              ) : (
                <p className="text-gray-500">Nenhuma categoria definida</p>
              )}
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Tags</CardTitle>
            </CardHeader>
            <CardContent>
              {product.tags && product.tags.length > 0 ? (
                <div className="flex flex-wrap gap-2">
                  {product.tags.map((tag) => (
                    <Badge key={tag.id} variant="outline" className="text-xs">
                      {tag.name}
                    </Badge>
                  ))}
                </div>
              ) : (
                <p className="text-gray-500">Nenhuma tag definida</p>
              )}
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Informações do Sistema</CardTitle>
          </CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            <div>
              <label className="text-sm font-medium text-gray-500">Criado em</label>
              <p className="text-sm">{new Date(product.created_at).toLocaleString('pt-BR')}</p>
            </div>
            
            <div>
              <label className="text-sm font-medium text-gray-500">Atualizado em</label>
              <p className="text-sm">{new Date(product.updated_at).toLocaleString('pt-BR')}</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}