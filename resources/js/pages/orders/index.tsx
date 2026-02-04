import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Eye } from 'lucide-react';

interface OrderItem {
  id: number;
  quantity: number;
  unit_price: number;
  total_price: number;
  product: {
    id: number;
    name: string;
  };
}

interface Order {
  id: number;
  status: string;
  total: number;
  subtotal: number;
  tax: number;
  shipping_cost: number;
  user: {
    id: number;
    name: string;
    email: string;
  };
  items: OrderItem[];
  created_at: string;
}

interface Props {
  orders: {
    data: Order[];
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

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  processing: 'bg-blue-100 text-blue-800',
  shipped: 'bg-purple-100 text-purple-800',
  delivered: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
};

const statusLabels = {
  pending: 'Pendente',
  processing: 'Processando',
  shipped: 'Enviado',
  delivered: 'Entregue',
  cancelled: 'Cancelado',
};

export default function OrdersIndex({ orders, filters }: Props) {
  const [search, setSearch] = useState(filters.search || '');

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    router.get('/orders', { search }, { preserveState: true });
  };

  return (
    <AppLayout>
      <Head title="Pedidos" />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Pedidos</h1>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Filtros</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSearch} className="flex gap-4">
              <Input
                placeholder="Buscar pedidos..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="flex-1"
              />
              <Button type="submit">Buscar</Button>
            </form>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {orders.data.map((order) => (
            <Card key={order.id} className="hover:shadow-lg transition-shadow">
              <CardHeader>
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <CardTitle className="text-lg mb-2">
                      Pedido #{order.id}
                    </CardTitle>
                    <div className="flex items-center gap-2 mb-2">
                      <Badge 
                        className={statusColors[order.status as keyof typeof statusColors]}
                      >
                        {statusLabels[order.status as keyof typeof statusLabels]}
                      </Badge>
                    </div>
                  </div>
                </div>
              </CardHeader>
              
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <p className="text-sm">
                    <span className="font-medium">Cliente:</span> {order.user.name}
                  </p>
                  <p className="text-sm text-gray-600">{order.user.email}</p>
                </div>
                
                <div className="space-y-1">
                  <p className="text-sm">
                    <span className="font-medium">Items:</span> {order.items?.length || 0}
                  </p>
                  <div className="text-xs text-gray-500">
                    {order.items?.slice(0, 2).map((item) => (
                      <div key={item.id}>
                        {item.quantity}x {item.product?.name || 'Produto'}
                      </div>
                    ))}
                    {(order.items?.length || 0) > 2 && (
                      <div>+{(order.items?.length || 0) - 2} mais...</div>
                    )}
                  </div>
                </div>
                
                <div className="border-t pt-2">
                  <div className="flex justify-between text-sm">
                    <span>Subtotal:</span>
                    <span>R$ {order.subtotal.toFixed(2)}</span>
                  </div>
                  {order.tax > 0 && (
                    <div className="flex justify-between text-sm">
                      <span>Taxa:</span>
                      <span>R$ {order.tax.toFixed(2)}</span>
                    </div>
                  )}
                  {order.shipping_cost > 0 && (
                    <div className="flex justify-between text-sm">
                      <span>Frete:</span>
                      <span>R$ {order.shipping_cost.toFixed(2)}</span>
                    </div>
                  )}
                  <div className="flex justify-between font-semibold text-green-600 border-t pt-1">
                    <span>Total:</span>
                    <span>R$ {order.total.toFixed(2)}</span>
                  </div>
                </div>
                
                <div className="text-xs text-gray-500">
                  {new Date(order.created_at).toLocaleDateString('pt-BR')}
                </div>
                
                <div className="flex gap-2 pt-2">
                  <Link href={`/orders/${order.id}`} className="flex-1">
                    <Button variant="outline" size="sm" className="w-full cursor-pointer">
                      <Eye className="h-4 w-4 mr-1" />
                      Ver Detalhes
                    </Button>
                  </Link>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {orders.data.length === 0 && (
          <Card>
            <CardContent className="p-12 text-center">
              <p className="text-gray-500 mb-4">Nenhum pedido encontrado.</p>
            </CardContent>
          </Card>
        )}

        {orders.meta.last_page > 1 && (
          <div className="flex justify-center gap-2">
            {Array.from({ length: orders.meta.last_page }, (_, i) => i + 1).map((page) => (
              <Button
                key={page}
                variant={page === orders.meta.current_page ? 'default' : 'outline'}
                size="sm"
                onClick={() => router.get('/orders', { ...filters, page })}
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