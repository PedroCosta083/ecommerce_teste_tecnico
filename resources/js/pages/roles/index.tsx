import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Role {
  id: number;
  name: string;
  permissions: Array<{ id: number; name: string }>;
}

interface Props {
  roles: Role[];
}

export default function RolesIndex({ roles }: Props) {
  return (
    <AppLayout>
      <Head title="Gerenciar Roles" />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Gerenciar Roles</h1>
          <Link href="/roles/create">
            <Button>Novo Role</Button>
          </Link>
        </div>

        <div className="grid gap-4">
          {roles.map((role) => (
            <Card key={role.id}>
              <CardHeader>
                <div className="flex justify-between items-center">
                  <CardTitle className="capitalize">{role.name}</CardTitle>
                  <div className="flex gap-2">
                    <Link href={`/roles/${role.id}`}>
                      <Button variant="outline" size="sm">Ver</Button>
                    </Link>
                    <Link href={`/roles/${role.id}/edit`}>
                      <Button variant="outline" size="sm">Editar</Button>
                    </Link>
                  </div>
                </div>
              </CardHeader>
              <CardContent>
                <div className="flex flex-wrap gap-2">
                  {role.permissions.map((permission) => (
                    <Badge key={permission.id} variant="secondary">
                      {permission.name}
                    </Badge>
                  ))}
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </AppLayout>
  );
}