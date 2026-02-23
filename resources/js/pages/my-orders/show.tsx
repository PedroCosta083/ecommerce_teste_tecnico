import { Head, Link, router } from '@inertiajs/react';
import { MapPin, ArrowLeft } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';

interface OrderItem {
  id: number;
  product: {
    id: number;
    name: string;
    price: string;
  };
  quantity: number;
  unit_price: string;
  total_price: string;
}

interface Order {
  id: number;
  status: string;
  total: string;
  subtotal: string;
  created_at: string;
  shipping_address: {
    street: string;
    number: string;
    complement?: string;
    neighborhood: string;
    city: string;
    state: string;
    zip_code: string;
  };
  notes?: string;
  order_items: OrderItem[];
}

interface Props {
  order: Order;
  auth: { user: any };
}

const statusMap: Record<string, { label: string; color: string }> = {
  pending: { label: 'Pendente', color: 'bg-yellow-100 text-yellow-800' },
  processing: { label: 'Processando', color: 'bg-blue-100 text-blue-800' },
  shipped: { label: 'Enviado', color: 'bg-purple-100 text-purple-800' },
  delivered: { label: 'Entregue', color: 'bg-green-100 text-green-800' },
};

export default function MyOrdersShow({ order, auth }: Props) {
  return (
    <AppLayout>
      <Head title={`Pedido #${order.id}`} />
      
      <div className="p-6">
        <Button
          variant="ghost"
          size="sm"
          className="mb-6 cursor-pointer"
          onClick={() => router.visit('/meus-pedidos')}
        >
          <ArrowLeft className="h-4 w-4 mr-2" />
          Voltar
        </Button>

        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold">Pedido #{order.id}</h1>
            <p className="text-gray-600">
              Realizado em {new Date(order.created_at).toLocaleDateString('pt-BR')}
            </p>
          </div>
          <Badge className={`${statusMap[order.status]?.color || ''} text-lg px-4 py-2`}>
            {statusMap[order.status]?.label || order.status}
          </Badge>
        </div>

        <div className="grid lg:grid-cols-3 gap-6">
          <div className="lg:col-span-2 space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Itens do Pedido</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {order.order_items.map((item) => (
                  <div key={item.id} className="flex justify-between items-start pb-4 border-b last:border-0">
                    <div className="flex-1">
                      <p className="font-semibold">{item.product.name}</p>
                      <p className="text-sm text-gray-600">
                        Quantidade: {item.quantity} x R$ {parseFloat(item.unit_price).toFixed(2)}
                      </p>
                    </div>
                    <p className="font-bold">
                      R$ {parseFloat(item.total_price).toFixed(2)}
                    </p>
                  </div>
                ))}
              </CardContent>
            </Card>

            {order.shipping_address && (
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <MapPin className="h-5 w-5" />
                    Endereço de Entrega
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <p>{order.shipping_address.street}, {order.shipping_address.number}</p>
                  {order.shipping_address.complement && (
                    <p>{order.shipping_address.complement}</p>
                  )}
                  <p>{order.shipping_address.neighborhood}</p>
                  <p>{order.shipping_address.city} - {order.shipping_address.state}</p>
                  <p>CEP: {order.shipping_address.zip_code}</p>
                </CardContent>
              </Card>
            )}

            {order.notes && (
              <Card>
                <CardHeader>
                  <CardTitle>Observações</CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-gray-700">{order.notes}</p>
                </CardContent>
              </Card>
            )}
          </div>

          <div>
            <Card>
              <CardHeader>
                <CardTitle>Resumo</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                <div className="flex justify-between text-sm">
                  <span>Subtotal</span>
                  <span>R$ {parseFloat(order.subtotal).toFixed(2)}</span>
                </div>
                <div className="flex justify-between text-lg font-bold border-t pt-3">
                  <span>Total</span>
                  <span className="text-green-600">R$ {parseFloat(order.total).toFixed(2)}</span>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
