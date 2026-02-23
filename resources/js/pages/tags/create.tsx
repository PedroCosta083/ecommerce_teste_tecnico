import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';

interface FormData {
  name: string;
  slug: string;
  color: string;
  active: boolean;
}

export default function TagsCreate() {
  const { data, setData, post, processing, errors } = useForm<FormData>({
    name: '',
    slug: '',
    color: '#3B82F6',
    active: true,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/tags');
  };

  const generateSlug = (name: string) => {
    return name
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
  };

  const handleNameChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const name = e.target.value;
    setData({
      ...data,
      name,
      slug: generateSlug(name),
    });
  };

  return (
    <AppLayout>
      <Head title="Nova Tag" />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Nova Tag</h1>
          <Link href="/tags">
            <Button variant="outline">Voltar</Button>
          </Link>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Informações da Tag</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="name">Nome *</Label>
                  <Input
                    id="name"
                    value={data.name}
                    onChange={handleNameChange}
                  />
                  {errors.name && <p className="text-sm text-red-600">{errors.name}</p>}
                </div>
                
                <div>
                  <Label htmlFor="slug">Slug *</Label>
                  <Input
                    id="slug"
                    value={data.slug}
                    onChange={(e) => setData('slug', e.target.value)}
                  />
                  {errors.slug && <p className="text-sm text-red-600">{errors.slug}</p>}
                </div>
              </div>

              <div>
                <Label htmlFor="color">Cor *</Label>
                <div className="flex items-center gap-2">
                  <Input
                    id="color"
                    type="color"
                    value={data.color}
                    onChange={(e) => setData('color', e.target.value)}
                    className="w-16 h-10 p-1"
                  />
                  <Input
                    value={data.color}
                    onChange={(e) => setData('color', e.target.value)}
                    placeholder="#3B82F6"
                    className="flex-1"
                  />
                </div>
                {errors.color && <p className="text-sm text-red-600">{errors.color}</p>}
              </div>

              <div className="flex items-center space-x-2">
                <Switch
                  id="active"
                  checked={data.active}
                  onCheckedChange={(checked) => setData('active', checked)}
                />
                <Label htmlFor="active">Tag ativa</Label>
              </div>

              <div className="flex gap-4">
                <Button type="submit" disabled={processing} className="cursor-pointer">
                  {processing ? 'Salvando...' : 'Salvar Tag'}
                </Button>
                <Link href="/tags">
                  <Button type="button" variant="outline" className="cursor-pointer">
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