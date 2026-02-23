import { Head, Link } from '@inertiajs/react';
import { CheckCircle, Package } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

interface Order {
  id: number;
  total: string;
  created_at: string;
}

interface Props {
  order: Order;
}

export default function CheckoutSuccess({ order }: Props) {
  return (
    <>
      <Head title="Pedido Realizado" />
      
      <div className="min-h-screen bg-background flex items-center justify-center p-4">
        <Card className="max-w-md w-full">
          <CardContent className="pt-6 text-center space-y-6">
            <div className="flex justify-center">
              <CheckCircle className="h-20 w-20 text-green-500" />
            </div>

            <div>
              <h1 className="text-2xl font-bold mb-2 text-foreground">Pedido Realizado!</h1>
              <p className="text-muted-foreground">
                Seu pedido foi recebido e está sendo processado.
              </p>
            </div>

            <div className="bg-muted p-4 rounded-lg space-y-2">
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Número do Pedido:</span>
                <span className="font-semibold text-foreground">#{order.id}</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Total:</span>
                <span className="font-semibold text-green-600 dark:text-green-400">
                  R$ {parseFloat(order.total).toFixed(2)}
                </span>
              </div>
            </div>

            <div className="flex flex-col gap-4">
              <Link href="/meus-pedidos">
                <Button className="w-full cursor-pointer" size="lg">
                  <Package className="h-5 w-5 mr-2" />
                  Ver Meus Pedidos
                </Button>
              </Link>
              
              <Link href="/">
                <Button variant="outline" className="w-full cursor-pointer" size="lg">
                  Continuar Comprando
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>
      </div>
    </>
  );
}
