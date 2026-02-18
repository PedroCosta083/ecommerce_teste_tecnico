# Swagger/OpenAPI - Documentação da API

## 📋 Visão Geral

Documentação interativa da API usando OpenAPI 3.0 (Swagger) com L5-Swagger.

**URL de Acesso:** `http://localhost:8000/api/documentation`

---

## 🚀 Instalação

### 1. Pacote Instalado

```bash
composer require "darkaonline/l5-swagger"
```

### 2. Publicar Configuração

```bash
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
```

### 3. Gerar Documentação

```bash
php artisan l5-swagger:generate
```

---

## 📚 Endpoints Documentados

### Products
- `GET /api/v1/products` - Listar produtos (com filtros e paginação)
- `GET /api/v1/products/{id}` - Obter produto específico
- `POST /api/v1/products` - Criar produto (admin)
- `PUT /api/v1/products/{id}` - Atualizar produto (admin)
- `DELETE /api/v1/products/{id}` - Deletar produto (admin)

### Categories
- `GET /api/v1/categories` - Listar categorias
- `GET /api/v1/categories/{id}/products` - Produtos da categoria

### Cart
- `GET /api/v1/cart` - Obter carrinho
- `POST /api/v1/cart/items` - Adicionar item
- `PUT /api/v1/cart/items/{id}` - Atualizar item
- `DELETE /api/v1/cart/items/{id}` - Remover item

### Orders
- `GET /api/v1/orders` - Listar pedidos
- `GET /api/v1/orders/{id}` - Obter pedido
- `POST /api/v1/orders` - Criar pedido
- `PUT /api/v1/orders/{id}/status` - Atualizar status (admin)

### Auth
- `POST /api/v1/login` - Login
- `POST /api/v1/logout` - Logout
- `GET /api/v1/me` - Usuário autenticado

---

## 🔐 Autenticação

**Tipo:** Bearer Token (Laravel Sanctum)

**Header:**
```
Authorization: Bearer {token}
```

**Como obter token:**
1. Fazer login via `POST /api/v1/login`
2. Copiar token da resposta
3. Usar no header Authorization

---

## 📖 Estrutura de Resposta

### Sucesso
```json
{
  "success": true,
  "data": {...}
}
```

### Paginação
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

### Erro
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation message"]
  }
}
```

---

## 🎯 Exemplos de Uso

### Criar Produto
```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Playstation 5",
    "slug": "playstation-5",
    "description": "Console de última geração",
    "price": 3550.00,
    "cost_price": 2800.00,
    "quantity": 100,
    "min_quantity": 10,
    "category_id": 1,
    "active": true
  }'
```

### Criar Pedido
```bash
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {"product_id": 1, "quantity": 2}
    ],
    "shipping_address": "123 Main St, City, State 12345",
    "billing_address": "123 Main St, City, State 12345",
    "notes": "Deliver in the morning"
  }'
```

---

## 🔧 Configuração

### Arquivo: `config/l5-swagger.php`

**Principais configurações:**
- `paths.annotations`: Diretórios escaneados para anotações
- `generate_always`: Regenerar em cada request (dev: true, prod: false)
- `routes.api`: Rota da UI do Swagger

### Variáveis de Ambiente

```env
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

---

## 📝 Anotações nos Controllers

### Exemplo: ProductController

```php
/**
 * @OA\Get(
 *     path="/products",
 *     tags={"Products"},
 *     summary="List products",
 *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="Success"),
 *     security={{"sanctum": {}}}
 * )
 */
public function index(Request $request): JsonResponse
{
    // ...
}
```

---

## 🌐 Acessar Documentação

### Interface Swagger UI
```
http://localhost:8000/api/documentation
```

### JSON da API
```
http://localhost:8000/docs/api-docs.json
```

---

## ✅ Status da Implementação

✅ Pacote L5-Swagger instalado  
✅ Configuração publicada  
✅ Documentação OpenAPI 3.0 criada  
✅ Endpoints principais documentados  
✅ Autenticação Sanctum configurada  
✅ Tags organizadas por recurso  
✅ Exemplos de requisições  

---

## 🔗 Referências

- [L5-Swagger Documentation](https://github.com/DarkaOnLine/L5-Swagger)
- [OpenAPI Specification](https://swagger.io/specification/)
- [Swagger UI](https://swagger.io/tools/swagger-ui/)
