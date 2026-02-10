import { usePage } from '@inertiajs/react';

interface AuthUser {
    id: number;
    name: string;
    email: string;
    permissions?: string[];
    roles?: string[];
}

interface PageProps {
    auth: {
        user: AuthUser | null;
    };
}

export function usePermission() {
    const { auth } = usePage<PageProps>().props;

    const can = (permission: string): boolean => {
        if (!auth.user) return false;
        if (auth.user.roles?.includes('admin')) return true;
        return auth.user.permissions?.includes(permission) || false;
    };

    const hasRole = (role: string): boolean => {
        if (!auth.user) return false;
        return auth.user.roles?.includes(role) || false;
    };

    const hasAnyRole = (roles: string[]): boolean => {
        if (!auth.user) return false;
        return roles.some(role => auth.user?.roles?.includes(role));
    };

    const hasAllRoles = (roles: string[]): boolean => {
        if (!auth.user) return false;
        return roles.every(role => auth.user?.roles?.includes(role));
    };

    return {
        can,
        hasRole,
        hasAnyRole,
        hasAllRoles,
        user: auth.user,
    };
}
