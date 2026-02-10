import { NavFooter } from '@/components/nav-footer';
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
import { BookOpen, Folder, LayoutGrid, Package, ShoppingCart, FolderTree, Tag, Users, Shield, Key, PackageCheck } from 'lucide-react';
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

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
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
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
