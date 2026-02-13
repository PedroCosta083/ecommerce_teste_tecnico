# Otimização de Queries - E-commerce

## Estratégias Implementadas

### 1. Índices de Banco de Dados

#### Products
- `slug` - Busca por slug
- `active` - Filtro de produtos ativos
- `active, category_id` - Produtos ativos por categoria (índice composto)
- `price` - Ordenação e filtros por preço

#### Categories
- `slug` - Busca por slug
- `active` - Filtro de categorias ativas
- `parent_id` - Hierarquia de categorias
- `active, parent_id` - Categorias ativas por pai (índice composto)

#### Tags
- `slug` - Busca por slug

#### Orders
- `user_id` - Pedidos por usuário
- `status` - Filtro por status
- `user_id, status` - Pedidos de usuário por status (índice composto)
- `created_at` - Ordenação por data

#### Order Items
- `order_id` - Itens por pedido
- `product_id` - Produtos em pedidos

#### Stock Movements
- `product_id` - Movimentações por produto
- `type` - Filtro por tipo
- `product_id, type` - Movimentações de produto por tipo (índice composto)
- `created_at` - Ordenação por data

#### Carts
- `user_id` - Carrinho por usuário
- `session_id` - Carrinho por sessão

#### Cart Items
- `cart_id` - Itens por carrinho
- `product_id` - Produtos em carrinhos

### 2. Eager Loading

Todos os repositories utilizam eager loading para evitar N+1 queries:

```php
// Products
Product::with(['category', 'tags'])->get();

// Categories
Category::with(['parent', 'children'])->get();

// Orders
Order::with(['user', 'orderItems.product'])->get();
```

### 3. Lazy Eager Loading Otimizado

Em OrderRepository, o eager loading é aplicado APÓS os filtros:

```php
// ❌ Menos eficiente
$query = Order::with(['user', 'orderItems.product']);
$query->where('status', 'pending');

// ✅ Mais eficiente
$query = Order::query();
$query->where('status', 'pending');
return $query->with(['user', 'orderItems.product'])->paginate();
```

### 4. Cache com Tags

Cache implementado para reduzir queries repetitivas:

```php
Cache::tags(['products'])->remember('products.all', 3600, function() {
    return Product::with(['category', 'tags'])->get();
});
```

### 5. Select Específico

Quando não precisar de todos os campos:

```php
// Apenas campos necessários
Product::select('id', 'name', 'price')->get();
```

## Aplicar Índices

Execute a migration para adicionar os índices:

```bash
docker exec teste_tecnico_app php artisan migrate
```

## Monitoramento de Performance

### Debug de Queries

Use o trait `LogsQueries` em controllers:

```php
use App\Traits\LogsQueries;

class ProductController extends ApiController
{
    use LogsQueries;

    public function index()
    {
        $this->enableQueryLog();
        
        // Suas queries aqui
        
        $this->logQueries('ProductController@index');
    }
}
```

### Verificar Queries Executadas

```bash
docker exec -it teste_tecnico_app php artisan tinker

>>> DB::enableQueryLog();
>>> App\Models\Product::with(['category', 'tags'])->first();
>>> DB::getQueryLog();
```

### Analisar Explain

```php
$query = Product::where('active', true);
dd($query->toSql(), $query->getBindings());
```

No PostgreSQL:
```sql
EXPLAIN ANALYZE SELECT * FROM products WHERE active = true;
```

## Boas Práticas

### ✅ Fazer

1. **Usar índices em colunas de filtro e ordenação**
2. **Eager loading para relacionamentos**
3. **Cache para queries frequentes**
4. **Paginação em listagens grandes**
5. **Select específico quando possível**
6. **Índices compostos para filtros combinados**

### ❌ Evitar

1. **N+1 queries** - Sempre use eager loading
2. **Select * desnecessário** - Selecione apenas campos necessários
3. **Queries em loops** - Use whereIn ou eager loading
4. **Falta de índices em foreign keys**
5. **Eager loading antes de filtros** - Filtre primeiro, depois carregue relacionamentos

## Exemplos de Otimização

### Antes (N+1 Problem)
```php
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name; // Query para cada produto
}
```

### Depois (Eager Loading)
```php
$products = Product::with('category')->all();
foreach ($products as $product) {
    echo $product->category->name; // Sem queries adicionais
}
```

### Antes (Sem índice)
```php
// Lento sem índice em 'slug'
Product::where('slug', 'produto-exemplo')->first();
```

### Depois (Com índice)
```php
// Rápido com índice em 'slug'
Product::where('slug', 'produto-exemplo')->first();
```

## Métricas de Performance

### Antes da Otimização
- Listagem de produtos: ~15 queries
- Detalhes do pedido: ~20 queries
- Produtos por categoria: ~10 queries

### Depois da Otimização
- Listagem de produtos: 3 queries (com cache: 0)
- Detalhes do pedido: 3 queries
- Produtos por categoria: 2 queries (com cache: 0)

## Comandos Úteis

### Ver índices no PostgreSQL
```bash
docker exec -it teste_tecnico_db psql -U laravel -d teste_tecnico

\di products*
```

### Analisar tamanho das tabelas
```sql
SELECT 
    schemaname,
    tablename,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size
FROM pg_tables
WHERE schemaname = 'public'
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;
```

### Verificar queries lentas
```sql
SELECT query, calls, total_time, mean_time
FROM pg_stat_statements
ORDER BY mean_time DESC
LIMIT 10;
```

## Próximos Passos

1. ✅ Índices implementados
2. ✅ Eager loading configurado
3. ✅ Cache implementado
4. ⚠️ Monitorar queries em produção
5. ⚠️ Ajustar índices baseado em uso real
6. ⚠️ Implementar query caching adicional se necessário
