import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';

interface Role {
  id: number;
  name: string;
}

interface User {
  id: number;
  name: string;
  email: string;
  roles: Role[];
}

interface Props {
  user: User;
  roles: Role[];
}

interface FormData {
  roles: number[];
}

export default function UsersEditRoles({ user, roles }: Props) {
  const { data, setData, put, processing } = useForm<FormData>({
    roles: user.roles.map(r => r.id),
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    put(`/users/${user.id}/roles`);
  };

  const handleRoleChange = (roleId: number, checked: boolean) => {
    if (checked) {
      setData('roles', [...data.roles, roleId]);
    } else {
      setData('roles', data.roles.filter(id => id !== roleId));
    }
  };

  return (
    <AppLayout>
      <Head title={`Gerenciar Roles: ${user.name}`} />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-3xl font-bold">Gerenciar Roles</h1>
            <p className="text-gray-600">{user.name} - {user.email}</p>
          </div>
          <Link href="/users">
            <Button variant="outline">Voltar</Button>
          </Link>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Roles do Usuário</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <Label>Selecione os roles</Label>
                <div className="grid grid-cols-1 gap-2 mt-2">
                  {roles.map((role) => (
                    <div key={role.id} className="flex items-center space-x-2">
                      <Checkbox
                        id={`role-${role.id}`}
                        checked={data.roles.includes(role.id)}
                        onCheckedChange={(checked) => 
                          handleRoleChange(role.id, !!checked)
                        }
                      />
                      <Label htmlFor={`role-${role.id}`} className="capitalize">
                        {role.name}
                      </Label>
                    </div>
                  ))}
                </div>
              </div>

              <div className="flex gap-4">
                <Button type="submit" disabled={processing}>
                  {processing ? 'Salvando...' : 'Salvar Roles'}
                </Button>
                <Link href="/users">
                  <Button type="button" variant="outline">
                    Cancelar
                  </Button>
                </Link>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}