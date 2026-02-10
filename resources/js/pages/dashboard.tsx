import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Can } from '@/components/can';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <Can permission="products.view">
                        <Card>
                            <CardHeader>
                                <CardTitle>Produtos</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm text-gray-600 mb-4">
                                    Gerencie o catálogo de produtos da sua loja
                                </p>
                                <Link href="/products">
                                    <Button className="w-full">Gerenciar Produtos</Button>
                                </Link>
                            </CardContent>
                        </Card>
                    </Can>
                    
                    <Can permission="categories.view">
                        <Card>
                            <CardHeader>
                                <CardTitle>Categorias</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm text-gray-600 mb-4">
                                    Organize produtos em categorias
                                </p>
                                <Link href="/categories">
                                    <Button className="w-full">Gerenciar Categorias</Button>
                                </Link>
                            </CardContent>
                        </Card>
                    </Can>
                    
                    <Can permission="tags.view">
                        <Card>
                            <CardHeader>
                                <CardTitle>Tags</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm text-gray-600 mb-4">
                                    Gerencie tags para classificar produtos
                                </p>
                                <Link href="/tags">
                                    <Button className="w-full">Gerenciar Tags</Button>
                                </Link>
                            </CardContent>
                        </Card>
                    </Can>
                </div>
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <Can permission="orders.view">
                        <Card>
                            <CardHeader>
                                <CardTitle>Pedidos</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm text-gray-600 mb-4">
                                    Visualize e gerencie os pedidos dos clientes
                                </p>
                                <Link href="/orders">
                                    <Button className="w-full">Gerenciar Pedidos</Button>
                                </Link>
                            </CardContent>
                        </Card>
                    </Can>
                    
                    <Can permission="roles.view">
                        <Card>
                            <CardHeader>
                                <CardTitle>Roles & Permissions</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm text-gray-600 mb-4">
                                    Gerencie roles e permissions do sistema
                                </p>
                                <Link href="/roles">
                                    <Button className="w-full">Gerenciar Roles</Button>
                                </Link>
                            </CardContent>
                        </Card>
                    </Can>
                    
                    <Can permission="users.view">
                        <Card>
                            <CardHeader>
                                <CardTitle>Usuários</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm text-gray-600 mb-4">
                                    Gerencie usuários e suas permissões
                                </p>
                                <Link href="/users">
                                    <Button className="w-full">Gerenciar Usuários</Button>
                                </Link>
                            </CardContent>
                        </Card>
                    </Can>
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </AppLayout>
    );
}
