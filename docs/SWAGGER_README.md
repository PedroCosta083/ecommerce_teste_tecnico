# 📘 Swagger/OpenAPI Documentation - E-commerce API

## 🎯 Visão Geral

Documentação interativa completa da API RESTful do sistema de e-commerce. Interface Swagger UI profissional com todos os endpoints, schemas, exemplos e autenticação integrada.

## 🚀 Acesso Rápido

### URLs de Acesso

- **Swagger UI (Interface Interativa):** http://localhost:8000/api/documentation
- **JSON Specification:** http://localhost:8000/docs/api-docs.json
- **API Base URL:** http://localhost:8000/api/v1

### Credenciais de Teste

```
Email: admin@example.com
Password: password
```

## 📋 Recursos Documentados

### ✅ Endpoints Completos

- **Authentication (3 endpoints)**
  - POST `/auth/login` - Login e obtenção de token
  - POST `/auth/logout` - Logout e revogação de token
  - GET `/auth/me` - Dados do usuário autenticado

- **Products (5 endpoints)**
  - GET `/products` - Listar com filtros avançados
  - GET `/products/{id}` - Detalhes do produto
  - POST `/products` - Criar produto
  - PUT `/products/{id}` - Atualizar produto
  - DELETE `/products/{id}` - Remover produto

- **Categories (6 endpoints)**
  - GET `/categories` - Listar categorias
  - GET `/categories/{id}` - Detalhes da categoria
  - POST `/categories` - Criar categoria
  - PUT `/categories/{id}` - Atualizar categoria
  - DELETE `/categories/{id}` - Remover categoria
  - GET `/categories/{id}/products` - Produtos da categoria

- **Cart (5 endpoints)**
  - GET `/cart` - Obter carrinho
  - POST `/cart/items` - Adicionar item
  - PUT `/cart/items/{id}` - Atualizar quantidade
  - DELETE `/cart/items/{id}` - Remover item
  - DELETE `/cart/{id}` - Limpar carrinho

- **Orders (6 endpoints)**
  - GET `/orders` - Listar pedidos
  - GET `/orders/{id}` - Detalhes do pedido
  - POST `/orders` - Criar pedido
  - PUT `/orders/{id}` - Atualizar pedido
  - PATCH `/orders/{id}/status` - Atualizar status
  - DELETE `/orders/{id}` - Remover pedido

### ✅ Schemas Completos

- User
- Product
- Category
- Cart / CartItem
- Order / OrderItem
- StockMovement
- ValidationError
- ErrorResponse
- SuccessResponse

### ✅ Recursos Profissionais

- ✔️ Autenticação Sanctum integrada
- ✔️ Exemplos de requisição/resposta
- ✔️ Descrições detalhadas em português
- ✔️ Códigos de status HTTP
- ✔️ Validações documentadas
- ✔️ Filtros e paginação
- ✔️ Relacionamentos entre entidades
- ✔️ Fluxos assíncronos (Jobs/Events)

## 🔐 Como Usar a Autenticação

### Passo 1: Obter Token

1. Acesse http://localhost:8000/api/documentation
2. Localize o endpoint `POST /auth/login`
3. Clique em "Try it out"
4. Preencha:
```json
{
  "email": "admin@example.com",
  "password": "password",
  "device_name": "Swagger UI"
}
```
5. Clique em "Execute"
6. Copie o token da resposta

### Passo 2: Autorizar no Swagger

1. Clique no botão **"Authorize"** (cadeado verde no topo)
2. Cole o token no formato: `Bearer {seu_token}`
3. Clique em "Authorize"
4. Agora todos os endpoints protegidos estão acessíveis

### Passo 3: Testar Endpoints

Todos os endpoints agora podem ser testados diretamente pela interface:
- Clique em qualquer endpoint
- "Try it out"
- Preencha os parâmetros
- "Execute"

## 📊 Exemplos de Uso

### Exemplo 1: Criar Produto

```bash
POST /api/v1/products
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Notebook Dell Inspiron 15",
  "slug": "notebook-dell-inspiron-15",
  "description": "Notebook com processador Intel i7, 16GB RAM, SSD 512GB",
  "price": 3499.90,
  "cost_price": 2800.00,
  "quantity": 50,
  "min_quantity": 10,
  "category_id": 1,
  "active": true
}
```

### Exemplo 2: Buscar Produtos

```bash
GET /api/v1/products?search=notebook&category_id=1&min_price=1000&max_price=5000&sort_by=price&sort_order=asc&page=1
Authorization: Bearer {token}
```

### Exemplo 3: Criar Pedido

```bash
POST /api/v1/orders
Authorization: Bearer {token}
Content-Type: application/json

{
  "items": [
    {
      "product_id": 5,
      "quantity": 2
    }
  ],
  "shipping_address": "Rua ABC, 123, São Paulo, SP, 01234-567",
  "billing_address": "Rua ABC, 123, São Paulo, SP, 01234-567",
  "notes": "Entregar no período da manhã"
}
```

## 🛠️ Configuração Técnica

### Instalação

O Swagger já está instalado e configurado. Para reinstalar:

