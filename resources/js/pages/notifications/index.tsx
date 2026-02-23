import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Bell, Check, CheckCheck, Package, ShoppingCart, AlertTriangle, ArrowLeft } from 'lucide-react';

interface Notification {
  id: number;
  type: string;
  title: string;
  message: string;
  data: any;
  read_at: string | null;
  created_at: string;
}

interface Props {
  notifications: {
    data: Notification[];
    meta: {
      current_page: number;
      last_page: number;
      per_page: number;
      total: number;
    };
  };
  unread_count: number;
}

const typeIcons: Record<string, any> = {
  product_created: Package,
  product_updated: Package,
  product_deleted: Package,
  order_created: ShoppingCart,
  order_status_changed: ShoppingCart,
  low_stock: AlertTriangle,
};

const typeColors: Record<string, string> = {
  product_created: 'text-green-600 bg-green-50',
  product_updated: 'text-blue-600 bg-blue-50',
  product_deleted: 'text-red-600 bg-red-50',
  order_created: 'text-purple-600 bg-purple-50',
  order_status_changed: 'text-indigo-600 bg-indigo-50',
  low_stock: 'text-orange-600 bg-orange-50',
};

export default function NotificationsIndex({ notifications, unread_count }: Props) {
  const markAsRead = (id: number) => {
    router.post(`/notifications/${id}/read`, {}, { preserveScroll: true });
  };

  const markAllAsRead = () => {
    router.post('/notifications/read-all', {}, { preserveScroll: true });
  };

  const getIcon = (type: string) => {
    const Icon = typeIcons[type] || Bell;
    return Icon;
  };

  return (
    <AppLayout>
      <Head title="Notificações" />
      
      <div className="p-6 space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold">Notificações</h1>
            <p className="text-gray-600 mt-1">
              {unread_count > 0 ? `${unread_count} não lida${unread_count > 1 ? 's' : ''}` : 'Todas as notificações lidas'}
            </p>
          </div>
          <div className="flex gap-2">
            {unread_count > 0 && (
              <Button onClick={markAllAsRead} variant="outline">
                <CheckCheck className="h-4 w-4 mr-2" />
                Marcar todas como lidas
              </Button>
            )}
            <Link href="/dashboard">
              <Button variant="outline">
                <ArrowLeft className="h-4 w-4 mr-2" />
                Voltar
              </Button>
            </Link>
          </div>
        </div>

        <div className="space-y-3">
          {notifications.data.map((notification) => {
            const Icon = getIcon(notification.type);
            const isUnread = !notification.read_at;
            
            return (
              <Card 
                key={notification.id} 
                className={`transition-all hover:shadow-md ${isUnread ? 'border-l-4 border-l-primary bg-primary/5' : ''}`}
              >
                <CardContent className="p-4">
                  <div className="flex items-start gap-4">
                    <div className={`p-3 rounded-lg ${typeColors[notification.type] || 'text-gray-600 bg-gray-50'}`}>
                      <Icon className="h-5 w-5" />
                    </div>
                    
                    <div className="flex-1">
                      <div className="flex items-start justify-between gap-4">
                        <div>
                          <h3 className="font-semibold text-lg">{notification.title}</h3>
                          <p className="text-gray-600 mt-1">{notification.message}</p>
                          <p className="text-sm text-gray-400 mt-2">
                            {new Date(notification.created_at).toLocaleString('pt-BR')}
                          </p>
                        </div>
                        
                        {isUnread && (
                          <Button 
                            size="sm" 
                            variant="ghost"
                            onClick={() => markAsRead(notification.id)}
                          >
                            <Check className="h-4 w-4 mr-1" />
                            Marcar como lida
                          </Button>
                        )}
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            );
          })}
        </div>

        {notifications.data.length === 0 && (
          <Card>
            <CardContent className="p-12 text-center">
              <Bell className="h-16 w-16 text-gray-300 mx-auto mb-4" />
              <h3 className="text-xl font-semibold text-gray-700 mb-2">Nenhuma notificação</h3>
              <p className="text-gray-500">Você não tem notificações no momento</p>
            </CardContent>
          </Card>
        )}

        {notifications.meta.last_page > 1 && (
          <div className="flex justify-center gap-2">
            {Array.from({ length: notifications.meta.last_page }, (_, i) => i + 1).map((page) => (
              <Button
                key={page}
                variant={page === notifications.meta.current_page ? 'default' : 'outline'}
                size="sm"
                onClick={() => router.get('/notifications', { page })}
                className="min-w-[40px]"
              >
                {page}
              </Button>
            ))}
          </div>
        )}
      </div>
    </AppLayout>
  );
}
