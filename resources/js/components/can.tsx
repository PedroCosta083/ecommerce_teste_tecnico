import { usePermission } from '@/hooks/use-permission';
import { ReactNode } from 'react';

interface CanProps {
    permission?: string;
    role?: string;
    children: ReactNode;
    fallback?: ReactNode;
}

export function Can({ permission, role, children, fallback = null }: CanProps) {
    const { can, hasRole } = usePermission();

    const hasAccess = permission ? can(permission) : role ? hasRole(role) : false;

    return hasAccess ? <>{children}</> : <>{fallback}</>;
}
