# 🧪 Guia de Testes - E-commerce API

## 📋 Índice

1. [Configuração Inicial](#configuração-inicial)
2. [Testes de Autenticação](#testes-de-autenticação)
3. [Testes de Produtos](#testes-de-produtos)
4. [Testes de Categorias](#testes-de-categorias)
5. [Testes de Carrinho](#testes-de-carrinho)
6. [Testes de Pedidos](#testes-de-pedidos)
7. [Testes de Fluxo Completo](#testes-de-fluxo-completo)

---

## Configuração Inicial

### Variáveis de Ambiente

```bash
BASE_URL=http://localhost:8000/api/v1
TOKEN=seu_token_aqui
```

### Headers Padrão

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {TOKEN}
```

---

## Testes de Autenticação

### 1. Login (Obter Token)

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password",
    "device_name": "API Test"
  }'
```

**Resposta Esperada (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com"
    },
    "token": "1|abcdef123456..."
  }
}
```

**Salve o token para usar nos próximos testes!**

### 2. Obter Dados do Usuário Autenticado

```bash
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 3. Logout

```bash
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

---

## Testes de Produtos

### 1. Listar Todos os Produtos

```bash
curl -X GET "http://localhost:8000/api/v1/products" \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 2. Listar Produtos com Filtros

```bash
curl -X GET "http://localhost:8000/api/v1/products?search=notebook&category_id=1&min_price=1000&max_price=5000&sort_by=price&sort_order=asc&page=1&per_page=10" \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 3. Obter Produto Específico

```bash
curl -X GET "http://localhost:8000/api/v1/products/1" \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 4. Criar Produto

```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Notebook Dell Inspiron 15",
    "slug": "notebook-dell-inspiron-15",
    "description": "Notebook com processador Intel i7, 16GB RAM, SSD 512GB",
    "price": 3499.90,
    "cost_price": 2800.00,
    "quantity": 50,
    "min_quantity": 10,
    "category_id": 1,
    "active": true
  }'
```

**Eventos Disparados:**
- `ProductCreated` → LogProductCreation
- `ProductCreated` → SendProductCreatedNotification

### 5. Atualizar Produto

```bash
curl -X PUT http://localhost:8000/api/v1/products/1 \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Notebook Dell Inspiron 15 - Atualizado",
    "price": 3299.90,
    "quantity": 45
  }'
```

### 6. Deletar Produto

```bash
curl -X DELETE http://localhost:8000/api/v1/products/1 \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

---

## Testes de Categorias

### 1. Listar Categorias

```bash
curl -X GET http://localhost:8000/api/v1/categories \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 2. Criar Categoria

```bash
curl -X POST http://localhost:8000/api/v1/categories \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Eletrônicos",
    "slug": "eletronicos",
    "description": "Produtos eletrônicos e tecnologia",
    "parent_id": null
  }'
```

### 3. Criar Subcategoria

```bash
curl -X POST http://localhost:8000/api/v1/categories \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Notebooks",
    "slug": "notebooks",
    "description": "Notebooks e laptops",
    "parent_id": 1
  }'
```

### 4. Listar Produtos de uma Categoria

```bash
curl -X GET "http://localhost:8000/api/v1/categories/1/products?page=1" \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 5. Atualizar Categoria

```bash
curl -X PUT http://localhost:8000/api/v1/categories/1 \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Eletrônicos e Informática",
    "description": "Produtos eletrônicos, informática e tecnologia"
  }'
```

### 6. Deletar Categoria

```bash
curl -X DELETE http://localhost:8000/api/v1/categories/1 \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

---

## Testes de Carrinho

### 1. Obter Carrinho do Usuário

```bash
curl -X GET "http://localhost:8000/api/v1/cart?user_id=1" \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 2. Adicionar Item ao Carrinho

```bash
curl -X POST http://localhost:8000/api/v1/cart/items \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "user_id": 1,
    "product_id": 5,
    "quantity": 2
  }'
```

**Validações:**
- Verifica se produto existe
- Verifica se produto está ativo
- Verifica estoque disponível
- Cria carrinho se não existir

### 3. Atualizar Quantidade do Item

```bash
curl -X PUT http://localhost:8000/api/v1/cart/items/10 \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "quantity": 5
  }'
```

### 4. Remover Item do Carrinho

```bash
curl -X DELETE http://localhost:8000/api/v1/cart/items/10 \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 5. Limpar Carrinho Completo

```bash
curl -X DELETE http://localhost:8000/api/v1/cart/1/clear \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

---

## Testes de Pedidos

### 1. Listar Pedidos

```bash
curl -X GET "http://localhost:8000/api/v1/orders?page=1&per_page=10" \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 2. Listar Pedidos com Filtros

```bash
curl -X GET "http://localhost:8000/api/v1/orders?user_id=1&status=processing&start_date=2024-01-01&end_date=2024-12-31&page=1" \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 3. Obter Pedido Específico

```bash
curl -X GET http://localhost:8000/api/v1/orders/1 \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### 4. Criar Pedido

```bash
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
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
  }'
```

**Fluxo Assíncrono Disparado:**
1. Validação de estoque
2. Criação do pedido (status: pending)
3. Event `OrderCreated` disparado
4. Listener `ProcessOrderCreated`:
   - Job `ProcessOrder` → Valida e atualiza status para "processing"
   - Job `UpdateStock` → Atualiza estoque de cada item
   - Job `SendOrderConfirmation` → Envia email
5. Se estoque baixo: Event `StockLow` disparado

### 5. Atualizar Pedido

```bash
curl -X PUT http://localhost:8000/api/v1/orders/1 \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "shipping_address": "Rua XYZ, 456, Rio de Janeiro, RJ, 20000-000",
    "notes": "Entregar após às 14h"
  }'
```

### 6. Atualizar Status do Pedido

```bash
curl -X PATCH http://localhost:8000/api/v1/orders/1/status \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "shipped"
  }'
```

**Status Válidos:**
- `pendente` - Pedido criado, aguardando processamento
- `processando` - Pedido em processamento
- `enviado` - Pedido enviado para entrega
- `entregue` - Pedido entregue ao cliente
- `cancelado` - Pedido cancelado

### 7. Deletar Pedido

```bash
curl -X DELETE http://localhost:8000/api/v1/orders/1 \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

---

## Testes de Fluxo Completo

### Cenário 1: Compra Completa (Usuário Autenticado)

#### Passo 1: Login
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password", "device_name": "Test"}'
```

#### Passo 2: Buscar Produtos
```bash
curl -X GET "http://localhost:8000/api/v1/products?search=notebook&category_id=1" \
  -H "Authorization: Bearer {TOKEN}"
```

#### Passo 3: Adicionar ao Carrinho
```bash
curl -X POST http://localhost:8000/api/v1/cart/items \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "product_id": 5, "quantity": 2}'
```

#### Passo 4: Ver Carrinho
```bash
curl -X GET "http://localhost:8000/api/v1/cart?user_id=1" \
  -H "Authorization: Bearer {TOKEN}"
```

#### Passo 5: Criar Pedido
```bash
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [{"product_id": 5, "quantity": 2}],
    "shipping_address": "Rua ABC, 123, SP",
    "billing_address": "Rua ABC, 123, SP"
  }'
```

#### Passo 6: Verificar Pedido
```bash
curl -X GET http://localhost:8000/api/v1/orders/1 \
  -H "Authorization: Bearer {TOKEN}"
```

#### Passo 7: Processar Jobs (Backend)
```bash
docker exec teste_tecnico_app php artisan queue:work --once
```

#### Passo 8: Verificar Status Atualizado
```bash
curl -X GET http://localhost:8000/api/v1/orders/1 \
  -H "Authorization: Bearer {TOKEN}"
```

### Cenário 2: Gestão de Produtos (Admin)

#### Passo 1: Criar Categoria
```bash
curl -X POST http://localhost:8000/api/v1/categories \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{"name": "Eletrônicos", "slug": "eletronicos"}'
```

#### Passo 2: Criar Produto
```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Notebook Dell",
    "slug": "notebook-dell",
    "price": 3499.90,
    "quantity": 50,
    "min_quantity": 10,
    "category_id": 1,
    "active": true
  }'
```

#### Passo 3: Verificar Logs de Evento
```bash
docker exec teste_tecnico_app tail -f storage/logs/laravel.log
```

### Cenário 3: Estoque Baixo

#### Passo 1: Criar Produto com Estoque Baixo
```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Produto Teste",
    "slug": "produto-teste",
    "price": 100.00,
    "quantity": 5,
    "min_quantity": 10,
    "category_id": 1,
    "active": true
  }'
```

#### Passo 2: Criar Pedido que Reduz Estoque
```bash
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [{"product_id": 10, "quantity": 3}],
    "shipping_address": "Rua ABC, 123",
    "billing_address": "Rua ABC, 123"
  }'
```

#### Passo 3: Processar Jobs
```bash
docker exec teste_tecnico_app php artisan queue:work --once
```

#### Passo 4: Verificar Logs de Estoque Baixo
```bash
docker exec teste_tecnico_app tail -f storage/logs/stock.log
```

---

## 🧪 Testes Automatizados

### Executar Testes PHPUnit

```bash
# Todos os testes
docker exec teste_tecnico_app php artisan test

# Testes específicos
docker exec teste_tecnico_app php artisan test --filter=JobsTest
docker exec teste_tecnico_app php artisan test --filter=EventsTest
docker exec teste_tecnico_app php artisan test --filter=OrderFlowTest

# Com coverage
docker exec teste_tecnico_app php artisan test --coverage
```

### Testes Disponíveis

- **JobsTest** (5 testes): ProcessOrder, SendOrderConfirmation, UpdateStock
- **EventsTest** (3 testes): ProductCreated, OrderCreated, StockLow
- **OrderFlowTest** (2 testes): Fluxo completo de pedido
- **ProductTest**: CRUD de produtos
- **CategoryTest**: CRUD de categorias
- **CartTest**: Operações de carrinho
- **OrderTest**: Operações de pedidos

---

## 📊 Monitoramento

### Ver Jobs na Fila

```bash
docker exec teste_tecnico_app php artisan queue:monitor
```

### Ver Logs em Tempo Real

```bash
# Log geral
docker exec teste_tecnico_app tail -f storage/logs/laravel.log

# Log de estoque
docker exec teste_tecnico_app tail -f storage/logs/stock.log
```

### Limpar Fila de Jobs

```bash
docker exec teste_tecnico_app php artisan queue:clear
```

---

## ✅ Checklist de Testes

- [ ] Login e obtenção de token
- [ ] CRUD completo de produtos
- [ ] CRUD completo de categorias
- [ ] Adicionar/remover itens do carrinho
- [ ] Criar pedido com validação de estoque
- [ ] Processar jobs assíncronos
- [ ] Verificar eventos disparados
- [ ] Testar estoque baixo
- [ ] Atualizar status de pedido
- [ ] Filtros e paginação
- [ ] Tratamento de erros (401, 404, 422)

---

**Última Atualização:** 2024-01-15  
**Testes Passando:** 89/89 (282 assertions)
