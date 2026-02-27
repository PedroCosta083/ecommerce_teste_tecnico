# Padronização de Autorização com Policies + Spatie Permission

## 📋 Visão Geral

Todas as autorizações do sistema utilizam **Laravel Policies** integradas com **Spatie Laravel Permission** de forma padronizada, eliminando o uso direto de middlewares de permissão nos controllers.

**Pacote:** `spatie/laravel-permission` v6.24.0

---

## 🎯 Padrão Adotado

### Controllers Web (Inertia)
Usam `authorizeResource()` no construtor:

```php
public function __construct(
    private ProductService $productService
) {
    $this->authorizeResource(Product::class, 'product');
}
```

### Controllers API (JSON)
Usam `authorize()` em cada método + middleware auth:

```php
public function __construct(
    private ProductService $productService
) {
    $this->middleware('auth:sanctum')->except(['index', 'show']);
}

public function store(CreateProductRequest $request): JsonResponse
{
    $this->authorize('create', Product::class);
    // ...
}

public function update(UpdateProductRequest $request, int $id): JsonResponse
{
    $product = $this->productService->getProductById($id);
    $this->authorize('update', $product);
    // ...
}
```

---

## 📚 Como Funciona

### Spatie Permission
O sistema usa o Spatie para gerenciar:
- **Roles** (Papéis): admin, manager, user
- **Permissions** (Permissões): products.view, products.create, etc.

### User Model
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    
    // Métodos disponíveis:
    // $user->hasRole('admin')
    // $user->can('products.create')
    // $user->givePermissionTo('products.view')
    // $user->assignRole('manager')
}
```

### Policies
As Policies usam `$user->can()` do Spatie:

```php
public function create(User $user): bool
{
    return $user->can('products.create'); // Spatie verifica permissão
}
```

---

## 📁 Estrutura de Policies

### ProductPolicy
```php
public function viewAny(User $user): bool
{
    return $user->can('products.view');
}

public function view(User $user, Product $product): bool
{
    return $user->can('products.view');
}

public function create(User $user): bool
{
    return $user->can('products.create');
}

public function update(User $user, Product $product): bool
{
    return $user->can('products.update');
}

public function delete(User $user, Product $product): bool
{
    return $user->can('products.delete');
}
```

### DashboardPolicy
```php
public function viewMetrics(User $user): bool
{
    return $user->can('products.view');
}
```

---

## ✅ Controllers Padronizados

### Web Controllers
- ✅ `ProductController` - `authorizeResource(Product::class)`
- ✅ `CategoryController` - `authorizeResource(Category::class)`
- ✅ `TagController` - `authorizeResource(Tag::class)`
- ✅ `OrderController` - `authorizeResource(Order::class)`

### API Controllers
- ✅ `ProductController` - `authorize()` + middleware
- ✅ `DashboardController` - `authorize('viewMetrics')`
- ✅ `CategoryController` - Rotas públicas (index, show)
- ✅ `TagController` - Rotas públicas (index, show)
- ✅ `OrderController` - Protegido por auth:sanctum
- ✅ `CartController` - Rotas públicas
- ✅ `StockMovementController` - Protegido por auth:sanctum

---

## 🔐 Permissões Disponíveis

### Products
- `products.view` - Visualizar produtos
- `products.create` - Criar produtos
- `products.update` - Atualizar produtos
- `products.delete` - Excluir produtos

### Categories
- `categories.view`
- `categories.create`
- `categories.update`
- `categories.delete`

### Tags
- `tags.view`
- `tags.create`
- `tags.update`
- `tags.delete`

### Orders
- `orders.view`
- `orders.create`
- `orders.update`
- `orders.delete`

---

## 🚫 O que NÃO fazer

### ❌ Middleware direto no construtor
```php
// EVITE
public function __construct()
{
    $this->middleware('can:products.view');
}
```

### ❌ Verificação manual de permissão
```php
// EVITE
public function store(Request $request)
{
    if (!auth()->user()->can('products.create')) {
        abort(403);
    }
}
```

---

## ✅ O que FAZER

### ✅ Use authorizeResource (Web)
```php
public function __construct()
{
    $this->authorizeResource(Product::class, 'product');
}
```

### ✅ Use authorize() (API)
```php
public function store(CreateProductRequest $request)
{
    $this->authorize('create', Product::class);
    // ...
}
```

---

## 🎨 Benefícios

1. **Centralização**: Toda lógica de autorização em Policies
2. **Reutilização**: Mesma Policy para Web e API
3. **Testabilidade**: Fácil testar Policies isoladamente
4. **Manutenibilidade**: Mudanças em um único lugar
5. **Clareza**: Código mais limpo e legível

---

## 📝 Checklist de Implementação

Ao criar um novo controller:

- [ ] Criar Policy correspondente
- [ ] Web: Adicionar `authorizeResource()` no construtor
- [ ] API: Adicionar middleware `auth:sanctum`
- [ ] API: Adicionar `authorize()` nos métodos protegidos
- [ ] Testar todas as permissões
- [ ] Documentar no Swagger (API)

---

## 🧪 Testando Autorização

```php
// Em testes
$user = User::factory()->create();
$user->givePermissionTo('products.create');

$this->actingAs($user)
    ->post('/products', $data)
    ->assertSuccessful();

// Sem permissão
$user2 = User::factory()->create();

$this->actingAs($user2)
    ->post('/products', $data)
    ->assertForbidden();
```

---

**Última Atualização:** 2024-02-27  
**Mantido por:** Development Team