```bash
# Instalar pacote
docker exec teste_tecnico_app composer require darkaonline/l5-swagger

# Publicar configuração
docker exec teste_tecnico_app php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"

# Gerar documentação
docker exec teste_tecnico_app php artisan l5-swagger:generate
```

### Arquivos de Configuração

- **Config:** `config/l5-swagger.php`
- **Controllers com Anotações:**
  - `app/Http/Controllers/SwaggerController.php` (Info geral)
  - `app/Http/Controllers/Api/AuthController.php`
  - `app/Http/Controllers/Api/ProductController.php`
  - `app/Http/Controllers/Api/CategoryController.php`
  - `app/Http/Controllers/Api/CartController.php`
  - `app/Http/Controllers/Api/OrderController.php`
- **Schemas:** `app/Http/Controllers/Api/Schemas/Schemas.php`
- **Documentação Gerada:** `storage/api-docs/api-docs.json`

### Regenerar Documentação

```bash
docker exec teste_tecnico_app php artisan l5-swagger:generate
```

## 📝 Estrutura das Anotações

### Exemplo de Endpoint Documentado

```php
/**
 * @OA\Get(
 *     path="/products",
 *     tags={"Products"},
 *     summary="Lista produtos com filtros e paginação",
 *     description="Retorna lista paginada de produtos com opções de busca",
 *     operationId="getProducts",
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Termo de busca",
 *         required=false,
 *         @OA\Schema(type="string", example="notebook")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de produtos",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Não autenticado")
 * )
 */
public function index(Request $request): JsonResponse
```

## 🎨 Personalização

### Tema Dark Mode

Edite `config/l5-swagger.php`:

```php
'ui' => [
    'display' => [
        'dark_mode' => true,
    ],
],
```

### Filtros Habilitados

```php
'ui' => [
    'display' => [
        'filter' => true,
    ],
],
```

### Expansão Automática

```php
'ui' => [
    'display' => [
        'doc_expansion' => 'list', // 'list', 'full', 'none'
    ],
],
```

## 🔍 Filtros e Parâmetros Avançados

### Products

- `search`: Busca por nome/descrição
- `category_id`: Filtrar por categoria
- `min_price` / `max_price`: Faixa de preço
- `sort_by`: Ordenar por (name, price, created_at)
- `sort_order`: Direção (asc, desc)
- `page` / `per_page`: Paginação

### Orders

- `user_id`: Filtrar por usuário
- `status`: Filtrar por status (pending, processing, shipped, delivered, cancelled)
- `start_date` / `end_date`: Período
- `page` / `per_page`: Paginação

## 📈 Status Codes

| Código | Significado |
|--------|-------------|
| 200 | OK - Sucesso |
| 201 | Created - Recurso criado |
| 400 | Bad Request - Requisição inválida |
| 401 | Unauthorized - Não autenticado |
| 403 | Forbidden - Sem permissão |
| 404 | Not Found - Recurso não encontrado |
| 422 | Unprocessable Entity - Erro de validação |
| 500 | Internal Server Error - Erro no servidor |

## 🚦 Fluxos Assíncronos Documentados

### Criar Pedido

1. **Request:** POST `/orders`
2. **Validação:** Estoque disponível
3. **Criação:** Order com status "pending"
4. **Event:** `OrderCreated` disparado
5. **Listeners:**
   - `ProcessOrderCreated` → Dispara Jobs
6. **Jobs:**
   - `ProcessOrder` → Valida e processa
   - `UpdateStock` → Atualiza estoque (para cada item)
   - `SendOrderConfirmation` → Envia email
7. **Response:** Order criado

### Estoque Baixo

1. **Job:** `UpdateStock` executa
2. **Verificação:** `quantity < min_quantity`
3. **Event:** `StockLow` disparado
4. **Listeners:**
   - `NotifyLowStock` → Alerta admins
   - `LogLowStock` → Registra log

## 📚 Recursos Adicionais

- **Documentação Markdown:** `docs/SWAGGER_DOCUMENTATION.md`
- **Guia de API:** `docs/swagger-api.md`
- **Jobs:** `docs/jobs-queues.md`
- **Eventos:** `docs/events-listeners.md`

## 🐛 Troubleshooting

### Swagger UI não carrega

```bash
# Limpar cache
docker exec teste_tecnico_app php artisan cache:clear
docker exec teste_tecnico_app php artisan config:clear

# Regenerar documentação
docker exec teste_tecnico_app php artisan l5-swagger:generate
```

### Erro 401 em todos os endpoints

- Verifique se o token está correto
- Clique em "Authorize" e cole o token com prefixo "Bearer "
- Faça logout e login novamente

### Documentação desatualizada

```bash
# Forçar regeneração
docker exec teste_tecnico_app php artisan l5-swagger:generate --force
```

## 📞 Suporte

- **Email:** api@ecommerce.com
- **Documentação:** http://localhost:8000/api/documentation
- **Issues:** [GitHub Issues]

---

**Versão:** 1.0.0  
**Última Atualização:** 2024-01-15  
**Mantido por:** API Support Team
