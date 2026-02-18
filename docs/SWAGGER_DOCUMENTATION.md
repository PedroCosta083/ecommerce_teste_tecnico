# Documentação Swagger/OpenAPI - E-commerce API

## 📋 Visão Geral

API RESTful completa para sistema de e-commerce profissional com gestão de produtos, categorias, pedidos, carrinho, estoque e autenticação. Inclui sistema de eventos, jobs assíncronos e notificações.

**Versão:** 1.0.0  
**Base URL (Dev):** `http://localhost:8000/api/v1`  
**Base URL (Prod):** `https://api.ecommerce.com/v1`  
**Swagger UI:** `http://localhost:8000/api/documentation`

---

## 🔐 Autenticação

A API utiliza **Laravel Sanctum** com Bearer Token authentication.

### Como Autenticar

1. **Obter Token:**
```bash
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123",
  "device_name": "iPhone 13"
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "João Silva",
      "email": "user@example.com"
    },
    "token": "1|abcdef123456..."
  }
}
```

2. **Usar Token nas Requisições:**
```bash
Authorization: Bearer 1|abcdef123456...
```

---

## 📚 Endpoints Principais

### Authentication

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/auth/login` | Autentica usuário e retorna token |
| POST | `/auth/logout` | Revoga token atual |
| GET | `/auth/me` | Retorna dados do usuário autenticado |

### Products

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/products` | Lista produtos com filtros e paginação |
| GET | `/products/{id}` | Obtém detalhes de um produto |
| POST | `/products` | Cria novo produto |
| PUT | `/products/{id}` | Atualiza produto existente |
| DELETE | `/products/{id}` | Remove produto |

### Categories

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/categories` | Lista todas as categorias |
| GET | `/categories/{id}` | Obtém detalhes de uma categoria |
| POST | `/categories` | Cria nova categoria |
| PUT | `/categories/{id}` | Atualiza categoria |
| DELETE | `/categories/{id}` | Remove categoria |
| GET | `/categories/{id}/products` | Lista produtos de uma categoria |

### Cart

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/cart` | Obtém carrinho do usuário |
| POST | `/cart/items` | Adiciona item ao carrinho |
| PUT | `/cart/items/{id}` | Atualiza quantidade de item |
| DELETE | `/cart/items/{id}` | Remove item do carrinho |
| DELETE | `/cart/{id}` | Limpa todo o carrinho |

### Orders

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/orders` | Lista pedidos com filtros |
| GET | `/orders/{id}` | Obtém detalhes de um pedido |
| POST | `/orders` | Cria novo pedido |
| PUT | `/orders/{id}` | Atualiza pedido |
| PATCH | `/orders/{id}/status` | Atualiza status do pedido |
| DELETE | `/orders/{id}` | Remove pedido |

---

## 🔍 Exemplos Detalhados

### 1. Listar Produtos com Filtros

```bash
GET /api/v1/products?search=notebook&category_id=1&min_price=1000&max_price=5000&sort_by=price&sort_order=asc&page=1&per_page=15
Authorization: Bearer {token}
```

**Parâmetros Query:**
- `search` (string): Termo de busca
- `category_id` (integer): ID da categoria
- `min_price` (float): Preço mínimo
- `max_price` (float): Preço máximo
- `sort_by` (string): Campo para ordenação (name, price, created_at)
- `sort_order` (string): Direção (asc, desc)
- `page` (integer): Número da página
- `per_page` (integer): Itens por página

**Resposta 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Notebook Dell Inspiron 15",
      "slug": "notebook-dell-inspiron-15",
      "description": "Notebook com processador Intel i7, 16GB RAM, SSD 512GB",
      "price": 3499.90,
      "cost_price": 2800.00,
      "quantity": 50,
      "min_quantity": 10,
      "category_id": 1,
      "active": true,
      "created_at": "2024-01-01T08:00:00Z",
      "updated_at": "2024-01-15T14:20:00Z",
      "category": {
        "id": 1,
        "name": "Eletrônicos",
        "slug": "eletronicos"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 73
  }
}
```

### 2. Criar Produto

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

**Resposta 201:**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 1,
    "name": "Notebook Dell Inspiron 15",
    "slug": "notebook-dell-inspiron-15",
    "price": 3499.90,
    "quantity": 50,
    "category_id": 1,
    "active": true,
    "created_at": "2024-01-15T10:00:00Z"
  }
}
```

**Eventos Disparados:**
- `ProductCreated`: Registra log e notifica admins

### 3. Adicionar Item ao Carrinho

```bash
POST /api/v1/cart/items
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 1,
  "product_id": 5,
  "quantity": 2
}
```

**Resposta 201:**
```json
{
  "success": true,
  "message": "Item added to cart",
  "data": {
    "item_id": 10
  }
}
```

**Validações:**
- Verifica estoque disponível
- Valida produto ativo
- Cria carrinho se não existir

### 4. Criar Pedido (Fluxo Completo)

```bash
POST /api/v1/orders
Authorization: Bearer {token}
Content-Type: application/json

