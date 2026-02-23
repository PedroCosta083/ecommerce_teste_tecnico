import { Head, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, LineElement, PointElement, ArcElement, Title, Tooltip, Legend } from 'chart.js';
import { Bar, Line, Doughnut } from 'react-chartjs-2';
import { Package, ShoppingCart, DollarSign, Users } from 'lucide-react';
import { Skeleton } from '@/components/ui/skeleton';

ChartJS.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, ArcElement, Title, Tooltip, Legend);

interface DashboardMetrics {
    overview: {
        total_products: number;
        active_products: number;
        total_categories: number;
        total_orders: number;
        total_revenue: number;
        pending_orders: number;
        total_users: number;
    };
    sales_by_status: Array<{ status: string; count: number; revenue: number }>;
    top_products: Array<{ name: string; total_sold: number; revenue: number }>;
    sales_last_7_days: Array<{ date: string; orders: number; revenue: number }>;
    products_by_category: Array<{ name: string; count: number }>;
}

export default function Dashboard() {
    const { auth } = usePage().props as any;
    const [metrics, setMetrics] = useState<DashboardMetrics | null>(null);
    const [loading, setLoading] = useState(true);
    
    const hasMetricsAccess = auth?.user?.roles?.some((role: any) => 
        ['admin', 'manager', 'editor'].includes(role.name)
    );

    useEffect(() => {
        if (!hasMetricsAccess) {
            setLoading(false);
            return;
        }
        
        fetch('/api/v1/dashboard/metrics', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            credentials: 'include',
        })
            .then(res => res.json())
            .then(data => {
                setMetrics(data.data);
                setLoading(false);
            })
            .catch(() => setLoading(false));
    }, [hasMetricsAccess]);

    if (loading) {
        return (
            <AppLayout>
                <Head title="Dashboard" />
                <div className="py-8">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                        <div>
                            <Skeleton className="h-10 w-48" />
                            <Skeleton className="h-5 w-96 mt-2" />
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            {Array.from({ length: 4 }).map((_, i) => (
                                <div key={i} className="bg-card rounded-xl shadow-lg p-6">
                                    <Skeleton className="h-4 w-32 mb-4" />
                                    <Skeleton className="h-10 w-24 mb-2" />
                                    <Skeleton className="h-4 w-20" />
                                </div>
                            ))}
                        </div>
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div className="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
                                <Skeleton className="h-6 w-48 mb-4" />
                                <Skeleton className="h-[300px] w-full" />
                            </div>
                            <div className="bg-white rounded-xl shadow-lg p-6">
                                <Skeleton className="h-6 w-48 mb-4" />
                                <Skeleton className="h-[300px] w-full" />
                            </div>
                        </div>
                    </div>
                </div>
            </AppLayout>
        );
    }

    if (!hasMetricsAccess) {
        return (
            <AppLayout>
                <Head title="Dashboard" />
                <div className="py-8">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="bg-card rounded-xl shadow-lg p-8 text-center">
                            <h1 className="text-3xl font-bold text-foreground mb-4">Bem-vindo!</h1>
                            <p className="text-muted-foreground">Você está logado como {auth?.user?.name}</p>
                        </div>
                    </div>
                </div>
            </AppLayout>
        );
    }

    if (!metrics) return null;

    const statusLabels: Record<string, string> = {
        pending: 'Pendente',
        processing: 'Processando',
        shipped: 'Enviado',
        delivered: 'Entregue',
        cancelled: 'Cancelado',
    };

    return (
        <AppLayout>
            <Head title="Dashboard" />

            <div className="py-8">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    
                    <div>
                        <h1 className="text-3xl font-bold text-foreground">Dashboard</h1>
                        <p className="text-muted-foreground mt-1">Visão geral do seu e-commerce</p>
                    </div>

                    {/* Overview Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div className="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-blue-100 text-sm font-medium">Total de Produtos</p>
                                    <p className="text-3xl font-bold mt-2">{metrics.overview.total_products}</p>
                                    <p className="text-blue-100 text-sm mt-1">{metrics.overview.active_products} ativos</p>
                                </div>
                                <div className="bg-white/20 p-3 rounded-lg">
                                    <Package className="h-8 w-8" />
                                </div>
                            </div>
                        </div>

                        <div className="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-green-100 text-sm font-medium">Total de Pedidos</p>
                                    <p className="text-3xl font-bold mt-2">{metrics.overview.total_orders}</p>
                                    <p className="text-green-100 text-sm mt-1">{metrics.overview.pending_orders} pendentes</p>
                                </div>
                                <div className="bg-white/20 p-3 rounded-lg">
                                    <ShoppingCart className="h-8 w-8" />
                                </div>
                            </div>
                        </div>

                        <div className="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-purple-100 text-sm font-medium">Receita Total</p>
                                    <p className="text-3xl font-bold mt-2">
                                        R$ {Number(metrics.overview.total_revenue).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                    </p>
                                </div>
                                <div className="bg-white/20 p-3 rounded-lg">
                                    <DollarSign className="h-8 w-8" />
                                </div>
                            </div>
                        </div>

                        <div className="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-orange-100 text-sm font-medium">Total de Usuários</p>
                                    <p className="text-3xl font-bold mt-2">{metrics.overview.total_users}</p>
                                    <p className="text-orange-100 text-sm mt-1">{metrics.overview.total_categories} categorias</p>
                                </div>
                                <div className="bg-white/20 p-3 rounded-lg">
                                    <Users className="h-8 w-8" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Charts Row 1 */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* Sales Last 7 Days */}
                        <div className="lg:col-span-2 bg-card rounded-xl shadow-lg p-6">
                            <h3 className="text-lg font-semibold mb-4 text-foreground">Vendas - Últimos 7 Dias</h3>
                            <div style={{ height: '300px' }}>
                                <Line
                                    data={{
                                        labels: metrics.sales_last_7_days.map(d => new Date(d.date).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })),
                                        datasets: [
                                            {
                                                label: 'Receita (R$)',
                                                data: metrics.sales_last_7_days.map(d => Number(d.revenue)),
                                                borderColor: 'rgb(139, 92, 246)',
                                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                                tension: 0.4,
                                                fill: true,
                                            },
                                        ],
                                    }}
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
                        </div>

                        {/* Sales by Status */}
                        <div className="bg-card rounded-xl shadow-lg p-6">
                            <h3 className="text-lg font-semibold mb-4 text-foreground">Pedidos por Status</h3>
                            <div style={{ height: '300px' }}>
                                <Doughnut
                                    data={{
                                        labels: metrics.sales_by_status.map(s => statusLabels[s.status] || s.status),
                                        datasets: [
                                            {
                                                data: metrics.sales_by_status.map(s => s.count),
                                                backgroundColor: [
                                                    'rgba(251, 191, 36, 0.8)',
                                                    'rgba(59, 130, 246, 0.8)',
                                                    'rgba(139, 92, 246, 0.8)',
                                                    'rgba(34, 197, 94, 0.8)',
                                                    'rgba(239, 68, 68, 0.8)',
                                                ],
                                                borderWidth: 0,
                                            },
                                        ],
                                    }}
                                    options={{
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { 
                                                position: 'bottom',
                                                labels: {
                                                    padding: 15,
                                                    font: { size: 11 },
                                                },
                                            },
                                        },
                                    }}
                                />
                            </div>
                        </div>
                    </div>

                    {/* Charts Row 2 */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {/* Top Products */}
                        <div className="bg-card rounded-xl shadow-lg p-6">
                            <h3 className="text-lg font-semibold mb-4 text-foreground">Top 5 Produtos Mais Vendidos</h3>
                            <div style={{ height: '300px' }}>
                                <Bar
                                    data={{
                                        labels: metrics.top_products.map(p => p.name.length > 20 ? p.name.substring(0, 20) + '...' : p.name),
                                        datasets: [
                                            {
                                                label: 'Quantidade Vendida',
                                                data: metrics.top_products.map(p => p.total_sold),
                                                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                                borderRadius: 8,
                                            },
                                        ],
                                    }}
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
                        </div>

                        {/* Products by Category */}
                        <div className="bg-card rounded-xl shadow-lg p-6">
                            <h3 className="text-lg font-semibold mb-4 text-foreground">Produtos por Categoria</h3>
                            <div style={{ height: '300px' }}>
                                <Bar
                                    data={{
                                        labels: metrics.products_by_category.map(c => c.name),
                                        datasets: [
                                            {
                                                label: 'Quantidade',
                                                data: metrics.products_by_category.map(c => c.count),
                                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                                borderRadius: 8,
                                            },
                                        ],
                                    }}
                                    options={{
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        indexAxis: 'y',
                                        plugins: {
                                            legend: { display: false },
                                        },
                                        scales: {
                                            x: { beginAtZero: true },
                                        },
                                    }}
                                />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </AppLayout>
    );
}
