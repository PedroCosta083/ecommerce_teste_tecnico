# Query Scopes - E-commerce

## Scopes Implementados

### Product Model

#### `scopeActive($query)`
Filtra produtos ativos.

```php
// Uso
Product::active()->get();
Product::active()->where('category_id', 1)->get();
```

#### `scopeInStock($query)`
Filtra produtos com estoque disponível (quantity > 0).

```php
// Uso
Product::inStock()->get();
Product::active()->inStock()->get();
```

#### `scopeLowStock($query)`
Filtra produtos com estoque baixo (quantity <= min_quantity).

```php
// Uso
Product::lowStock()->get();

// Produtos ativos com estoque baixo
Product::active()->lowStock()->get();
```

### Category Model

#### `scopeActive($query)`
Filtra categorias ativas.

```php
// Uso
Category::active()->get();
Category::active()->with('products')->get();
```

#### `scopeRoot($query)`
Filtra categorias raiz (sem parent_id).

```php
// Uso
Category::root()->get();
Category::active()->root()->get();
```

### Order Model

#### `scopeByStatus($query, string $status)`
Filtra pedidos por status específico.

```php
// Uso
Order::byStatus('pending')->get();
Order::byStatus('delivered')->get();
```

#### `scopePending($query)`
Filtra pedidos pendentes.

```php
// Uso
Order::pending()->get();
Order::pending()->recent()->get();
```

#### `scopeByUser($query, int $userId)`
Filtra pedidos de um usuário específico.

```php
// Uso
Order::byUser(1)->get();
Order::byUser($userId)->recent()->get();
```

#### `scopeRecent($query)`
Ordena pedidos por data de criação (mais recentes primeiro).

```php
// Uso
Order::recent()->get();
Order::pending()->recent()->take(10)->get();
```

## Exemplos de Uso Combinado

### Produtos
```php
// Produtos ativos em estoque de uma categoria
Product::active()
    ->inStock()
    ->where('category_id', 1)
    ->get();

// Produtos com estoque baixo que precisam reposição
Product::active()
    ->lowStock()
    ->orderBy('quantity', 'asc')
    ->get();
```

### Categorias
```php
// Categorias raiz ativas com seus produtos
Category::active()
    ->root()
    ->with(['products' => function($query) {
        $query->active()->inStock();
    }])
    ->get();
```

### Pedidos
```php
// Pedidos pendentes de um usuário
Order::byUser($userId)
    ->pending()
    ->recent()
    ->get();

// Últimos 10 pedidos entregues
Order::byStatus('delivered')
    ->recent()
    ->take(10)
    ->get();
```

## Uso em Repositories

Os scopes podem ser usados nos repositories para simplificar queries:

```php
// ProductRepository
public function findActiveInStock(): Collection
{
    return Product::active()->inStock()->get();
}

public function findLowStock(): Collection
{
    return Product::active()->lowStock()->get();
}

// OrderRepository
public function findPendingByUser(int $userId): Collection
{
    return Order::byUser($userId)->pending()->recent()->get();
}
```

## Benefícios

1. **Reutilização**: Queries comuns podem ser reutilizadas em todo o código
2. **Legibilidade**: Código mais limpo e fácil de entender
3. **Manutenção**: Mudanças em queries comuns em um único lugar
4. **Composição**: Scopes podem ser combinados facilmente
5. **Testabilidade**: Mais fácil de testar queries complexas

## Boas Práticas

1. **Nomes descritivos**: Use nomes que descrevam claramente o filtro
2. **Scopes simples**: Cada scope deve fazer uma coisa
3. **Composição**: Combine scopes para queries complexas
4. **Documentação**: Documente o propósito de cada scope
5. **Parâmetros**: Use parâmetros quando necessário (ex: byStatus)
