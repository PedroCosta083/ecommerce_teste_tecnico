import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { AlertTriangle, ArrowLeft, Package } from 'lucide-react';

interface Product {
  id: number;
  name: string;
  quantity: number;
  min_quantity: number | null;
  price: number;
  category?: {
    id: number;
    name: string;
  };
}

interface Props {
  products: Product[];
}

export default function LowStockReport({ products }: Props) {
  return (
    <AppLayout>
      <Head title="Relatório de Estoque Baixo" />
      
      <div className="min-h-screen bg-background">
        <div className="max-w-7xl mx-auto p-6 space-y-6">
          <div className="flex items-center gap-4">
            <Link href="/reports">
              <Button variant="outline" size="sm">
                <ArrowLeft className="h-4 w-4 mr-2" />
                Voltar
              </Button>
            </Link>
            <div>
              <h1 className="text-4xl font-bold text-red-600 dark:text-red-400">
                Estoque Baixo
              </h1>
              <p className="text-muted-foreground mt-1">Produtos que precisam de reposição</p>
            </div>
          </div>

          {products.length === 0 ? (
            <Card className="border-0 shadow-md">
              <CardContent className="p-12 text-center">
                <Package className="h-16 w-16 text-green-500 mx-auto mb-4" />
                <h3 className="text-xl font-semibold text-foreground mb-2">Estoque OK!</h3>
                <p className="text-muted-foreground">Todos os produtos estão com estoque adequado</p>
              </CardContent>
            </Card>
          ) : (
            <Card className="border-0 shadow-md">
              <CardContent className="p-6">
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b">
                        <th className="text-left py-3 px-4 font-semibold text-foreground">Produto</th>
                        <th className="text-left py-3 px-4 font-semibold text-foreground">Categoria</th>
                        <th className="text-center py-3 px-4 font-semibold text-foreground">Estoque Atual</th>
                        <th className="text-center py-3 px-4 font-semibold text-foreground">Estoque Mínimo</th>
                        <th className="text-center py-3 px-4 font-semibold text-foreground">Status</th>
                        <th className="text-right py-3 px-4 font-semibold text-foreground">Ações</th>
                      </tr>
                    </thead>
                    <tbody>
                      {products.map((product) => {
                        const minQty = product.min_quantity || 10;
                        const percentage = (product.quantity / minQty) * 100;
                        const isCritical = percentage <= 50;
                        
                        return (
                          <tr key={product.id} className="border-b hover:bg-muted/50 transition-colors">
                            <td className="py-4 px-4">
                              <div className="font-medium text-foreground">{product.name}</div>
                            </td>
                            <td className="py-4 px-4">
                              {product.category && (
                                <Badge variant="outline">{product.category.name}</Badge>
                              )}
                            </td>
                            <td className="py-4 px-4 text-center">
                              <span className={`font-bold ${isCritical ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400'}`}>
                                {product.quantity}
                              </span>
                            </td>
                            <td className="py-4 px-4 text-center text-muted-foreground">
                              {minQty}
                            </td>
                            <td className="py-4 px-4 text-center">
                              <Badge variant={isCritical ? 'destructive' : 'secondary'}>
                                <AlertTriangle className="h-3 w-3 mr-1" />
                                {isCritical ? 'Crítico' : 'Baixo'}
                              </Badge>
                            </td>
                            <td className="py-4 px-4 text-right">
                              <Link href={`/products/${product.id}/edit`}>
                                <Button variant="outline" size="sm">
                                  Repor Estoque
                                </Button>
                              </Link>
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                  </table>
                </div>
              </CardContent>
            </Card>
          )}

          <Card className="border-0 shadow-md bg-blue-50 dark:bg-blue-950/30">
            <CardContent className="p-6">
              <div className="flex items-start gap-3">
                <AlertTriangle className="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                <div>
                  <h3 className="font-semibold text-blue-900 dark:text-blue-300 mb-1">Sobre este relatório</h3>
                  <p className="text-sm text-blue-800 dark:text-blue-400">
                    Este relatório mostra produtos com estoque igual ou abaixo do estoque mínimo configurado. 
                    Produtos sem estoque mínimo definido aparecem quando têm 10 unidades ou menos.
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </AppLayout>
  );
}
