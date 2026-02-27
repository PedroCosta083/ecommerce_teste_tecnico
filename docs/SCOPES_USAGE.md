# Query Scopes - Guia de Uso

## 📋 Visão Geral

Query Scopes são métodos reutilizáveis que encapsulam lógica de consulta comum nos models. Eles tornam o código mais limpo, legível e manutenível.

---

## 🎯 Scopes Disponíveis

### Product Model

#### `active()`
Filtra apenas produtos ativos.

```php
// Uso direto
$products = Product::active()->get();

// Combinado com outras queries
$products = Product::active()->where('price', '>', 100)->get();

// No Service
public function getActiveProducts(): Collection
{
    return Product::active()->get();
}
```

#### `inStock()`
Filtra produtos com quantidade em estoque maior que zero.

```php
// Produtos disponíveis
$available = Product::inStock()->get();

// Produtos ativos E em estoque
$products = Product::active()->inStock()->get();

// Produtos em estoque de uma categoria
$products = Product::inStock()
    ->where('category_id', 1)
    ->orderBy('name')
    ->get();
```

#### `lowStock()`
Filtra produtos com estoque baixo (quantity <= min_quantity).

```php
// Produtos com estoque baixo
$lowStock = Product::lowStock()->get();

// Produtos ativos com estoque baixo
$alerts = Product::active()->lowStock()->get();

// Para dashboard/alertas
$count = Product::lowStock()->count();
```

**Exemplo de uso no DashboardService:**
```php
public function getMetrics()
{
    return [
        'low_stock_products' => Product::lowStock()->count(),
        'low_stock_list' => Product::lowStock()
            ->with('category')
            ->limit(10)
            ->get(),
    ];
}
```

---

### Order Model

#### `byStatus(string $status)`
Filtra pedidos por status específico.

```php
// Pedidos processando
$orders = Order::byStatus('processing')->get();

// Pedidos enviados do usuário
$orders = Order::byStatus('shipped')
    ->where('user_id', $userId)
    ->get();
```

#### `pending()`
Atalho para pedidos pendentes.

```php
// Pedidos pendentes
$pending = Order::pending()->get();

// Pedidos pendentes recentes
$recent = Order::pending()->recent()->get();

// Contagem de pendentes
$count = Order::pending()->count();
```

#### `byUser(int $userId)`
Filtra pedidos de um usuário específico.

```php
// Pedidos do usuário
$orders = Order::byUser($userId)->get();

// Pedidos pendentes do usuário
$orders = Order::byUser($userId)->pending()->get();

// Últimos 5 pedidos do usuário
$orders = Order::byUser($userId)
    ->recent()
    ->limit(5)
    ->get();
```

#### `recent()`
Ordena pedidos por data de criação (mais recentes primeiro).

```php
// Pedidos recentes
$orders = Order::recent()->get();

// 10 pedidos mais recentes
$orders = Order::recent()->limit(10)->get();

// Pedidos recentes de um status
$orders = Order::byStatus('delivered')
    ->recent()
    ->paginate(15);
```

**Exemplo de uso no OrderService:**
```php
public function getUserRecentOrders(int $userId, int $limit = 10)
{
    return Order::byUser($userId)
        ->recent()
        ->limit($limit)
        ->with(['orderItems.product'])
        ->get();
}

public function getPendingOrdersCount(): int
{
    return Order::pending()->count();
}
```

---

### Category Model

#### `active()`
Filtra apenas categorias ativas.

```php
// Categorias ativas
$categories = Category::active()->get();

// Categorias ativas com produtos
$categories = Category::active()
    ->with('products')
    ->get();
```

#### `root()`
Filtra categorias raiz (sem parent_id).

```php
// Categorias principais
$roots = Category::root()->get();

// Categorias principais ativas
$roots = Category::active()->root()->get();

// Para menu de navegação
$menu = Category::active()
    ->root()
    ->with('children')
    ->get();
```

