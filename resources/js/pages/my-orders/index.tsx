import { Head, Link, router } from '@inertiajs/react';
import { Package } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';

interface OrderItem {
  id: number;
  product: {
    id: number;
    name: string;
  };
  quantity: number;
  unit_price: string;
}

interface Order {
  id: number;
  status: string;
  total: string;
  created_at: string;
  order_items: OrderItem[];
}

interface Props {
  orders: Order[];
  auth: { user: any };
}

const statusMap: Record<string, { label: string; color: string }> = {
  pending: { label: 'Pendente', color: 'bg-yellow-100 text-yellow-800' },
  processing: { label: 'Processando', color: 'bg-blue-100 text-blue-800' },
  shipped: { label: 'Enviado', color: 'bg-purple-100 text-purple-800' },
  delivered: { label: 'Entregue', color: 'bg-green-100 text-green-800' },
};

export default function MyOrdersIndex({ orders, auth }: Props) {
  return (
    <AppLayout>
      <Head title="Meus Pedidos" />
      
      <div className="p-6">
        <h1 className="text-3xl font-bold mb-8">Meus Pedidos</h1>

        {orders.length === 0 ? (
          <Card>
            <CardContent className="p-12 text-center">
              <Package className="h-16 w-16 mx-auto text-gray-400 mb-4" />
              <p className="text-gray-600 mb-4">Você ainda não fez nenhum pedido</p>
              <Link href="/">
                <Button className="cursor-pointer">Começar a Comprar</Button>
              </Link>
            </CardContent>
          </Card>
        ) : (
          <div className="space-y-4">
            {orders.map((order) => (
              <Card key={order.id} className="hover:shadow-lg transition-shadow cursor-pointer" onClick={() => router.visit(`/meus-pedidos/${order.id}`)}>
                <CardContent className="p-6">
                  <div className="flex items-start justify-between mb-4">
                    <div>
                      <p className="text-sm text-gray-600">Pedido #{order.id}</p>
                      <p className="text-sm text-gray-500">
                        {new Date(order.created_at).toLocaleDateString('pt-BR')}
                      </p>
                    </div>
                    <Badge className={statusMap[order.status]?.color || ''}>
                      {statusMap[order.status]?.label || order.status}
                    </Badge>
                  </div>

                  <div className="space-y-2 mb-4">
                    {order.order_items?.slice(0, 2).map((item) => (
                      <p key={item.id} className="text-sm text-gray-700">
                        {item.quantity}x {item.product.name}
                      </p>
                    ))}
                    {order.order_items && order.order_items.length > 2 && (
                      <p className="text-sm text-gray-500">
                        +{order.order_items.length - 2} item(s)
                      </p>
                    )}
                  </div>

                  <div className="flex items-center justify-between pt-4 border-t">
                    <span className="text-lg font-bold text-green-600">
                      R$ {parseFloat(order.total).toFixed(2)}
                    </span>
                    <Button variant="outline" size="sm" className="cursor-pointer">
                      Ver Detalhes
                    </Button>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>
    </AppLayout>
  );
}
