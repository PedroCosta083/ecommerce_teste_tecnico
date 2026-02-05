import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Trash2, Edit, Eye, Plus } from 'lucide-react';

interface Tag {
  id: number;
  name: string;
  slug: string;
  color: string;
  active: boolean;
}

interface Props {
  tags: Tag[];
}

export default function TagsIndex({ tags }: Props) {
  const handleDelete = (id: number, name: string) => {
    if (confirm(`Tem certeza que deseja excluir a tag "${name}"?`)) {
      router.delete(`/tags/${id}`);
    }
  };

  return (
    <AppLayout>
      <Head title="Tags" />
      
      <div className="p-6 space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold">Tags</h1>
          <Link href="/tags/create">
            <Button className="cursor-pointer">
              <Plus className="h-4 w-4 mr-2" />
              Nova Tag
            </Button>
          </Link>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {tags.map((tag) => (
            <Card key={tag.id} className="hover:shadow-lg transition-shadow">
              <CardHeader>
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <CardTitle className="text-lg mb-2 flex items-center gap-2">
                      <div 
                        className="w-4 h-4 rounded-full" 
                        style={{ backgroundColor: tag.color }}
                      />
                      {tag.name}
                    </CardTitle>
                    <Badge variant={tag.active ? 'default' : 'secondary'}>
                      {tag.active ? 'Ativa' : 'Inativa'}
                    </Badge>
                  </div>
                </div>
              </CardHeader>
              
              <CardContent className="space-y-4">
                <div className="flex justify-between items-center text-sm">
                  <span className="text-gray-500">
                    Slug: {tag.slug}
                  </span>
                  <span className="text-gray-500 font-mono">
                    {tag.color}
                  </span>
                </div>
                
                <div className="flex gap-2 pt-2">
                  <Link href={`/tags/${tag.id}`} className="flex-1">
                    <Button variant="outline" size="sm" className="w-full cursor-pointer">
                      <Eye className="h-4 w-4 mr-1" />
                      Ver
                    </Button>
                  </Link>
                  <Link href={`/tags/${tag.id}/edit`} className="flex-1">
                    <Button variant="outline" size="sm" className="w-full cursor-pointer">
                      <Edit className="h-4 w-4 mr-1" />
                      Editar
                    </Button>
                  </Link>
                  <Button 
                    variant="destructive" 
                    size="sm"
                    onClick={() => handleDelete(tag.id, tag.name)}
                    className="px-3 cursor-pointer"
                  >
                    <Trash2 className="h-4 w-4" />
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {tags.length === 0 && (
          <Card>
            <CardContent className="p-12 text-center">
              <p className="text-gray-500 mb-4">Nenhuma tag encontrada.</p>
              <Link href="/tags/create">
                <Button className="cursor-pointer">Criar Primeira Tag</Button>
              </Link>
            </CardContent>
          </Card>
        )}
      </div>
    </AppLayout>
  );
}