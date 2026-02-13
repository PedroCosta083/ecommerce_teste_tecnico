# Versionamento da API - v1

## Estrutura de Versionamento

Todas as rotas da API agora utilizam o prefixo `/api/v1/` para versionamento.

## Autenticação Sanctum

### Rate Limiting
A API possui rate limiting configurado:
- **Rotas gerais**: 100 requisições por minuto por IP/usuário
- **Login**: 5 tentativas por minuto por IP

Quando o limite é excedido, a API retorna:
```json
{
  "success": false,
  "message": "Too many requests. Please try again later."
}
```
Status: `429 Too Many Requests`

### Login
```bash
POST /api/v1/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password",
  "device_name": "mobile-app" // opcional
}

Response:
{
  "success": true,
  "data": {
    "user": {...},
    "token": "1|xxxxxxxxxxxxx"
  },
  "message": "Login successful"
}
```

### Logout
```bash
POST /api/v1/logout
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": null,
  "message": "Logout successful"
}
```

### Usuário Autenticado
```bash
GET /api/v1/me
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {...}
}
```

## Endpoints Disponíveis

### Rotas Públicas

#### Produtos
- `GET /api/v1/products` - Listar produtos (com filtros)
- `GET /api/v1/products/{id}` - Detalhes do produto

#### Categorias
- `GET /api/v1/categories` - Listar categorias
- `GET /api/v1/categories/{id}` - Detalhes da categoria

#### Tags
- `GET /api/v1/tags` - Listar tags
- `GET /api/v1/tags/{id}` - Detalhes da tag

#### Carrinho
- `GET /api/v1/cart` - Visualizar carrinho
- `POST /api/v1/cart/items` - Adicionar item ao carrinho
- `PUT /api/v1/cart/items/{cartItem}` - Atualizar item do carrinho
- `DELETE /api/v1/cart/items/{cartItem}` - Remover item do carrinho
- `DELETE /api/v1/cart/{cart}/clear` - Limpar carrinho

### Rotas Protegidas (Requer autenticação Sanctum)

#### Usuário
- `GET /api/v1/user` - Dados do usuário autenticado

#### Produtos (Admin)
- `POST /api/v1/products` - Criar produto
- `PUT /api/v1/products/{id}` - Atualizar produto
- `DELETE /api/v1/products/{id}` - Deletar produto

#### Categorias (Admin)
- `POST /api/v1/categories` - Criar categoria
- `PUT /api/v1/categories/{id}` - Atualizar categoria
- `DELETE /api/v1/categories/{id}` - Deletar categoria
- `GET /api/v1/categories/{category}/products` - Produtos por categoria

#### Tags (Admin)
- `POST /api/v1/tags` - Criar tag
- `PUT /api/v1/tags/{id}` - Atualizar tag
- `DELETE /api/v1/tags/{id}` - Deletar tag

#### Pedidos
- `GET /api/v1/orders` - Listar pedidos
- `GET /api/v1/orders/{id}` - Detalhes do pedido
- `POST /api/v1/orders` - Criar pedido
- `PUT /api/v1/orders/{id}` - Atualizar pedido
- `PUT /api/v1/orders/{id}/status` - Atualizar status do pedido
- `DELETE /api/v1/orders/{id}` - Deletar pedido

#### Movimentações de Estoque
- `GET /api/v1/stock-movements` - Listar movimentações
- `POST /api/v1/stock-movements` - Criar movimentação
- `GET /api/v1/products/{product}/stock-summary` - Resumo de estoque

## Formato de Resposta Padronizado

Todas as respostas seguem o formato:

### Sucesso
```json
{
  "success": true,
  "data": {...},
  "message": "Operation completed successfully"
}
```

### Erro
```json
{
  "success": false,
  "message": "Error message",
  "errors": {...}
}
```

## Autenticação

As rotas protegidas requerem token Sanctum no header:
```
Authorization: Bearer {token}
```

## Próximas Versões

Quando necessário criar uma nova versão da API:
1. Criar novo grupo de rotas com prefixo `/api/v2/`
2. Manter v1 funcionando para compatibilidade
3. Documentar mudanças e deprecações
