import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FileText, TrendingDown, Package, DollarSign } from 'lucide-react';

export default function ReportsIndex() {
  const reports = [
    {
      title: 'Receita',
      description: 'Análise de vendas e receita por período',
      icon: DollarSign,
      href: '/reports/revenue',
      color: 'text-green-600',
      bgColor: 'bg-green-50',
    },
    {
      title: 'Estoque Baixo',
      description: 'Produtos com estoque abaixo do mínimo',
      icon: TrendingDown,
      href: '/reports/low-stock',
      color: 'text-red-600',
      bgColor: 'bg-red-50',
    },
    {
      title: 'Movimentações de Estoque',
      description: 'Histórico de entradas e saídas',
      icon: Package,
      href: '/reports/stock-movements',
      color: 'text-blue-600',
      bgColor: 'bg-blue-50',
    },
  ];

  return (
    <AppLayout>
      <Head title="Relatórios" />
      
      <div className="min-h-screen bg-background">
        <div className="max-w-7xl mx-auto p-6 space-y-6">
          <div>
            <h1 className="text-4xl font-bold text-foreground">
              Relatórios
            </h1>
            <p className="text-muted-foreground mt-1">Análises e relatórios do sistema</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {reports.map((report) => (
              <Link key={report.href} href={report.href}>
                <Card className="hover:shadow-xl transition-all duration-300 cursor-pointer border-0 shadow-md h-full">
                  <CardHeader>
                    <div className={`w-12 h-12 rounded-lg ${report.bgColor} dark:bg-opacity-20 flex items-center justify-center mb-4`}>
                      <report.icon className={`h-6 w-6 ${report.color}`} />
                    </div>
                    <CardTitle className="text-xl">{report.title}</CardTitle>
                    <CardDescription>{report.description}</CardDescription>
                  </CardHeader>
                </Card>
              </Link>
            ))}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
