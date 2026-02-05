import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';

interface Permission {
  id: number;
  name: string;
}

interface Role {
  id: number;
  name: string;
  permissions: Permission[];
}

interface Props {
  role: Role;
  permissions: Permission[];
}

interface FormData {
  name: string;
  permissions: number[];
}

export default function RolesEdit({ role, permissions }: Props) {
  const { data, setData, put, processing, errors } = useForm<FormData>({
    name: role.name,
    permissions: role.permissions.map(p => p.id),
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    put(`/roles/${role.id}`);
  };

  const handlePermissionChange = (permissionId: number, checked: boolean) => {
    if (checked) {
      setData('permissions', [...data.permissions, permissionId]);
    } else {
      setData('permissions', data.permissions.filter(id => id !== permissionId));
    }
  };

  return (
    <AppLayout>
      <Head title={`Editar Role: ${role.name}`} />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Editar Role: {role.name}</h1>
          <Link href="/roles">
            <Button variant="outline">Voltar</Button>
          </Link>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Informações do Role</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <Label htmlFor="name">Nome *</Label>
                <Input
                  id="name"
                  value={data.name}
                  onChange={(e) => setData('name', e.target.value)}
                />
                {errors.name && <p className="text-sm text-red-600">{errors.name}</p>}
              </div>

              <div>
                <Label>Permissions</Label>
                <div className="grid grid-cols-2 gap-2 mt-2">
                  {permissions.map((permission) => (
                    <div key={permission.id} className="flex items-center space-x-2">
                      <Checkbox
                        id={`permission-${permission.id}`}
                        checked={data.permissions.includes(permission.id)}
                        onCheckedChange={(checked) => 
                          handlePermissionChange(permission.id, !!checked)
                        }
                      />
                      <Label htmlFor={`permission-${permission.id}`}>
                        {permission.name}
                      </Label>
                    </div>
                  ))}
                </div>
              </div>

              <div className="flex gap-4">
                <Button type="submit" disabled={processing}>
                  {processing ? 'Salvando...' : 'Salvar Role'}
                </Button>
                <Link href="/roles">
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