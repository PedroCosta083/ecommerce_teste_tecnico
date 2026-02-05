import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Permission {
  id: number;
  name: string;
}

interface Props {
  permissions: Permission[];
}

export default function PermissionsIndex({ permissions }: Props) {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/permissions', {
      onSuccess: () => reset(),
    });
  };

  return (
    <AppLayout>
      <Head title="Gerenciar Permissions" />
      
      <div className="p-6 space-y-6">
        <h1 className="text-3xl font-bold">Gerenciar Permissions</h1>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card>
            <CardHeader>
              <CardTitle>Nova Permission</CardTitle>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                  <Label htmlFor="name">Nome da Permission *</Label>
                  <Input
                    id="name"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    placeholder="ex: manage reports"
                  />
                  {errors.name && <p className="text-sm text-red-600">{errors.name}</p>}
                </div>
                <Button type="submit" disabled={processing}>
                  {processing ? 'Criando...' : 'Criar Permission'}
                </Button>
              </form>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Permissions Existentes ({permissions.length})</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-2 max-h-96 overflow-y-auto">
                {permissions.map((permission) => (
                  <div key={permission.id} className="flex justify-between items-center p-2 border rounded">
                    <span className="text-sm">{permission.name}</span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </AppLayout>
  );
}