import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid, Package, ShoppingCart, FolderTree, Tag, Users, Shield, Key, PackageCheck, FileText, Store, Bell } from 'lucide-react';
import AppLogo from './app-logo';

interface AuthUser {
    id: number;
    name: string;
    email: string;
    permissions: string[];
    roles: string[];
}

interface PageProps {
    auth: {
        user: AuthUser | null;
    };
}

const allNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        permission: null,
    },
    {
        title: 'Meus Pedidos',
        href: '/meus-pedidos',
        icon: PackageCheck,
        permission: null,
    },
    {
        title: 'Produtos',
        href: '/products',
        icon: Package,
        permission: 'products.view',
    },
    {
        title: 'Categorias',
        href: '/categories',
        icon: FolderTree,
        permission: 'categories.view',
    },
    {
        title: 'Tags',
        href: '/tags',
        icon: Tag,
        permission: 'tags.view',
    },
    {
        title: 'Pedidos',
        href: '/orders',
        icon: ShoppingCart,
        permission: 'orders.view',
    },
    {
        title: 'Relatórios',
        href: '/reports',
        icon: FileText,
        permission: 'products.view',
    },
    {
        title: 'Notificações',
        href: '/notifications',
        icon: Bell,
        permission: 'products.view',
    },
    {
        title: 'Usuários',
        href: '/users',
        icon: Users,
        permission: 'users.view',
    },
    {
        title: 'Roles',
        href: '/roles',
        icon: Shield,
        permission: 'roles.view',
    },
    {
        title: 'Permissions',
        href: '/permissions',
        icon: Key,
        permission: 'roles.view',
    },
];

export function AppSidebar() {
    const { auth } = usePage<PageProps>().props;
    
    const hasPermission = (permission: string | null): boolean => {
        if (!permission) return true;
        if (!auth.user) return false;
        return auth.user.permissions.includes(permission) || auth.user.roles.includes('admin');
    };

    const mainNavItems = allNavItems.filter(item => hasPermission(item.permission || null));

    return (
        <Sidebar collapsible="icon" variant="inset" className="border-r-0">
            <SidebarHeader className="border-b bg-gradient-to-r from-primary/5 to-primary/10">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild className="hover:bg-primary/10">
                            <Link href={dashboard()} prefetch className="flex items-center gap-3">
                                <div className="flex aspect-square size-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                                    <Store className="size-5" />
                                </div>
                                <div className="flex flex-col gap-0.5 leading-none">
                                    <span className="font-bold text-lg bg-gradient-to-r from-primary to-primary/60 bg-clip-text text-transparent">Loja</span>
                                    <span className="text-xs text-muted-foreground">E-commerce</span>
                                </div>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent className="bg-gradient-to-b from-background to-muted/20">
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter className="border-t bg-gradient-to-r from-primary/5 to-primary/10">
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
