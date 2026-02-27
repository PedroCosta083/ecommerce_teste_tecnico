# 🎯 Roteiro de Apresentação - Sistema E-commerce

## 📋 Índice
1. [Visão Geral do Projeto](#visão-geral)
2. [Arquitetura e Padrões](#arquitetura)
3. [Events e Listeners](#events-listeners)
4. [Jobs e Filas](#jobs-filas)
5. [Query Scopes](#query-scopes)
6. [Policies e Autorização](#policies)
7. [Testes](#testes)
8. [API e Documentação](#api)
9. [Frontend](#frontend)
10. [Destaques Técnicos](#destaques)

---

## 1️⃣ Visão Geral do Projeto {#visão-geral}

### O que é?
Sistema completo de e-commerce desenvolvido com **Laravel 12** e **React + TypeScript (Inertia.js)**, incluindo:
- Gestão de produtos, categorias, pedidos
- Carrinho de compras (guest e autenticado)
- Sistema de permissões (Spatie)
- Processamento assíncrono com Jobs
- API RESTful documentada (Swagger)
- Dashboard com métricas

### Stack Tecnológica
**Backend:**
- Laravel 12.50.0
- PHP 8.3
- PostgreSQL
- Redis (cache/queue)
- Spatie Permission

**Frontend:**
- React 19
- TypeScript
- Inertia.js
- TailwindCSS
- Shadcn/ui

**Ferramentas:**
- PHPUnit (testes)
- Swagger/OpenAPI
- Docker

---

## 2️⃣ Arquitetura e Padrões {#arquitetura}

### Arquitetura em Camadas

```
┌─────────────────────────────────────┐
│         Controllers                 │
│  (Recebe requests, valida)          │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│           Services                  │
│  (Lógica de negócio)                │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│         Repositories                │
│  (Acesso a dados)                   │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│           Models                    │
│  (Eloquent ORM)                     │
└─────────────────────────────────────┘
```

### Padrões Implementados

#### 1. Repository Pattern
**Por quê?** Desacopla lógica de negócio do acesso a dados.

```php
// Interface
interface ProductRepositoryInterface {
    public function findById(int $id): ?Product;
    public function findAll(): Collection;
}

// Implementação
class EloquentProductRepository implements ProductRepositoryInterface {
    public function findById(int $id): ?Product {
        return Product::find($id);
    }
}

// Uso no Service
class ProductService {
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}
}
```

**Benefícios:**
- Fácil trocar implementação (Eloquent → MongoDB)
- Testável (mock do repository)
- Código limpo e organizado

#### 2. DTO (Data Transfer Objects)
**Por quê?** Valida e transporta dados entre camadas.

```php
class CreateProductDTO {
    public function __construct(
        public string $name,
        public float $price,
        public int $quantity,
        public int $categoryId
    ) {}
    
    public static function fromRequest(array $data): self {
        return new self(
            name: $data['name'],
            price: $data['price'],
            quantity: $data['quantity'],
            categoryId: $data['category_id']
        );
    }
}
```

**Benefícios:**
- Type safety
- Validação centralizada
- Imutabilidade

#### 3. Service Layer
**Por quê?** Centraliza lógica de negócio.

```php
class ProductService {
    public function createProduct(CreateProductDTO $dto): Product {
        $product = $this->productRepository->create([...]);
        
        // Dispara evento
        ProductCreated::dispatch($product);
        
        return $product;
    }
}
```

---

## 3️⃣ Events e Listeners {#events-listeners}

### Como Funciona?

**Events** são disparados quando algo importante acontece.
**Listeners** escutam esses eventos e executam ações.

### Estrutura

```
Event Disparado → EventServiceProvider → Listeners Executados
```

### Exemplo Real: Criação de Produto

#### 1. Event
```php
// app/Events/ProductCreated.php
class ProductCreated {
    public function __construct(
        public Product $product
    ) {}
}
```

#### 2. Disparo do Event
```php
// app/Services/ProductService.php
public function createProduct(CreateProductDTO $dto): Product {
    $product = $this->productRepository->create([...]);
    
    // Dispara evento
    ProductCreated::dispatch($product);
    
    return $product;
}
```

#### 3. Listeners Registrados
```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    ProductCreated::class => [
        LogProductCreation::class,
        SendProductCreatedNotification::class,
    ],
];
```

#### 4. Listeners Executados
```php
// app/Listeners/LogProductCreation.php
class LogProductCreation {
    public function handle(ProductCreated $event): void {
        Log::info('Produto criado', [
            'id' => $event->product->id,
            'name' => $event->product->name
        ]);
    }
}
```

### Events Implementados

| Event | Quando Dispara | Listeners |
|-------|----------------|-----------|
| **ProductCreated** | Produto criado | LogProductCreation, SendProductCreatedNotification |
| **OrderCreated** | Pedido criado | ProcessOrderCreated, SendOrderCreatedNotification |
| **ProductStockChanged** | Estoque alterado | RecordStockMovement |
| **StockLow** | Estoque baixo | NotifyLowStock, LogLowStock, CreateLowStockNotification |

### Benefícios
✅ Desacoplamento (Service não precisa saber dos Listeners)
✅ Extensibilidade (adicionar novos Listeners sem alterar código)
✅ Organização (cada Listener tem uma responsabilidade)

---

## 4️⃣ Jobs e Filas {#jobs-filas}

### Como Funciona?

**Jobs** são tarefas executadas de forma **assíncrona** em background.

```
Request → Dispatch Job → Queue → Worker Processa
```

### Configuração
```env
QUEUE_CONNECTION=database
```

### Exemplo Real: Criação de Pedido

#### Fluxo Completo

```
1. Cliente cria pedido
   ↓
2. OrderCreated event disparado
   ↓
3. ProcessOrderCreated listener
   ↓
4. Dispara Jobs:
   - ProcessOrder (valida estoque)
   - SendOrderConfirmation (envia email)
   ↓
5. ProcessOrder dispara:
   - UpdateStock (para cada item)
```

#### 1. Job: ProcessOrder
```php
// app/Jobs/ProcessOrder.php
class ProcessOrder implements ShouldQueue {
    use Queueable;
    
    public function __construct(
        public Order $order
    ) {}
    
    public function handle(): void {
        // Valida estoque
        foreach ($this->order->items as $item) {
            if ($item->product->quantity < $item->quantity) {
                throw new Exception('Estoque insuficiente');
            }
        }
        
        // Atualiza status
        $this->order->update(['status' => 'processing']);
        
        // Dispara UpdateStock para cada item
        foreach ($this->order->items as $item) {
            UpdateStock::dispatch(
                $item->product,
                -$item->quantity,
                'venda',
                $this->order->id
            );
        }
    }
}
```

#### 2. Job: UpdateStock
```php
// app/Jobs/UpdateStock.php
class UpdateStock implements ShouldQueue {
    public function handle(): void {
        // Atualiza quantidade
        $this->product->decrement('quantity', abs($this->quantity));
        
        // Cria movimentação
        StockMovement::create([...]);
        
        // Verifica estoque baixo
        if ($this->product->quantity <= $this->product->min_quantity) {
            StockLow::dispatch($this->product);
        }
    }
}
```

#### 3. Disparo
```php
// app/Listeners/ProcessOrderCreated.php
class ProcessOrderCreated {
    public function handle(OrderCreated $event): void {
        ProcessOrder::dispatch($event->order);
        SendOrderConfirmation::dispatch($event->order);
    }
}
```

### Jobs Implementados

| Job | Responsabilidade | Queue |
|-----|------------------|-------|
| **ProcessOrder** | Valida estoque, atualiza status | default |
| **UpdateStock** | Atualiza quantidade, cria movimentação | default |
| **SendOrderConfirmation** | Envia email de confirmação | emails |

### Processar Jobs
```bash
php artisan queue:work
```

### Benefícios
✅ Performance (não trava request)
✅ Escalabilidade (múltiplos workers)
✅ Confiabilidade (retry automático)
✅ Rastreabilidade (logs de execução)

---

## 5️⃣ Query Scopes {#query-scopes}

### O que são?
Métodos reutilizáveis que encapsulam queries comuns nos Models.

### Como Funciona?

#### Definição no Model
```php
// app/Models/Product.php
class Product extends Model {
    // Scope: active
    public function scopeActive($query) {
        return $query->where('active', true);
    }
    
    // Scope: inStock
    public function scopeInStock($query) {
        return $query->where('quantity', '>', 0);
    }
    
    // Scope: lowStock
    public function scopeLowStock($query) {
        return $query->whereColumn('quantity', '<=', 'min_quantity');
    }
}
```

#### Uso
```php
// Produtos ativos
Product::active()->get();

// Produtos em estoque
Product::inStock()->get();

// Produtos com estoque baixo
Product::lowStock()->get();

// Combinando scopes
Product::active()->inStock()->get();

// Com outras queries
Product::active()
    ->inStock()
    ->where('category_id', 1)
    ->orderBy('name')
    ->get();
```

### Scopes Implementados

#### Product Model
- `active()` - Produtos ativos
- `inStock()` - Produtos em estoque (qty > 0)
- `lowStock()` - Produtos com estoque baixo (qty <= min_qty)

#### Order Model
- `byStatus($status)` - Filtrar por status
- `pending()` - Pedidos pendentes
- `byUser($userId)` - Pedidos de um usuário
- `recent()` - Ordenar por mais recentes

#### Category Model
- `active()` - Categorias ativas
- `root()` - Categorias raiz (sem parent)

### Uso nos Services
```php
// app/Services/ProductService.php
public function getLowStockProducts(): Collection {
    return Product::lowStock()->get();
}

public function getAvailableProducts(): Collection {
    return Product::active()->inStock()->get();
}

// app/Services/OrderService.php
public function getPendingOrders(): Collection {
    return Order::pending()->recent()->get();
}
```

### Benefícios
✅ Reutilização (não repete where)
✅ Legibilidade (código mais limpo)
✅ Manutenibilidade (lógica centralizada)
✅ Testabilidade (fácil testar scopes)

---


## 6️⃣ Policies e Autorização {#policies}

### Como Funciona?

**Policies** centralizam lógica de autorização integradas com **Spatie Permission**.

```
Request → Controller → Policy → Spatie → Verifica Permissão
```

### Estrutura

#### 1. User Model com Spatie
```php
// app/Models/User.php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasRoles;
    
    // Métodos disponíveis:
    // $user->hasRole('admin')
    // $user->can('products.create')
    // $user->givePermissionTo('products.view')
}
```

#### 2. Policy
```php
// app/Policies/ProductPolicy.php
class ProductPolicy {
    public function viewAny(User $user): bool {
        return $user->can('products.view');
    }
    
    public function create(User $user): bool {
        return $user->can('products.create');
    }
    
    public function update(User $user, Product $product): bool {
        return $user->can('products.update');
    }
    
    public function delete(User $user, Product $product): bool {
        return $user->can('products.delete');
    }
}
```

#### 3. Registro
```php
// app/Providers/AuthServiceProvider.php
protected $policies = [
    Product::class => ProductPolicy::class,
    Category::class => CategoryPolicy::class,
    Order::class => OrderPolicy::class,
    Tag::class => TagPolicy::class,
];
```

### Uso nos Controllers

#### Web Controllers (Inertia)
```php
// app/Http/Controllers/Web/ProductController.php
class ProductController extends Controller {
    public function __construct() {
        // Autoriza automaticamente todos os métodos
        $this->authorizeResource(Product::class, 'product');
    }
    
    // index() → viewAny
    // show() → view
    // create() → create
    // store() → create
    // edit() → update
    // update() → update
    // destroy() → delete
}
```

#### API Controllers
```php
// app/Http/Controllers/Api/ProductController.php
class ProductController extends ApiController {
    public function __construct() {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    
    public function store(CreateProductRequest $request): JsonResponse {
        $this->authorize('create', Product::class);
        // ...
    }
    
    public function update(UpdateProductRequest $request, int $id): JsonResponse {
        $product = $this->productService->getProductById($id);
        $this->authorize('update', $product);
        // ...
    }
}
```

### Permissões Disponíveis

| Recurso | Permissões |
|---------|-----------|
| **Products** | products.view, products.create, products.update, products.delete |
| **Categories** | categories.view, categories.create, categories.update, categories.delete |
| **Tags** | tags.view, tags.create, tags.update, tags.delete |
| **Orders** | orders.view, orders.create, orders.update, orders.delete |
| **Roles** | roles.view, roles.create, roles.update, roles.delete |

### Roles Padrão

| Role | Permissões |
|------|-----------|
| **admin** | Todas as permissões |
| **manager** | view, create, update (sem delete) |
| **user** | Apenas view |

### Benefícios
✅ Centralização (toda autorização em Policies)
✅ Reutilização (mesma Policy para Web e API)
✅ Integração (Spatie gerencia roles e permissions)
✅ Testabilidade (fácil testar Policies)

---

## 7️⃣ Testes {#testes}

### Cobertura de Testes

**Total: 139 testes** com **100% de sucesso**

### Estrutura

```
tests/
├── Feature/          # Testes de integração
│   ├── Auth/        # Autenticação
│   ├── Settings/    # Configurações
│   ├── *ApiTest.php # Testes de API
│   └── *Test.php    # Testes de features
└── Unit/            # Testes unitários
    ├── *ModelTest.php
    └── *ServiceTest.php
```

### Tipos de Testes Implementados

#### 1. Testes de API (Feature)
```php
// tests/Feature/ProductApiTest.php
class ProductApiTest extends TestCase {
    public function test_can_list_products() {
        $response = $this->getJson('/api/v1/products');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'price', 'quantity']
                ]
            ]);
    }
    
    public function test_can_create_product() {
        $user = User::factory()->create();
        $user->givePermissionTo('products.create');
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/products', [
                'name' => 'Test Product',
                'price' => 99.90,
                'quantity' => 10,
                'category_id' => 1
            ]);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }
}
```

#### 2. Testes de Models (Unit)
```php
// tests/Unit/ProductTest.php
class ProductTest extends TestCase {
    public function test_product_belongs_to_category() {
        $product = Product::factory()->create();
        
        $this->assertInstanceOf(Category::class, $product->category);
    }
    
    public function test_active_scope_filters_active_products() {
        Product::factory()->create(['active' => true]);
        Product::factory()->create(['active' => false]);
        
        $activeProducts = Product::active()->get();
        
        $this->assertCount(1, $activeProducts);
        $this->assertTrue($activeProducts->first()->active);
    }
}
```

#### 3. Testes de Events e Jobs
```php
// tests/Feature/EventsTest.php
class EventsTest extends TestCase {
    public function test_product_created_event_is_dispatched() {
        Event::fake([ProductCreated::class]);
        
        $product = Product::factory()->create();
        
        Event::assertDispatched(ProductCreated::class, function ($event) use ($product) {
            return $event->product->id === $product->id;
        });
    }
}

// tests/Feature/JobsTest.php
class JobsTest extends TestCase {
    public function test_process_order_job_updates_stock_and_status() {
        Queue::fake();
        
        $order = Order::factory()->create();
        
        ProcessOrder::dispatch($order);
        
        Queue::assertPushed(ProcessOrder::class);
    }
}
```

#### 4. Testes de Validação
```php
// tests/Feature/ValidationTest.php
class ValidationTest extends TestCase {
    public function test_product_requires_name() {
        $user = User::factory()->create();
        $user->givePermissionTo('products.create');
        
        $response = $this->actingAs($user)
            ->postJson('/api/v1/products', [
                'price' => 99.90,
                'quantity' => 10
            ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
```

#### 5. Testes de Autorização
```php
// tests/Feature/DashboardApiTest.php
class DashboardApiTest extends TestCase {
    public function test_admin_can_access_dashboard_metrics() {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/dashboard/metrics');
        
        $response->assertStatus(200);
    }
    
    public function test_regular_user_cannot_access_dashboard_metrics() {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard/metrics');
        
        $response->assertStatus(403);
    }
}
```

### Categorias de Testes

| Categoria | Quantidade | Descrição |
|-----------|------------|-----------|
| **Auth** | 25 | Login, logout, registro, 2FA |
| **API** | 35 | Endpoints REST (Products, Orders, Cart) |
| **Models** | 20 | Relacionamentos, scopes |
| **Events/Jobs** | 12 | Eventos e processamento assíncrono |
| **Validation** | 15 | Regras de validação |
| **Authorization** | 10 | Policies e permissões |
| **Integration** | 22 | Fluxos completos |

### Executar Testes
```bash
# Todos os testes
php artisan test

# Com cobertura
php artisan test --coverage

# Específico
php artisan test --filter ProductApiTest

# Paralelo
php artisan test --parallel
```

### Benefícios
✅ Confiança (código testado)
✅ Documentação (testes como exemplos)
✅ Refatoração segura (detecta quebras)
✅ Qualidade (bugs encontrados cedo)

---

## 8️⃣ API e Documentação {#api}

### API RESTful

**Base URL:** `http://localhost:8000/api/v1`

### Autenticação

**Laravel Sanctum** com Bearer Token

```bash
# Login
POST /api/v1/login
{
  "email": "user@example.com",
  "password": "password"
}

# Response
{
  "success": true,
  "data": {
    "user": {...},
    "token": "1|abcdef..."
  }
}

# Usar token
Authorization: Bearer 1|abcdef...
```

### Endpoints Principais

#### Products
```
GET    /api/v1/products              # Listar (público)
GET    /api/v1/products/{id}         # Ver (público)
POST   /api/v1/products              # Criar (auth)
PUT    /api/v1/products/{id}         # Atualizar (auth)
DELETE /api/v1/products/{id}         # Deletar (auth)
```

#### Orders
```
GET    /api/v1/orders                # Listar (auth)
POST   /api/v1/orders                # Criar (auth)
GET    /api/v1/orders/{id}           # Ver (auth)
PATCH  /api/v1/orders/{id}/status    # Atualizar status (auth)
```

#### Cart
```
GET    /api/v1/cart                  # Ver carrinho (público)
POST   /api/v1/cart/items            # Adicionar item (público)
PUT    /api/v1/cart/items/{id}       # Atualizar item (público)
DELETE /api/v1/cart/items/{id}       # Remover item (público)
```

### Documentação Swagger

**URL:** `http://localhost:8000/api/documentation`

#### Características
- ✅ OpenAPI 3.0
- ✅ Todos os endpoints documentados
- ✅ Schemas de request/response
- ✅ Exemplos de uso
- ✅ Autenticação Sanctum
- ✅ Try it out (testar direto)

#### Exemplo de Anotação
```php
/**
 * @OA\Post(
 *     path="/api/v1/products",
 *     tags={"Products"},
 *     summary="Cria novo produto",
 *     security={{"sanctum": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "price", "quantity"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="price", type="number"),
 *             @OA\Property(property="quantity", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Criado")
 * )
 */
```

### Gerar Documentação
```bash
php artisan l5-swagger:generate
```

---

## 9️⃣ Frontend {#frontend}

### Stack
- **React 19** + **TypeScript**
- **Inertia.js** (SPA sem API)
- **TailwindCSS** (estilização)
- **Shadcn/ui** (componentes)

### Estrutura
```
resources/js/
├── components/       # Componentes reutilizáveis
│   └── ui/          # Shadcn components
├── layouts/         # Layouts (AppLayout)
├── pages/           # Páginas Inertia
│   ├── products/
│   ├── orders/
│   ├── cart/
│   └── dashboard.tsx
└── types/           # TypeScript types
```

### Inertia.js

**Como funciona?**
```
Laravel Controller → Inertia::render() → React Component
```

#### Exemplo
```php
// Controller
public function index() {
    return Inertia::render('products/index', [
        'products' => Product::paginate(15)
    ]);
}
```

```tsx
// React Component
export default function ProductsIndex({ products }) {
    return (
        <AppLayout>
            <h1>Produtos</h1>
            {products.data.map(product => (
                <ProductCard key={product.id} product={product} />
            ))}
        </AppLayout>
    );
}
```

### Funcionalidades

#### 1. Dashboard com Métricas
- Gráficos (Chart.js)
- Cards de resumo
- Vendas por período
- Produtos com estoque baixo

#### 2. CRUD de Produtos
- Listagem com filtros
- Busca em tempo real
- Upload de imagens
- Validação client-side

#### 3. Carrinho de Compras
- Guest cart (sessão)
- Merge ao fazer login
- Atualização em tempo real
- Cálculo de totais

#### 4. Checkout
- Formulário multi-step
- Validação de CEP (ViaCEP)
- Simulação de pagamento
- Confirmação de pedido

### Componentes Reutilizáveis
- Button, Input, Select
- Card, Badge, Skeleton
- Dialog, Toast, Dropdown
- DataTable, Pagination

---


## 🔟 Destaques Técnicos {#destaques}

### 1. Query Builders Customizados

#### Repository com Query Builder
```php
// app/Repositories/Eloquent/ProductRepository.php
public function findWithFilters(ProductFilterDTO $filters): LengthAwarePaginator {
    $query = Product::query()->with(['category', 'tags']);
    
    if ($filters->search) {
        $query->where(function($q) use ($filters) {
            $q->where('name', 'like', "%{$filters->search}%")
              ->orWhere('description', 'like', "%{$filters->search}%");
        });
    }
    
    if ($filters->categoryId) {
        $query->where('category_id', $filters->categoryId);
    }
    
    if ($filters->minPrice) {
        $query->where('price', '>=', $filters->minPrice);
    }
    
    if ($filters->maxPrice) {
        $query->where('price', '<=', $filters->maxPrice);
    }
    
    $query->orderBy($filters->sortBy ?? 'created_at', $filters->sortOrder ?? 'desc');
    
    return $query->paginate($filters->perPage ?? 15);
}
```

### 2. Validações Customizadas

#### HasStock Rule
```php
// app/Rules/HasStock.php
class HasStock implements ValidationRule {
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $product = Product::find($value);
        
        if (!$product || $product->quantity <= 0) {
            $fail('Produto sem estoque disponível.');
        }
    }
}

// Uso
$request->validate([
    'product_id' => ['required', new HasStock]
]);
```

#### UniqueSlug Rule
```php
// app/Rules/UniqueSlug.php
class UniqueSlug implements ValidationRule {
    public function __construct(
        private string $table,
        private ?int $ignoreId = null
    ) {}
    
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $query = DB::table($this->table)->where('slug', $value);
        
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }
        
        if ($query->exists()) {
            $fail('Este slug já está em uso.');
        }
    }
}
```

### 3. Observers

#### ProductObserver
```php
// app/Observers/ProductObserver.php
class ProductObserver {
    public function creating(Product $product): void {
        if (empty($product->slug)) {
            $product->slug = Str::slug($product->name);
        }
    }
    
    public function updating(Product $product): void {
        if ($product->isDirty('quantity')) {
            $oldQuantity = $product->getOriginal('quantity');
            $newQuantity = $product->quantity;
            
            ProductStockChanged::dispatch($product, $oldQuantity, $newQuantity);
        }
    }
}
```

### 4. Traits Reutilizáveis

#### HasCrudResponses
```php
// app/Http/Controllers/Traits/HasCrudResponses.php
trait HasCrudResponses {
    protected function showResource($resource, string $resourceClass, string $notFoundMessage): JsonResponse {
        if (!$resource) {
            return $this->error($notFoundMessage, 404);
        }
        
        return $this->success(new $resourceClass($resource));
    }
    
    protected function paginatedResponse($paginator, string $resourceClass): JsonResponse {
        return $this->success([
            'data' => $resourceClass::collection($paginator->items())->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        ]);
    }
}
```

### 5. Cache Strategy

```php
// app/Services/ProductService.php
public function getProductById(int $id): ?Product {
    return Cache::remember(
        "product.{$id}",
        now()->addHours(1),
        fn() => $this->productRepository->findById($id)
    );
}

public function clearProductCache(int $id): void {
    Cache::forget("product.{$id}");
}
```

### 6. API Resources

```php
// app/Http/Resources/ProductResource.php
class ProductResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => number_format($this->price, 2, '.', ''),
            'quantity' => $this->quantity,
            'active' => $this->active,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
```

### 7. Form Requests

```php
// app/Http/Requests/Product/CreateProductRequest.php
class CreateProductRequest extends FormRequest {
    public function authorize(): bool {
        return true; // Policy já autoriza
    }
    
    public function rules(): array {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', new UniqueSlug('products')],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'active' => ['boolean'],
        ];
    }
    
    public function messages(): array {
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'price.min' => 'O preço deve ser maior que zero.',
        ];
    }
}
```

---

## 📊 Métricas do Projeto

### Código
- **Linhas de código:** ~15.000
- **Controllers:** 15
- **Models:** 9
- **Services:** 8
- **Repositories:** 8
- **Policies:** 5
- **Events:** 3
- **Jobs:** 3
- **Listeners:** 8

### Testes
- **Total:** 139 testes
- **Feature:** 95
- **Unit:** 44
- **Cobertura:** ~85%
- **Assertions:** 400+

### API
- **Endpoints:** 45+
- **Documentados:** 100%
- **Versionados:** v1
- **Autenticação:** Sanctum

### Frontend
- **Páginas:** 25+
- **Componentes:** 50+
- **TypeScript:** 100%

---

## 🎤 Pontos para Destacar na Entrevista

### 1. Arquitetura Limpa
> "Implementei uma arquitetura em camadas com Repository Pattern, Service Layer e DTOs, garantindo separação de responsabilidades e código testável."

### 2. Processamento Assíncrono
> "Utilizei Events, Listeners e Jobs para processar pedidos de forma assíncrona, melhorando performance e escalabilidade. Por exemplo, ao criar um pedido, disparo jobs para validar estoque, enviar emails e atualizar movimentações."

### 3. Query Scopes
> "Criei Query Scopes reutilizáveis nos models, como active(), inStock() e lowStock(), que podem ser combinados para queries complexas de forma elegante e legível."

### 4. Autorização Robusta
> "Implementei autorização completa usando Laravel Policies integradas com Spatie Permission, centralizando toda lógica de permissões e roles."

### 5. Testes Abrangentes
> "Escrevi 139 testes cobrindo API, models, events, jobs e validações, garantindo qualidade e confiabilidade do código."

### 6. API Documentada
> "Documentei toda a API usando Swagger/OpenAPI, facilitando integração e uso por outros desenvolvedores."

### 7. Frontend Moderno
> "Desenvolvi o frontend com React + TypeScript usando Inertia.js, criando uma SPA sem necessidade de API REST separada."

### 8. Boas Práticas
> "Segui PSR-12, SOLID principles, utilizei Type Hints, implementei validações customizadas e mantive código limpo e documentado."

---

## 🚀 Demonstração Sugerida

### Fluxo Completo de Pedido

1. **Adicionar produtos ao carrinho** (guest)
2. **Fazer login** (merge do carrinho)
3. **Finalizar compra** (checkout)
4. **Mostrar processamento assíncrono** (queue:work)
5. **Verificar estoque atualizado**
6. **Mostrar notificação de estoque baixo**
7. **Dashboard com métricas atualizadas**

### Mostrar Código

1. **Event + Listeners** (`OrderCreated`)
2. **Job** (`ProcessOrder`)
3. **Query Scope** (`Product::lowStock()`)
4. **Policy** (`ProductPolicy`)
5. **Teste** (`ProductApiTest`)
6. **Swagger** (documentação)

---

## 📚 Documentação Disponível

Toda documentação está em `/docs`:

- `project-structure.md` - Estrutura do projeto
- `events-listeners.md` - Events e Listeners
- `jobs-queues.md` - Jobs e Filas
- `SCOPES_USAGE.md` - Query Scopes
- `AUTHORIZATION_STANDARD.md` - Policies e Permissões
- `TESTS_COMPLETE_SUMMARY.md` - Testes
- `SWAGGER_DOCUMENTATION.md` - API
- `cart-system.md` - Sistema de carrinho
- `query-optimization.md` - Otimizações

---

## 💡 Perguntas Esperadas e Respostas

### "Por que usar Repository Pattern?"
> "Para desacoplar a lógica de negócio do acesso a dados, facilitando testes (posso mockar o repository) e permitindo trocar a implementação sem afetar o resto do código."

### "Como funciona o processamento assíncrono?"
> "Quando um pedido é criado, disparo um Event (OrderCreated). Os Listeners escutam esse evento e disparam Jobs (ProcessOrder, SendOrderConfirmation) que são processados em background por workers, não travando a requisição do usuário."

### "Como garantir a qualidade do código?"
> "Através de testes automatizados (139 testes), validações em múltiplas camadas (Form Requests, Rules customizadas), uso de Type Hints, Policies para autorização e code review."

### "Como escalar o sistema?"
> "O sistema já está preparado para escalar: usa filas para processamento assíncrono (posso adicionar mais workers), cache para queries pesadas, API stateless com Sanctum, e arquitetura desacoplada."

### "Qual foi o maior desafio?"
> "Implementar o fluxo completo de pedidos com validação de estoque, processamento assíncrono e notificações, garantindo consistência dos dados e tratamento de erros em cada etapa."

---

## ✅ Checklist Final

Antes da apresentação, certifique-se:

- [ ] Projeto rodando localmente
- [ ] Banco de dados com dados de exemplo
- [ ] Queue worker rodando (`php artisan queue:work`)
- [ ] Swagger acessível (`/api/documentation`)
- [ ] Testes passando (`php artisan test`)
- [ ] Código commitado e organizado
- [ ] Documentação atualizada
- [ ] Preparar demonstração do fluxo completo
- [ ] Revisar código dos principais arquivos
- [ ] Preparar respostas para perguntas técnicas

---

## 🎯 Conclusão

Este projeto demonstra:

✅ **Domínio de Laravel** (Events, Jobs, Policies, Scopes)
✅ **Arquitetura limpa** (Repository, Service, DTO)
✅ **Boas práticas** (SOLID, PSR-12, Type Hints)
✅ **Testes** (Feature, Unit, Integration)
✅ **API RESTful** (Documentada com Swagger)
✅ **Frontend moderno** (React + TypeScript)
✅ **Processamento assíncrono** (Jobs e Filas)
✅ **Autorização robusta** (Policies + Spatie)

**Boa sorte na entrevista! 🚀**