**Exemplo de uso no CategoryService:**
```php
public function getActiveRootCategories(): Collection
{
    return Category::active()->root()->get();
}

public function getNavigationMenu()
{
    return Category::active()
        ->root()
        ->with(['children' => function($query) {
            $query->where('active', true);
        }])
        ->orderBy('name')
        ->get();
}
```

---

## 🔗 Combinando Scopes

Scopes podem ser encadeados para criar queries complexas:

```php
// Produtos ativos, em estoque, de uma categoria
$products = Product::active()
    ->inStock()
    ->where('category_id', 1)
    ->orderBy('name')
    ->get();

// Pedidos pendentes de um usuário, recentes
$orders = Order::byUser($userId)
    ->pending()
    ->recent()
    ->with('orderItems')
    ->get();

// Categorias ativas raiz com produtos ativos
$categories = Category::active()
    ->root()
    ->with(['products' => function($query) {
        $query->active()->inStock();
    }])
    ->get();
```

---

## 📊 Exemplos Práticos

### Dashboard de Alertas
```php
public function getAlerts()
{
    return [
        'low_stock' => Product::lowStock()->count(),
        'pending_orders' => Order::pending()->count(),
        'low_stock_products' => Product::lowStock()
            ->with('category')
            ->select('id', 'name', 'quantity', 'min_quantity', 'category_id')
            ->get(),
    ];
}
```

### Listagem de Produtos para Loja
```php
public function getStoreProducts(int $categoryId = null)
{
    $query = Product::active()->inStock();
    
    if ($categoryId) {
        $query->where('category_id', $categoryId);
    }
    
    return $query->with(['category', 'tags'])
        ->orderBy('name')
        ->paginate(20);
}
```

### Histórico de Pedidos do Usuário
```php
public function getUserOrderHistory(int $userId)
{
    return Order::byUser($userId)
        ->recent()
        ->with(['orderItems.product'])
        ->paginate(10);
}
```

### Menu de Navegação
```php
public function getNavigationMenu()
{
    return Category::active()
        ->root()
        ->with(['children' => function($query) {
            $query->active()->orderBy('name');
        }])
        ->orderBy('name')
        ->get();
}
```

---

## 🎨 Boas Práticas

### ✅ Faça
```php
// Use scopes para lógica reutilizável
$products = Product::active()->inStock()->get();

// Combine scopes para queries complexas
$orders = Order::byUser($userId)->pending()->recent()->get();

// Use em Services para encapsular lógica
public function getAvailableProducts()
{
    return Product::active()->inStock()->get();
}
```

### ❌ Evite
```php
// Não repita lógica de filtro
$products = Product::where('active', true)
    ->where('quantity', '>', 0)
    ->get();

// Use o scope ao invés
$products = Product::active()->inStock()->get();
```

---

## 🚀 Criando Novos Scopes

Para adicionar novos scopes, adicione métodos no model:

```php
// No Product.php
public function scopeFeatured($query)
{
    return $query->where('featured', true);
}

public function scopeByPriceRange($query, $min, $max)
{
    return $query->whereBetween('price', [$min, $max]);
}

// Uso
$products = Product::active()->featured()->get();
$products = Product::byPriceRange(100, 500)->get();
```

---

## 📝 Resumo

| Model | Scope | Descrição |
|-------|-------|-----------|
| **Product** | `active()` | Produtos ativos |
| | `inStock()` | Produtos em estoque (qty > 0) |
| | `lowStock()` | Produtos com estoque baixo |
| **Order** | `byStatus($status)` | Pedidos por status |
| | `pending()` | Pedidos pendentes |
| | `byUser($userId)` | Pedidos de um usuário |
| | `recent()` | Ordenar por mais recentes |
| **Category** | `active()` | Categorias ativas |
| | `root()` | Categorias raiz (sem parent) |

---

**Última Atualização:** 2024-02-27  
**Mantido por:** Development Team
