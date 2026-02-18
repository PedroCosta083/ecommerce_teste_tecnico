# 🚀 Acesso Rápido - Swagger/OpenAPI

## 📍 URLs de Acesso

### Swagger UI (Interface Interativa)
```
http://localhost:8000/api/documentation
```

### JSON Specification
```
http://localhost:8000/docs/api-docs.json
```

### API Base URL
```
http://localhost:8000/api/v1
```

---

## 🔐 Autenticação Rápida

### 1. Obter Token

**Endpoint:** POST `/api/v1/login`

**cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password",
    "device_name": "Swagger Test"
  }'
```

**Resposta:**
```json
{
  "success": true,
  "data": {
    "token": "1|abcdef123456..."
  }
}
```

### 2. Usar no Swagger UI

1. Acesse http://localhost:8000/api/documentation
2. Clique no botão **"Authorize"** (cadeado verde no topo)
3. Cole: `Bearer {seu_token}`
4. Clique em "Authorize"
5. Pronto! Todos os endpoints estão acessíveis

---

## 🧪 Teste Rápido

### Listar Produtos

```bash
curl -X GET "http://localhost:8000/api/v1/products" \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

### Criar Produto

```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Produto Teste",
    "slug": "produto-teste",
    "price": 99.90,
    "quantity": 100,
    "category_id": 1,
    "active": true
  }'
```

### Criar Pedido

```bash
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {"product_id": 1, "quantity": 2}
    ],
    "shipping_address": "Rua ABC, 123, SP",
    "billing_address": "Rua ABC, 123, SP"
  }'
```

---

## 📚 Documentação

### Guias Principais

| Documento | Descrição | Localização |
|-----------|-----------|-------------|
| **Documentação Completa** | Visão geral e exemplos | `docs/SWAGGER_DOCUMENTATION.md` |
| **Guia do Swagger** | Como usar o Swagger UI | `docs/SWAGGER_README.md` |
| **Guia de Testes** | Testes com cURL/Postman | `docs/API_TESTING_GUIDE.md` |
| **README Principal** | Visão geral do projeto | `README.md` |

### Acesso Rápido

```bash
# Ver documentação completa
cat docs/SWAGGER_DOCUMENTATION.md

# Ver guia do Swagger
cat docs/SWAGGER_README.md

# Ver guia de testes
cat docs/API_TESTING_GUIDE.md
```

---

## 🎯 Endpoints Principais

### Authentication
- POST `/api/v1/login` - Login
- POST `/api/v1/logout` - Logout
- GET `/api/v1/me` - Usuário autenticado

### Products (5 endpoints)
- GET `/api/v1/products` - Listar
- GET `/api/v1/products/{id}` - Detalhes
- POST `/api/v1/products` - Criar
- PUT `/api/v1/products/{id}` - Atualizar
- DELETE `/api/v1/products/{id}` - Remover

### Categories (6 endpoints)
- GET `/api/v1/categories` - Listar
- GET `/api/v1/categories/{id}` - Detalhes
- POST `/api/v1/categories` - Criar
- PUT `/api/v1/categories/{id}` - Atualizar
- DELETE `/api/v1/categories/{id}` - Remover
- GET `/api/v1/categories/{id}/products` - Produtos

### Cart (5 endpoints)
- GET `/api/v1/cart` - Obter carrinho
- POST `/api/v1/cart/items` - Adicionar item
- PUT `/api/v1/cart/items/{id}` - Atualizar
- DELETE `/api/v1/cart/items/{id}` - Remover item
- DELETE `/api/v1/cart/{id}/clear` - Limpar

### Orders (6 endpoints)
- GET `/api/v1/orders` - Listar
- GET `/api/v1/orders/{id}` - Detalhes
- POST `/api/v1/orders` - Criar
- PUT `/api/v1/orders/{id}` - Atualizar
- PATCH `/api/v1/orders/{id}/status` - Status
- DELETE `/api/v1/orders/{id}` - Remover

**Total: 25 endpoints**

---

## 🔧 Comandos Úteis

### Regenerar Documentação

```bash
docker exec teste_tecnico_app php artisan l5-swagger:generate
```

### Limpar Cache

```bash
docker exec teste_tecnico_app php artisan cache:clear
docker exec teste_tecnico_app php artisan config:clear
```

### Processar Jobs

```bash
docker exec teste_tecnico_app php artisan queue:work
```

### Executar Testes

```bash
docker exec teste_tecnico_app php artisan test
```

---

## 📞 Suporte

- **Swagger UI:** http://localhost:8000/api/documentation
- **Documentação:** `docs/SWAGGER_DOCUMENTATION.md`
- **Email:** api@ecommerce.com

---

## ✅ Checklist de Verificação

- [ ] Swagger UI acessível
- [ ] Login funcionando
- [ ] Token obtido
- [ ] Autorização configurada
- [ ] Endpoints testados
- [ ] Documentação lida

---

**Última Atualização:** 2024-01-15  
**Versão:** 1.0.0
