# Fluxo de Produto - Arquitetura em Camadas

## Visão Geral

Este documento descreve o fluxo completo do módulo de produtos, implementado seguindo o padrão de arquitetura em camadas com inversão de dependência.

## Arquitetura

```
Request → Form Request → Controller → Service → Repository → Model
                ↓           ↓          ↓
              DTO      Resource    Interface
```

## Componentes

### 1. DTOs (Data Transfer Objects)

#### CreateProductDTO
```php
namespace App\DTOs\Product;

class CreateProductDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public float $price,
        public float $costPrice,
        public int $quantity,
        public int $minQuantity,
        public int $categoryId,
        public bool $active = true,
        public array $tagIds = []
    ) {}
}
```

#### UpdateProductDTO
```php
namespace App\DTOs\Product;

class UpdateProductDTO
{
    // Todos os campos opcionais para atualização parcial
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        // ... outros campos
    ) {}
}
```

#### ProductFilterDTO
```php
namespace App\DTOs\Product;

class ProductFilterDTO
{
    // Filtros para busca e paginação
    public function __construct(
        public ?string $search = null,
        public ?int $categoryId = null,
        public ?array $tagIds = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ?bool $active = null,
        public ?string $sortBy = 'name',
        public ?string $sortDirection = 'asc',
        public int $perPage = 15
    ) {}
}
```

### 2. Form Requests

#### CreateProductRequest
- **Responsabilidade**: Validação de dados para criação
- **Regras**: Campos obrigatórios, tipos, unicidade do slug
- **Autorização**: Controle de acesso

#### UpdateProductRequest
- **Responsabilidade**: Validação de dados para atualização
- **Regras**: Campos opcionais, unicidade ignorando o próprio registro
- **Autorização**: Controle de acesso

### 3. Controller

#### ProductController
```php
namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    // GET /api/products
    public function index(Request $request): JsonResponse
    
    // GET /api/products/{id}
    public function show(int $id): JsonResponse
    
    // POST /api/products
    public function store(CreateProductRequest $request): JsonResponse
    
    // PUT /api/products/{id}
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    
    // DELETE /api/products/{id}
    public function destroy(int $id): JsonResponse
}
```

**Responsabilidades**:
- Receber requisições HTTP
- Validar dados via Form Requests
- Converter dados em DTOs
- Chamar métodos do Service
- Retornar responses formatados via Resources

### 4. Service

#### ProductService
```php
namespace App\Services;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}
    
    public function createProduct(CreateProductDTO $dto): Product
    public function updateProduct(int $id, UpdateProductDTO $dto): ?Product
    public function deleteProduct(int $id): bool
    public function getProductsWithFilters(ProductFilterDTO $filters): LengthAwarePaginator
    // ... outros métodos
}
```

**Responsabilidades**:
- Lógica de negócio
- Orquestração de operações
- Gerenciamento de relacionamentos (tags)
- Validações de negócio
- Transformação de DTOs em dados para persistência

### 5. Repository

#### Interface
```php
namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function findAll(): Collection;
    public function findById(int $id): ?Product;
    public function findWithFilters(ProductFilterDTO $filters): LengthAwarePaginator;
    public function create(array $data): Product;
    public function update(Product $product, array $data): bool;
    public function delete(Product $product): bool;
}
```

#### Implementação
```php
namespace App\Repositories\Eloquent;

class ProductRepository implements ProductRepositoryInterface
{
    // Implementação usando Eloquent ORM
}
```

**Responsabilidades**:
- Acesso a dados
- Queries complexas
- Relacionamentos
- Paginação

### 6. Resources

#### ProductResource
```php
namespace App\Http\Resources;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            // ... outros campos
        ];
    }
}
```

**Responsabilidades**:
- Formatação de dados para API
- Controle de campos expostos
- Relacionamentos condicionais
- Transformação de dados

## Fluxos de Operação

### Criar Produto

1. **Request**: `POST /api/products`
2. **Form Request**: `CreateProductRequest` valida dados
3. **Controller**: Converte request em `CreateProductDTO`
4. **Service**: Processa DTO, cria produto e associa tags
5. **Repository**: Persiste dados no banco
6. **Resource**: Formata resposta
7. **Response**: JSON com produto criado

### Listar Produtos

1. **Request**: `GET /api/products?search=termo&category_id=1`
2. **Controller**: Converte query params em `ProductFilterDTO`
3. **Service**: Aplica filtros via repository
4. **Repository**: Executa query com filtros e paginação
5. **Resource**: Formata lista de produtos
6. **Response**: JSON com dados paginados

### Atualizar Produto

1. **Request**: `PUT /api/products/{id}`
2. **Form Request**: `UpdateProductRequest` valida dados
3. **Controller**: Converte request em `UpdateProductDTO`
4. **Service**: Busca produto, aplica mudanças, atualiza tags
5. **Repository**: Persiste alterações
6. **Resource**: Formata produto atualizado
7. **Response**: JSON com produto atualizado

## Vantagens da Arquitetura

### Separação de Responsabilidades
- Cada camada tem uma responsabilidade específica
- Facilita manutenção e testes

### Inversão de Dependência
- Service depende de interface, não implementação
- Facilita testes unitários e troca de implementações

### Reutilização
- DTOs podem ser reutilizados em diferentes contextos
- Services podem ser usados por diferentes controllers

### Testabilidade
- Cada camada pode ser testada isoladamente
- Mocks podem ser facilmente criados para interfaces

### Flexibilidade
- Fácil adição de novas funcionalidades
- Mudanças em uma camada não afetam outras

## Registro de Dependências

```php
// RepositoryServiceProvider
$this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
```

Isso permite que o Laravel injete automaticamente a implementação correta quando a interface for solicitada.