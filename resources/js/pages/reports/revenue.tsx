import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DollarSign, TrendingUp, ShoppingCart, ArrowLeft, Calendar } from 'lucide-react';
import { Bar } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

interface Order {
  id: number;
  total: number;
  status: string;
  created_at: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
}

interface Props {
  orders: Order[];
  summary: {
    total_revenue: number;
    total_orders: number;
    average_ticket: number;
  };
  revenue_by_status: Record<string, { count: number; revenue: number }>;
  revenue_by_day: Record<string, { count: number; revenue: number }>;
  filters: {
    date_from: string;
    date_to: string;
    status?: string;
  };
}

const statusLabels: Record<string, string> = {
  pending: 'Pendente',
  processing: 'Processando',
  shipped: 'Enviado',
  delivered: 'Entregue',
  cancelled: 'Cancelado',
};

const statusColors: Record<string, string> = {
  pending: 'bg-yellow-100 text-yellow-800',
  processing: 'bg-blue-100 text-blue-800',
  shipped: 'bg-purple-100 text-purple-800',
  delivered: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
};

export default function RevenueReport({ orders, summary, revenue_by_status, revenue_by_day, filters }: Props) {
  const [dateFrom, setDateFrom] = useState(filters.date_from);
  const [dateTo, setDateTo] = useState(filters.date_to);
  const [status, setStatus] = useState(filters.status || 'all');

  const handleFilter = () => {
    const params: any = { date_from: dateFrom, date_to: dateTo };
    if (status !== 'all') {
      params.status = status;
    }
    router.get('/reports/revenue', params, { preserveState: true });
  };

  const chartData = {
    labels: Object.keys(revenue_by_day).map(date => new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })),
    datasets: [
      {
        label: 'Receita (R$)',
        data: Object.values(revenue_by_day).map(d => d.revenue),
        backgroundColor: 'rgba(70, 104, 91, 0.8)',
        borderRadius: 8,
      },
    ],
  };

  return (
    <AppLayout>
      <Head title="Relatório de Receita" />
      
      <div className="p-6 space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-foreground">Relatório de Receita</h1>
            <p className="text-muted-foreground mt-1">Análise detalhada de vendas e receita</p>
          </div>
          <Link href="/reports">
            <Button variant="outline">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Voltar
            </Button>
          </Link>
        </div>

        {/* Filtros */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Calendar className="h-5 w-5" />
              Filtros
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label className="text-sm font-medium mb-2 block">Data Inicial</label>
                <Input
                  type="date"
                  value={dateFrom}
                  onChange={(e) => setDateFrom(e.target.value)}
                />
              </div>
              <div>
                <label className="text-sm font-medium mb-2 block">Data Final</label>
                <Input
                  type="date"
                  value={dateTo}
                  onChange={(e) => setDateTo(e.target.value)}
                />
              </div>
              <div>
                <label className="text-sm font-medium mb-2 block">Status</label>
                <Select value={status} onValueChange={setStatus}>
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">Todos</SelectItem>
                    <SelectItem value="pending">Pendente</SelectItem>
                    <SelectItem value="processing">Processando</SelectItem>
                    <SelectItem value="shipped">Enviado</SelectItem>
                    <SelectItem value="delivered">Entregue</SelectItem>
                    <SelectItem value="cancelled">Cancelado</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div className="flex items-end">
                <Button onClick={handleFilter} className="w-full">
                  Filtrar
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Cards de Resumo */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <Card className="bg-gradient-to-br from-green-500 to-green-600 text-white">
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-green-100 text-sm font-medium">Receita Total</p>
                  <p className="text-3xl font-bold mt-2">
                    R$ {summary.total_revenue.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </p>
                </div>
                <div className="bg-white/20 p-3 rounded-lg">
                  <DollarSign className="h-8 w-8" />
                </div>
              </div>
            </CardContent>
          </Card>

          <Card className="bg-gradient-to-br from-blue-500 to-blue-600 text-white">
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-blue-100 text-sm font-medium">Total de Pedidos</p>
                  <p className="text-3xl font-bold mt-2">{summary.total_orders}</p>
                </div>
                <div className="bg-white/20 p-3 rounded-lg">
                  <ShoppingCart className="h-8 w-8" />
                </div>
              </div>
            </CardContent>
          </Card>

          <Card className="bg-gradient-to-br from-purple-500 to-purple-600 text-white">
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-purple-100 text-sm font-medium">Ticket Médio</p>
                  <p className="text-3xl font-bold mt-2">
                    R$ {summary.average_ticket.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </p>
                </div>
                <div className="bg-white/20 p-3 rounded-lg">
                  <TrendingUp className="h-8 w-8" />
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Gráfico */}
        <Card>
          <CardHeader>
            <CardTitle>Receita por Dia</CardTitle>
          </CardHeader>
          <CardContent>
            <div style={{ height: '300px' }}>
              <Bar
                data={chartData}
                options={{
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: { display: false },
                  },
                  scales: {
                    y: {
                      beginAtZero: true,
                      ticks: {
                        callback: (value) => `R$ ${value}`,
                      },
                    },
                  },
                }}
              />
            </div>
          </CardContent>
        </Card>

        {/* Receita por Status */}
        <Card>
          <CardHeader>
            <CardTitle>Receita por Status</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
              {Object.entries(revenue_by_status).map(([statusKey, data]) => (
                <div key={statusKey} className="p-4 border rounded-lg">
                  <Badge className={statusColors[statusKey]} variant="secondary">
                    {statusLabels[statusKey]}
                  </Badge>
                  <p className="text-2xl font-bold mt-2">
                    R$ {data.revenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}
                  </p>
                  <p className="text-sm text-muted-foreground">{data.count} pedidos</p>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Lista de Pedidos */}
        <Card>
          <CardHeader>
            <CardTitle>Pedidos ({orders.length})</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b">
                    <th className="text-left p-3">ID</th>
                    <th className="text-left p-3">Cliente</th>
                    <th className="text-left p-3">Data</th>
                    <th className="text-left p-3">Status</th>
                    <th className="text-right p-3">Total</th>
                  </tr>
                </thead>
                <tbody>
                  {orders.map((order) => (
                    <tr key={order.id} className="border-b hover:bg-muted/50 transition-colors">
                      <td className="p-3 text-foreground">#{order.id}</td>
                      <td className="p-3 text-foreground">{order.user.name}</td>
                      <td className="p-3 text-foreground">{new Date(order.created_at).toLocaleDateString('pt-BR')}</td>
                      <td className="p-3">
                        <Badge className={statusColors[order.status]} variant="secondary">
                          {statusLabels[order.status]}
                        </Badge>
                      </td>
                      <td className="p-3 text-right font-semibold text-foreground">
                        R$ {Number(order.total).toFixed(2)}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}
