import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface Category {
  id: number;
  name: string;
}

interface Tag {
  id: number;
  name: string;
}

interface Product {
  id: number;
  name: string;
  slug: string;
  description?: string;
  image?: string;
  price: number;
  cost_price: number;
  quantity: number;
  min_quantity: number;
  active: boolean;
  category?: {
    id: number;
    name: string;
  };
  tags?: Array<{
    id: number;
    name: string;
  }>;
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
  _method?: string;
}

interface Props {
  product: Product;
  categories: Category[];
  tags: Tag[];
}

export default function ProductsEdit({ product, categories, tags }: Props) {
  const { data, setData, post, processing, errors } = useForm<FormData>({
    name: product.name || '',
    slug: product.slug || '',
    description: product.description || '',
    image: null,
    price: product.price?.toString() || '',
    cost_price: product.cost_price?.toString() || '',
    quantity: product.quantity?.toString() || '',
    min_quantity: product.min_quantity?.toString() || '',
    category_id: product.category?.id?.toString() || '',
    active: product.active ?? true,
    tag_ids: product.tags?.map(tag => tag.id) || [],
    _method: 'PUT',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(`/products/${product.id}`, {
      forceFormData: true,
      preserveScroll: true,
    });
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

  const handleTagToggle = (tagId: number) => {
    const newTagIds = data.tag_ids.includes(tagId)
      ? data.tag_ids.filter(id => id !== tagId)
      : [...data.tag_ids, tagId];
    setData('tag_ids', newTagIds);
  };

  return (
    <AppLayout>
      <Head title={`Editar: ${product.name}`} />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Editar Produto</h1>
          <div className="flex gap-2">
            <Link href={`/products/${product.id}`}>
              <Button variant="outline">Ver Produto</Button>
            </Link>
            <Link href="/products">
              <Button variant="outline">Voltar</Button>
            </Link>
          </div>
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
                {product.image && (
                  <div className="mb-2">
                    <img src={product.image} alt={product.name} className="w-32 h-32 object-cover rounded" />
                  </div>
                )}
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
                <div className="grid grid-cols-3 gap-2 mt-2">
                  {tags.map((tag) => (
                    <div key={tag.id} className="flex items-center space-x-2">
                      <Checkbox
                        id={`tag-${tag.id}`}
                        checked={data.tag_ids.includes(tag.id)}
                        onCheckedChange={() => handleTagToggle(tag.id)}
                      />
                      <Label htmlFor={`tag-${tag.id}`} className="text-sm">
                        {tag.name}
                      </Label>
                    </div>
                  ))}
                </div>
              </div>

              <div className="flex items-center space-x-2">
                <Checkbox
                  id="active"
                  checked={data.active}
                  onCheckedChange={(checked) => setData('active', !!checked)}
                />
                <Label htmlFor="active">Produto ativo</Label>
              </div>

              <div className="flex gap-4">
                <Button type="submit" disabled={processing} className="cursor-pointer">
                  {processing ? 'Salvando...' : 'Salvar Alterações'}
                </Button>
                <Link href={`/products/${product.id}`}>
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