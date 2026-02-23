import { Head, Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, ArrowUp, ArrowDown, Package, TrendingUp, TrendingDown } from 'lucide-react';
import { Bar, Doughnut } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, ArcElement, Title, Tooltip, Legend } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, ArcElement, Title, Tooltip, Legend);

interface StockMovement {
  id: number;
  type: string;
  quantity: number;
  reason: string;
  created_at: string;
  product: {
    id: number;
    name: string;
  };
}

interface Props {
  movements: {
    data: StockMovement[];
    meta: {
      current_page: number;
      last_page: number;
      per_page: number;
      total: number;
    };
  };
  filters: {
    type?: string;
    product_id?: number;
    date_from?: string;
    date_to?: string;
    sort_by?: string;
    sort_order?: string;
  };
  products: Array<{
    id: number;
    name: string;
  }>;
}

export default function StockMovementsReport({ movements, filters, products }: Props) {
  const [typeFilter, setTypeFilter] = useState(filters.type || 'all');
  const [productFilter, setProductFilter] = useState(filters.product_id?.toString() || 'all');
  const [dateFrom, setDateFrom] = useState(filters.date_from || '');
  const [dateTo, setDateTo] = useState(filters.date_to || '');
  const [sortBy, setSortBy] = useState(filters.sort_by || 'created_at');
  const [sortOrder, setSortOrder] = useState(filters.sort_order || 'desc');

  const applyFilters = () => {
    const params: any = {};
    if (typeFilter !== 'all') params.type = typeFilter;
    if (productFilter !== 'all') params.product_id = productFilter;
    if (dateFrom) params.date_from = dateFrom;
    if (dateTo) params.date_to = dateTo;
    params.sort_by = sortBy;
    params.sort_order = sortOrder;
    router.get('/reports/stock-movements', params, { preserveState: true, preserveScroll: true });
  };

  useEffect(() => {
    const timer = setTimeout(() => applyFilters(), 300);
    return () => clearTimeout(timer);
  }, [typeFilter, productFilter, dateFrom, dateTo, sortBy, sortOrder]);

  const typeLabels: Record<string, string> = {
    entrada: 'Entrada',
    saida: 'Saída',
    venda: 'Venda',
    devolucao: 'Devolução',
    ajuste: 'Ajuste',
  };

  const getTypeIcon = (type: string) => {
    if (type === 'entrada' || type === 'devolucao') {
      return <ArrowUp className="h-4 w-4" />;
    }
    return <ArrowDown className="h-4 w-4" />;
  };

  const getTypeVariant = (type: string): "default" | "destructive" | "secondary" => {
    if (type === 'entrada' || type === 'devolucao') return 'default';
    if (type === 'venda' || type === 'saida') return 'destructive';
    return 'secondary';
  };

  const movementsByType = movements.data.reduce((acc, mov) => {
    acc[mov.type] = (acc[mov.type] || 0) + 1;
    return acc;
  }, {} as Record<string, number>);

  const totalEntradas = movements.data
    .filter(m => m.type === 'entrada' || m.type === 'devolucao')
    .reduce((sum, m) => sum + m.quantity, 0);

  const totalSaidas = movements.data
    .filter(m => m.type === 'saida' || m.type === 'venda')
    .reduce((sum, m) => sum + m.quantity, 0);

  const typeChartData = {
    labels: Object.keys(movementsByType).map(type => typeLabels[type] || type),
    datasets: [
      {
        data: Object.values(movementsByType),
        backgroundColor: [
          'rgba(70, 104, 91, 0.8)',
          'rgba(239, 68, 68, 0.8)',
          'rgba(251, 191, 36, 0.8)',
          'rgba(59, 130, 246, 0.8)',
          'rgba(139, 92, 246, 0.8)',
        ],
        borderWidth: 0,
      },
    ],
  };

  const topProducts = Object.entries(
    movements.data.reduce((acc, mov) => {
      const name = mov.product.name;
      acc[name] = (acc[name] || 0) + mov.quantity;
      return acc;
    }, {} as Record<string, number>)
  )
    .sort((a, b) => b[1] - a[1])
    .slice(0, 5);

  const productsChartData = {
    labels: topProducts.map(([name]) => name.length > 20 ? name.substring(0, 20) + '...' : name),
    datasets: [
      {
        label: 'Quantidade Movimentada',
        data: topProducts.map(([, qty]) => qty),
        backgroundColor: 'rgba(70, 104, 91, 0.8)',
        borderRadius: 8,
      },
    ],
  };

  return (
    <AppLayout>
      <Head title="Movimentações de Estoque" />
      
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
              <h1 className="text-4xl font-bold text-primary">
                Movimentações de Estoque
              </h1>
              <p className="text-muted-foreground mt-1">Histórico completo de entradas e saídas</p>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Card className="bg-gradient-to-br from-green-500 to-green-600 text-white">
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-green-100 text-sm font-medium">Total de Entradas</p>
                    <p className="text-3xl font-bold mt-2">{totalEntradas}</p>
                    <p className="text-green-100 text-sm mt-1">unidades</p>
                  </div>
                  <div className="bg-white/20 p-3 rounded-lg">
                    <TrendingUp className="h-8 w-8" />
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card className="bg-gradient-to-br from-red-500 to-red-600 text-white">
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-red-100 text-sm font-medium">Total de Saídas</p>
                    <p className="text-3xl font-bold mt-2">{totalSaidas}</p>
                    <p className="text-red-100 text-sm mt-1">unidades</p>
                  </div>
                  <div className="bg-white/20 p-3 rounded-lg">
                    <TrendingDown className="h-8 w-8" />
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Card className="border-0 shadow-md">
              <CardHeader>
                <CardTitle>Movimentações por Tipo</CardTitle>
              </CardHeader>
              <CardContent>
                <div style={{ height: '300px' }}>
                  <Doughnut
                    data={typeChartData}
                    options={{
                      responsive: true,
                      maintainAspectRatio: false,
                      plugins: {
                        legend: {
                          position: 'bottom',
                          labels: {
                            padding: 15,
                            font: { size: 12 },
                          },
                        },
                      },
                    }}
                  />
                </div>
              </CardContent>
            </Card>

            <Card className="border-0 shadow-md">
              <CardHeader>
                <CardTitle>Top 5 Produtos Movimentados</CardTitle>
              </CardHeader>
              <CardContent>
                <div style={{ height: '300px' }}>
                  <Bar
                    data={productsChartData}
                    options={{
                      responsive: true,
                      maintainAspectRatio: false,
                      plugins: {
                        legend: { display: false },
                      },
                      scales: {
                        y: { beginAtZero: true },
                      },
                    }}
                  />
                </div>
              </CardContent>
            </Card>
          </div>

          <Card className="border-0 shadow-md">
            <CardContent className="p-6">
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
                <Select value={typeFilter} onValueChange={setTypeFilter}>
                  <SelectTrigger>
                    <SelectValue placeholder="Tipo de movimentação" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">Todos os tipos</SelectItem>
                    <SelectItem value="entrada">Entrada</SelectItem>
                    <SelectItem value="saida">Saída</SelectItem>
                    <SelectItem value="venda">Venda</SelectItem>
                    <SelectItem value="devolucao">Devolução</SelectItem>
                    <SelectItem value="ajuste">Ajuste</SelectItem>
                  </SelectContent>
                </Select>

                <Select value={productFilter} onValueChange={setProductFilter}>
                  <SelectTrigger>
                    <SelectValue placeholder="Produto" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">Todos os produtos</SelectItem>
                    {products.map((product) => (
                      <SelectItem key={product.id} value={product.id.toString()}>
                        {product.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>

                <Select value={sortBy} onValueChange={setSortBy}>
                  <SelectTrigger>
                    <SelectValue placeholder="Ordenar por" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="created_at">Data</SelectItem>
                    <SelectItem value="quantity">Quantidade</SelectItem>
                  </SelectContent>
                </Select>

                <Input
                  type="date"
                  placeholder="Data inicial"
                  value={dateFrom}
                  onChange={(e) => setDateFrom(e.target.value)}
                />

                <Input
                  type="date"
                  placeholder="Data final"
                  value={dateTo}
                  onChange={(e) => setDateTo(e.target.value)}
                />

                <Select value={sortOrder} onValueChange={setSortOrder}>
                  <SelectTrigger>
                    <SelectValue placeholder="Ordem" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="desc">Maior para Menor</SelectItem>
                    <SelectItem value="asc">Menor para Maior</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              {movements.data.length === 0 ? (
                <div className="text-center py-12">
                  <Package className="h-16 w-16 text-muted-foreground mx-auto mb-4" />
                  <h3 className="text-xl font-semibold text-foreground mb-2">Nenhuma movimentação encontrada</h3>
                  <p className="text-muted-foreground">Não há movimentações de estoque registradas</p>
                </div>
              ) : (
                <>
                  <div className="overflow-x-auto">
                    <table className="w-full">
                      <thead>
                        <tr className="border-b">
                          <th className="text-left py-3 px-4 font-semibold text-foreground">Data/Hora</th>
                          <th className="text-left py-3 px-4 font-semibold text-foreground">Produto</th>
                          <th className="text-center py-3 px-4 font-semibold text-foreground">Tipo</th>
                          <th className="text-center py-3 px-4 font-semibold text-foreground">Quantidade</th>
                          <th className="text-left py-3 px-4 font-semibold text-foreground">Motivo</th>
                        </tr>
                      </thead>
                      <tbody>
                        {movements.data.map((movement) => (
                          <tr key={movement.id} className="border-b hover:bg-muted/50 transition-colors">
                            <td className="py-4 px-4 text-sm text-muted-foreground">
                              {new Date(movement.created_at).toLocaleString('pt-BR')}
                            </td>
                            <td className="py-4 px-4">
                              <div className="font-medium text-foreground">{movement.product.name}</div>
                            </td>
                            <td className="py-4 px-4 text-center">
                              <Badge variant={getTypeVariant(movement.type)} className={`gap-1 ${getTypeVariant(movement.type) === 'destructive' ? 'text-white' : ''}`}>
                                {getTypeIcon(movement.type)}
                                {typeLabels[movement.type] || movement.type}
                              </Badge>
                            </td>
                            <td className="py-4 px-4 text-center">
                              <span className="font-bold text-foreground">{movement.quantity}</span>
                            </td>
                            <td className="py-4 px-4 text-sm text-muted-foreground">
                              {movement.reason}
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>

                  {movements.meta.last_page > 1 && (
                    <div className="flex justify-center gap-2 mt-6">
                      {Array.from({ length: movements.meta.last_page }, (_, i) => i + 1).map((page) => {
                        const params: any = {};
                        if (typeFilter !== 'all') params.type = typeFilter;
                        if (productFilter !== 'all') params.product_id = productFilter;
                        if (dateFrom) params.date_from = dateFrom;
                        if (dateTo) params.date_to = dateTo;
                        params.sort_by = sortBy;
                        params.sort_order = sortOrder;
                        params.page = page;
                        
                        return (
                          <Button
                            key={page}
                            variant={page === movements.meta.current_page ? 'default' : 'outline'}
                            size="sm"
                            onClick={() => router.get('/reports/stock-movements', params, { preserveState: true })}
                            className="min-w-[40px]"
                          >
                            {page}
                          </Button>
                        );
                      })}
                    </div>
                  )}
                </>
              )}
            </CardContent>
          </Card>
        </div>
      </div>
    </AppLayout>
  );
}
