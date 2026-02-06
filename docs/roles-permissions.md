# Sistema de Roles e Permissions

## Estrutura

### Policies
- **ProductPolicy**: Gerencia autorizações para produtos
- **CategoryPolicy**: Gerencia autorizações para categorias  
- **OrderPolicy**: Gerencia autorizações para pedidos (inclui lógica para usuário ver próprio pedido)

### Permissions
Formato: `{recurso}.{ação}`

**Produtos:**
- products.view
- products.create
- products.update
- products.delete

**Categorias:**
- categories.view
- categories.create
- categories.update
- categories.delete

**Pedidos:**
- orders.view
- orders.create
- orders.update
- orders.delete

**Tags:**
- tags.view
- tags.create
- tags.update
- tags.delete

**Usuários:**
- users.view
- users.create
- users.update
- users.delete

**Roles:**
- roles.view
- roles.create
- roles.update
- roles.delete

### Roles Padrão

**Admin:**
- Todas as permissões

**Manager:**
- Gerenciar produtos (view, create, update)
- Gerenciar categorias (view, create, update)
- Visualizar e atualizar pedidos
- Gerenciar tags (view, create, update)

**User:**
- Visualizar produtos e categorias
- Criar e visualizar próprios pedidos

## Uso

### Controllers
```php
// Autorização automática em resource controllers
public function __construct()
{
    $this->authorizeResource(Product::class, 'id');
}
```

### Policies
```php
// Verificação manual
$this->authorize('update', $product);

// Em blade/inertia
@can('update', $product)
```

### Permissions diretas
```php
// Verificar permissão
if (auth()->user()->can('products.create')) {
    // ...
}

// Middleware
Route::middleware('permission:products.create');
```

## Seeder
Execute: `php artisan db:seed --class=RolesAndPermissionsSeeder`
