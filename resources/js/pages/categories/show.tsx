import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Edit, ArrowLeft } from 'lucide-react';

interface Category {
  id: number;
  name: string;
  slug: string;
  description?: string;
  active: boolean;
  parent?: {
    id: number;
    name: string;
  };
  children?: Category[];
}

interface Props {
  category: Category;
}

export default function CategoriesShow({ category }: Props) {
  return (
    <AppLayout>
      <Head title={category.name} />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">{category.name}</h1>
          <div className="flex gap-2">
            <Link href={`/categories/${category.id}/edit`}>
              <Button className="cursor-pointer">
                <Edit className="h-4 w-4 mr-2" />
                Editar
              </Button>
            </Link>
            <Link href="/categories">
              <Button variant="outline" className="cursor-pointer">
                <ArrowLeft className="h-4 w-4 mr-2" />
                Voltar
              </Button>
            </Link>
          </div>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Informações da Categoria</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="text-sm font-medium text-gray-500">Nome</label>
                <p className="text-lg">{category.name}</p>
              </div>
              <div>
                <label className="text-sm font-medium text-gray-500">Slug</label>
                <p className="text-lg font-mono">{category.slug}</p>
              </div>
            </div>

            {category.description && (
              <div>
                <label className="text-sm font-medium text-gray-500">Descrição</label>
                <p className="text-lg">{category.description}</p>
              </div>
            )}

            <div className="flex items-center gap-4">
              <div>
                <label className="text-sm font-medium text-gray-500">Status</label>
                <div className="mt-1">
                  <Badge variant={category.active ? 'default' : 'secondary'}>
                    {category.active ? 'Ativa' : 'Inativa'}
                  </Badge>
                </div>
              </div>

              {category.parent && (
                <div>
                  <label className="text-sm font-medium text-gray-500">Categoria Pai</label>
                  <div className="mt-1">
                    <Link href={`/categories/${category.parent.id}`}>
                      <Badge variant="outline" className="cursor-pointer hover:bg-gray-100">
                        {category.parent.name}
                      </Badge>
                    </Link>
                  </div>
                </div>
              )}
            </div>

            {category.children && category.children.length > 0 && (
              <div>
                <label className="text-sm font-medium text-gray-500">
                  Subcategorias ({category.children.length})
                </label>
                <div className="flex flex-wrap gap-2 mt-2">
                  {category.children.map((child) => (
                    <Link key={child.id} href={`/categories/${child.id}`}>
                      <Badge variant="outline" className="cursor-pointer hover:bg-gray-100">
                        {child.name}
                      </Badge>
                    </Link>
                  ))}
                </div>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}