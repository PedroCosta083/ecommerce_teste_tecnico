import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import { X } from 'lucide-react';

interface Category {
  id: number;
  name: string;
}

interface Tag {
  id: number;
  name: string;
}

interface FormData {
  name: string;
  slug: string;
  description: string;
  image: File | null;
  price: string;
  cost_price: string;
  quantity: string;
  min_quantity: string;
  category_id: string;
  active: boolean;
  tag_ids: number[];
}

interface Props {
  categories: Category[];
  tags: Tag[];
}

export default function ProductsCreate({ categories, tags }: Props) {
  const { data, setData, post, processing, errors } = useForm<FormData>({
    name: '',
    slug: '',
    description: '',
    image: null,
    price: '',
    cost_price: '',
    quantity: '',
    min_quantity: '',
    category_id: '',
    active: true,
    tag_ids: [],
  });

  const [selectedTagId, setSelectedTagId] = useState<string>('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/products');
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

  const handleTagAdd = (tagId: string) => {
    const id = parseInt(tagId);
    if (!data.tag_ids.includes(id)) {
      setData('tag_ids', [...data.tag_ids, id]);
    }
    setSelectedTagId('');
  };

  const handleTagRemove = (tagId: number) => {
    setData('tag_ids', data.tag_ids.filter(id => id !== tagId));
  };

  return (
    <AppLayout>
      <Head title="Novo Produto" />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Novo Produto</h1>
          <Link href="/products">
            <Button variant="outline">Voltar</Button>
          </Link>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Informações do Produto</CardTitle>
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
                <Label htmlFor="image">Imagem do Produto</Label>
                <Input
                  id="image"
                  type="file"
                  accept="image/jpeg,image/jpg,image/png,image/webp"
                  onChange={(e) => setData('image', e.target.files?.[0] || null)}
                />
                {errors.image && <p className="text-sm text-red-600">{errors.image}</p>}
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="price">Preço de Venda *</Label>
                  <CurrencyInput
                    id="price"
                    value={data.price}
                    onChange={(value) => setData('price', value)}
                  />
                  {errors.price && <p className="text-sm text-red-600">{errors.price}</p>}
                </div>
                
                <div>
                  <Label htmlFor="cost_price">Preço de Custo *</Label>
                  <CurrencyInput
                    id="cost_price"
                    value={data.cost_price}
                    onChange={(value) => setData('cost_price', value)}
                  />
                  {errors.cost_price && <p className="text-sm text-red-600">{errors.cost_price}</p>}
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="quantity">Quantidade *</Label>
                  <Input
                    id="quantity"
                    type="number"
                    value={data.quantity}
                    onChange={(e) => setData('quantity', e.target.value)}
                  />
                  {errors.quantity && <p className="text-sm text-red-600">{errors.quantity}</p>}
                </div>
                
                <div>
                  <Label htmlFor="min_quantity">Quantidade Mínima *</Label>
                  <Input
                    id="min_quantity"
                    type="number"
                    value={data.min_quantity}
                    onChange={(e) => setData('min_quantity', e.target.value)}
                  />
                  {errors.min_quantity && <p className="text-sm text-red-600">{errors.min_quantity}</p>}
                </div>
              </div>

              <div>
                <Label htmlFor="category_id">Categoria *</Label>
                <Select value={data.category_id} onValueChange={(value) => setData('category_id', value)}>
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione uma categoria" />
                  </SelectTrigger>
                  <SelectContent>
                    {categories.map((category) => (
                      <SelectItem key={category.id} value={category.id.toString()}>
                        {category.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.category_id && <p className="text-sm text-red-600">{errors.category_id}</p>}
              </div>

              <div>
                <Label>Tags</Label>
                <Select value={selectedTagId} onValueChange={handleTagAdd}>
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione tags" />
                  </SelectTrigger>
                  <SelectContent>
                    {tags.filter(tag => !data.tag_ids.includes(tag.id)).map((tag) => (
                      <SelectItem key={tag.id} value={tag.id.toString()}>
                        {tag.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                <div className="flex flex-wrap gap-2 mt-2">
                  {data.tag_ids.map((tagId) => {
                    const tag = tags.find(t => t.id === tagId);
                    return tag ? (
                      <Badge key={tag.id} variant="secondary" className="gap-1">
                        {tag.name}
                        <X 
                          className="h-3 w-3 cursor-pointer hover:text-destructive" 
                          onClick={() => handleTagRemove(tag.id)}
                        />
                      </Badge>
                    ) : null;
                  })}
                </div>
              </div>

              <div className="flex items-center space-x-2">
                <Switch
                  id="active"
                  checked={data.active}
                  onCheckedChange={(checked) => setData('active', checked)}
                />
                <Label htmlFor="active">Produto ativo</Label>
              </div>

              <div className="flex gap-4">
                <Button type="submit" disabled={processing} className="cursor-pointer">
                  {processing ? 'Salvando...' : 'Salvar Produto'}
                </Button>
                <Link href="/products">
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