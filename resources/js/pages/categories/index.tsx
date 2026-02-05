import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Trash2, Edit, Eye, Plus } from 'lucide-react';

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
  products_count?: number;
}

interface Props {
  categories: Category[];
}

export default function CategoriesIndex({ categories }: Props) {
  const handleDelete = (id: number, name: string) => {
    if (confirm(`Tem certeza que deseja excluir a categoria "${name}"?`)) {
      router.delete(`/categories/${id}`);
    }
  };

  return (
    <AppLayout>
      <Head title="Categorias" />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Categorias</h1>
          <Link href="/categories/create">
            <Button className="cursor-pointer">
              <Plus className="h-4 w-4 mr-2" />
              Nova Categoria
            </Button>
          </Link>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {categories.map((category) => (
            <Card key={category.id} className="hover:shadow-lg transition-shadow">
              <CardHeader>
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <CardTitle className="text-lg mb-2">{category.name}</CardTitle>
                    <div className="flex items-center gap-2 mb-2">
                      <Badge variant={category.active ? 'default' : 'secondary'}>
                        {category.active ? 'Ativa' : 'Inativa'}
                      </Badge>
                      {category.parent && (
                        <Badge variant="outline">
                          Subcategoria de: {category.parent.name}
                        </Badge>
                      )}
                    </div>
                  </div>
                </div>
              </CardHeader>
              
              <CardContent className="space-y-4">
                {category.description && (
                  <p className="text-sm text-gray-600 line-clamp-2">
                    {category.description}
                  </p>
                )}
                
                <div className="flex justify-between items-center text-sm">
                  <span className="text-gray-500">
                    Slug: {category.slug}
                  </span>
                </div>
                
                {category.children && category.children.length > 0 && (
                  <div>
                    <p className="text-sm font-medium text-gray-700 mb-1">
                      Subcategorias ({category.children.length}):
                    </p>
                    <div className="flex flex-wrap gap-1">
                      {category.children.slice(0, 3).map((child) => (
                        <Badge key={child.id} variant="outline" className="text-xs">
                          {child.name}
                        </Badge>
                      ))}
                      {category.children.length > 3 && (
                        <Badge variant="outline" className="text-xs">
                          +{category.children.length - 3}
                        </Badge>
                      )}
                    </div>
                  </div>
                )}
                
                <div className="flex gap-2 pt-2">
                  <Link href={`/categories/${category.id}`} className="flex-1">
                    <Button variant="outline" size="sm" className="w-full cursor-pointer">
                      <Eye className="h-4 w-4 mr-1" />
                      Ver
                    </Button>
                  </Link>
                  <Link href={`/categories/${category.id}/edit`} className="flex-1">
                    <Button variant="outline" size="sm" className="w-full cursor-pointer">
                      <Edit className="h-4 w-4 mr-1" />
                      Editar
                    </Button>
                  </Link>
                  <Button 
                    variant="destructive" 
                    size="sm"
                    onClick={() => handleDelete(category.id, category.name)}
                    className="px-3 cursor-pointer"
                  >
                    <Trash2 className="h-4 w-4" />
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {categories.length === 0 && (
          <Card>
            <CardContent className="p-12 text-center">
              <p className="text-gray-500 mb-4">Nenhuma categoria encontrada.</p>
              <Link href="/categories/create">
                <Button className="cursor-pointer">Criar Primeira Categoria</Button>
              </Link>
            </CardContent>
          </Card>
        )}
      </div>
    </AppLayout>
  );
}