{
  "items": [
    {
      "product_id": 5,
      "quantity": 2,
      "price": 3499.90
    },
    {
      "product_id": 8,
      "quantity": 1,
      "price": 150.00
    }
  ],
  "shipping_address": "Rua ABC, 123, São Paulo, SP, 01234-567",
  "billing_address": "Rua ABC, 123, São Paulo, SP, 01234-567",
  "notes": "Entregar no período da manhã"
}
```

**Resposta 201:**
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "status": "pending",
    "total_amount": 7149.80,
    "shipping_address": "Rua ABC, 123, São Paulo, SP, 01234-567",
    "billing_address": "Rua ABC, 123, São Paulo, SP, 01234-567",
    "notes": "Entregar no período da manhã",
    "created_at": "2024-01-15T11:00:00Z",
    "items": [
      {
        "id": 1,
        "product_id": 5,
        "quantity": 2,
        "price": 3499.90,
        "subtotal": 6999.80
      },
      {
        "id": 2,
        "product_id": 8,
        "quantity": 1,
        "price": 150.00,
        "subtotal": 150.00
      }
    ]
  }
}
```

**Fluxo Assíncrono Disparado:**
1. **Evento:** `OrderCreated`
2. **Listener:** `ProcessOrderCreated`
   - Dispara `ProcessOrder` Job
   - Dispara `SendOrderConfirmation` Job
3. **Job:** `ProcessOrder`
   - Valida estoque
   - Atualiza status para "processing"
   - Dispara `UpdateStock` Job para cada item
4. **Job:** `UpdateStock`
   - Atualiza quantidade em estoque
   - Cria `StockMovement` (tipo: venda)
   - Verifica estoque baixo e dispara `StockLow` Event se necessário
5. **Job:** `SendOrderConfirmation`
   - Envia email de confirmação
   - Registra logs

### 5. Atualizar Status do Pedido

```bash
PATCH /api/v1/orders/1/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "shipped"
}
```

**Status Válidos:**
- `pending`: Pendente
- `processing`: Processando
- `shipped`: Enviado
- `delivered`: Entregue
- `cancelled`: Cancelado

**Resposta 200:**
```json
{
  "success": true,
  "message": "Order status updated successfully",
  "data": {
    "id": 1,
    "status": "shipped",
    "updated_at": "2024-01-15T15:30:00Z"
  }
}
```

---

## 📊 Schemas de Dados

### Product
```json
{
  "id": 1,
  "name": "string",
  "slug": "string",
  "description": "string",
  "price": 0.00,
  "cost_price": 0.00,
  "quantity": 0,
  "min_quantity": 0,
  "category_id": 0,
  "active": true,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z",
  "category": {}
}
```

### Category
```json
{
  "id": 1,
  "name": "string",
  "slug": "string",
  "description": "string",
  "parent_id": null,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z",
  "children": [],
  "products_count": 0
}
```

### Order
```json
{
  "id": 1,
  "user_id": 1,
  "status": "pending",
  "total_amount": 0.00,
  "shipping_address": "string",
  "billing_address": "string",
  "notes": "string",
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z",
  "user": {},
  "items": []
}
```

---

## ⚠️ Códigos de Erro

| Código | Descrição |
|--------|-----------|
| 200 | Sucesso |
| 201 | Criado com sucesso |
| 400 | Requisição inválida |
| 401 | Não autenticado |
| 403 | Não autorizado |
| 404 | Recurso não encontrado |
| 422 | Erro de validação |
| 500 | Erro interno do servidor |

**Formato de Erro:**
```json
{
  "success": false,
  "message": "Resource not found",
  "error": "Not Found"
}
```

**Erro de Validação:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

## 🚀 Recursos Avançados

### Jobs Assíncronos
- `ProcessOrder`: Processa pedidos e valida estoque
- `UpdateStock`: Atualiza estoque e cria movimentações
- `SendOrderConfirmation`: Envia emails de confirmação

### Eventos e Listeners
- `ProductCreated` → LogProductCreation, SendProductCreatedNotification
- `OrderCreated` → ProcessOrderCreated, SendOrderCreatedNotification
- `StockLow` → NotifyLowStock, LogLowStock

### Validações
- Estoque disponível antes de adicionar ao carrinho
- Validação de estoque antes de criar pedido
- Validação de dados com Form Requests
- Políticas de autorização com Policies

---

## 📝 Notas de Implementação

1. **Queue Connection:** database (configurado no .env)
2. **Processar Jobs:** `php artisan queue:work`
3. **Arquitetura:** DTOs, Services, Repositories, Resources, Policies
4. **Testes:** 89 testes passando (282 assertions)
5. **Docker:** Containers para app, nginx, postgres, node

---

## 🔗 Links Úteis

- **Swagger UI:** http://localhost:8000/api/documentation
- **JSON Spec:** http://localhost:8000/docs/api-docs.json
- **Repositório:** [GitHub]
- **Postman Collection:** [Link]

---

**Última Atualização:** 2024-01-15  
**Mantido por:** API Support Team (api@ecommerce.com)
