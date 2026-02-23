import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface Category {
  id: number;
  name: string;
}

interface FormData {
  name: string;
  slug: string;
  description: string;
  parent_id: string;
  active: boolean;
}

interface Props {
  categories: Category[];
}

export default function CategoriesCreate({ categories }: Props) {
  const { data, setData, post, processing, errors } = useForm<FormData>({
    name: '',
    slug: '',
    description: '',
    parent_id: 'none',
    active: true,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/categories');
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
      <Head title="Nova Categoria" />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Nova Categoria</h1>
          <Link href="/categories">
            <Button variant="outline">Voltar</Button>
          </Link>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Informações da Categoria</CardTitle>
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
                <Label htmlFor="description">Descrição</Label>
                <Input
                  id="description"
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
                />
                {errors.description && <p className="text-sm text-red-600">{errors.description}</p>}
              </div>

              <div>
                <Label htmlFor="parent_id">Categoria Pai (opcional)</Label>
                <Select value={data.parent_id} onValueChange={(value) => setData('parent_id', value)}>
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione uma categoria pai" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="none">Nenhuma (categoria raiz)</SelectItem>
                    {categories.map((category) => (
                      <SelectItem key={category.id} value={category.id.toString()}>
                        {category.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.parent_id && <p className="text-sm text-red-600">{errors.parent_id}</p>}
              </div>

              <div className="flex items-center space-x-2">
                <Switch
                  id="active"
                  checked={data.active}
                  onCheckedChange={(checked) => setData('active', checked)}
                />
                <Label htmlFor="active">Categoria ativa</Label>
              </div>

              <div className="flex gap-4">
                <Button type="submit" disabled={processing} className="cursor-pointer">
                  {processing ? 'Salvando...' : 'Salvar Categoria'}
                </Button>
                <Link href="/categories">
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