import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

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
  shipping_address: any;
  billing_address: any;
  notes?: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
  order_items: OrderItem[];
  created_at: string;
  updated_at: string;
}

interface Props {
  order: Order;
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

export default function OrdersShow({ order }: Props) {
  const [currentStatus, setCurrentStatus] = useState(order.status);
  const [updating, setUpdating] = useState(false);

  const handleStatusUpdate = (newStatus: string) => {
    if (newStatus === currentStatus) return;
    
    setUpdating(true);
    router.put(`/orders/${order.id}/status`, { status: newStatus }, {
      onSuccess: () => {
        setCurrentStatus(newStatus);
        setUpdating(false);
      },
      onError: () => {
        setUpdating(false);
      }
    });
  };

  const formatAddress = (address: any) => {
    if (!address) return null;
    
    if (typeof address === 'string') {
      try {
        address = JSON.parse(address);
      } catch {
        return address;
      }
    }
    
    const parts = [];
    if (address.street) parts.push(address.street);
    if (address.city) parts.push(address.city);
    if (address.state) parts.push(address.state);
    if (address.zip) parts.push(`CEP: ${address.zip}`);
    
    return parts.join(', ') || 'Endereço não informado';
  };
  return (
    <AppLayout>
      <Head title={`Pedido #${order.id}`} />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Pedido #{order.id}</h1>
          <Link href="/orders">
            <Button variant="outline">Voltar</Button>
          </Link>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Card>
            <CardHeader>
              <CardTitle>Informações do Pedido</CardTitle>
            </CardHeader>

            <CardContent className="space-y-4">
              <div>
                <label className="text-sm font-medium text-gray-500">Status</label>
                <div className="mt-2 flex items-center gap-3">
                  <Badge className={statusColors[currentStatus as keyof typeof statusColors]}>
                    {statusLabels[currentStatus as keyof typeof statusLabels]}
                  </Badge>
                  <Select value={currentStatus} onValueChange={handleStatusUpdate} disabled={updating}>
                    <SelectTrigger className="w-40">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="pending">Pendente</SelectItem>
                      <SelectItem value="processing">Processando</SelectItem>
                      <SelectItem value="shipped">Enviado</SelectItem>
                      <SelectItem value="delivered">Entregue</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              
              <div>
                <label className="text-sm font-medium text-gray-500">Cliente</label>
                <p className="text-lg">{order.user.name}</p>
                <p className="text-sm text-gray-600">{order.user.email}</p>
              </div>
              
              <div>
                <label className="text-sm font-medium text-gray-500">Criado em</label>
                <p className="text-sm">{new Date(order.created_at).toLocaleString('pt-BR')}</p>
              </div>

              {order.notes && (
                <div>
                  <label className="text-sm font-medium text-gray-500">Observações</label>
                  <p className="text-sm">{order.notes}</p>
                </div>
              )}
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Resumo Financeiro</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              <div className="flex justify-between">
                <span>Subtotal:</span>
                <span>R$ {order.subtotal.toFixed(2)}</span>
              </div>
              
              {order.tax > 0 && (
                <div className="flex justify-between">
                  <span>Taxa:</span>
                  <span>R$ {order.tax.toFixed(2)}</span>
                </div>
              )}
              
              {order.shipping_cost > 0 && (
                <div className="flex justify-between">
                  <span>Frete:</span>
                  <span>R$ {order.shipping_cost.toFixed(2)}</span>
                </div>
              )}
              
              <div className="border-t pt-3">
                <div className="flex justify-between font-semibold text-lg text-green-600">
                  <span>Total:</span>
                  <span>R$ {order.total.toFixed(2)}</span>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Itens do Pedido</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {order.order_items?.map((item) => (
                <div key={item.id} className="flex justify-between items-center p-4 border rounded-lg">
                  <div className="flex-1">
                    <h4 className="font-medium">{item.product.name}</h4>
                    <p className="text-sm text-gray-600">
                      Quantidade: {item.quantity} × R$ {item.unit_price.toFixed(2)}
                    </p>
                  </div>
                  <div className="text-right">
                    <p className="font-semibold">R$ {item.total_price.toFixed(2)}</p>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {(order.shipping_address || order.billing_address) && (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {order.shipping_address && (
              <Card>
                <CardHeader>
                  <CardTitle>Endereço de Entrega</CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-sm">{formatAddress(order.shipping_address)}</p>
                </CardContent>
              </Card>
            )}

            {order.billing_address && (
              <Card>
                <CardHeader>
                  <CardTitle>Endereço de Cobrança</CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-sm">{formatAddress(order.billing_address)}</p>
                </CardContent>
              </Card>
            )}
          </div>
        )}
      </div>
    </AppLayout>
  );
}