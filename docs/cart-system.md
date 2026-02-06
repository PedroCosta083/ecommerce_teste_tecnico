# Sistema de Carrinho - E-commerce

## Estratégia Implementada

### Carrinho Guest (Não Logado)
- **Frontend**: localStorage para persistência local
- **Backend**: session_id para identificação
- **Tabela**: `carts` com `session_id` preenchido e `user_id` null

### Carrinho Autenticado
- **Backend**: Vinculado ao `user_id`
- **Tabela**: `carts` com `user_id` preenchido
- **Persistência**: Banco de dados

### Merge ao Login
Quando usuário faz login:
1. Busca carrinho guest pelo `session_id`
2. Busca/cria carrinho do usuário pelo `user_id`
3. Mescla itens:
   - Se produto já existe: soma quantidades
   - Se produto novo: adiciona ao carrinho
4. Remove carrinho guest

## Rotas Públicas

```
GET  /                      - Vitrine (listagem de produtos)
GET  /produto/{id}          - Detalhes do produto

GET  /cart                  - Visualizar carrinho
POST /cart/items            - Adicionar item
PUT  /cart/items/{id}       - Atualizar quantidade
DELETE /cart/items/{id}     - Remover item
POST /cart/merge            - Mesclar carrinho ao fazer login
```

## Fluxo de Uso

### 1. Usuário Não Logado
```javascript
// Adicionar ao carrinho
POST /cart/items
{
  "product_id": 1,
  "quantity": 2
}
// Backend usa session()->getId() automaticamente
```

### 2. Fazer Login
```javascript
// Após login bem-sucedido
POST /cart/merge
{
  "session_id": "abc123..." // session_id do guest
}
// Backend mescla carrinho guest com carrinho do usuário
```

### 3. Usuário Logado
```javascript
// Adicionar ao carrinho
POST /cart/items
{
  "product_id": 1,
  "quantity": 2
}
// Backend usa auth()->id() automaticamente
```

## Componentes Frontend

### StorefrontController
- `index()`: Lista produtos com filtros
- `show()`: Detalhes do produto

### PublicCartController
- `show()`: Retorna carrinho (guest ou autenticado)
- `addItem()`: Adiciona produto
- `updateItem()`: Atualiza quantidade
- `removeItem()`: Remove item
- `mergeOnLogin()`: Mescla carrinhos

## Vantagens da Abordagem

1. **Experiência Contínua**: Usuário não perde carrinho ao fazer login
2. **Performance**: localStorage para guest reduz chamadas ao servidor
3. **Sincronização**: Carrinho disponível em qualquer dispositivo após login
4. **Segurança**: Session_id gerenciado pelo Laravel
5. **Escalabilidade**: Fácil adicionar features como "salvar para depois"

## Próximos Passos

1. Criar views React para vitrine
2. Implementar componente de carrinho
3. Adicionar checkout
4. Integrar com sistema de pedidos
