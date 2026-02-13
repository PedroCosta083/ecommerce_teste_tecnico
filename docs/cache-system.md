# Sistema de Cache - E-commerce

## Visão Geral

O sistema implementa cache com tags para otimizar consultas ao banco de dados e melhorar a performance da API.

## Configuração

- **Driver**: Database (configurável via `CACHE_STORE` no `.env`)
- **TTL**: 3600 segundos (1 hora)
- **Tags**: Suporte para invalidação seletiva

## Cache Implementado

### Products
**Tag**: `products`

**Métodos com cache**:
- `findAll()` - Lista todos os produtos
- `findById($id)` - Produto por ID
- `findBySlug($slug)` - Produto por slug
- `findActive()` - Produtos ativos
- `findByCategory($categoryId)` - Produtos por categoria

**Invalidação**: Automática em create, update e delete

### Categories
**Tag**: `categories`

**Métodos com cache**:
- `findAll()` - Lista todas as categorias
- `findById($id)` - Categoria por ID
- `findBySlug($slug)` - Categoria por slug
- `findActive()` - Categorias ativas
- `findRootCategories()` - Categorias raiz

**Invalidação**: Automática em create, update e delete
**Nota**: Ao modificar categorias, o cache de produtos também é limpo

### Tags
**Tag**: `tags`

**Métodos com cache**:
- `findAll()` - Lista todas as tags
- `findById($id)` - Tag por ID
- `findBySlug($slug)` - Tag por slug

**Invalidação**: Automática em create, update e delete

## Comandos Artisan

### Limpar todo o cache da aplicação
```bash
docker exec teste_tecnico_app php artisan cache:clear-app
```

### Limpar cache específico por tag
```bash
# Limpar apenas produtos
docker exec teste_tecnico_app php artisan cache:clear-app --tag=products

# Limpar apenas categorias
docker exec teste_tecnico_app php artisan cache:clear-app --tag=categories

# Limpar múltiplas tags
docker exec teste_tecnico_app php artisan cache:clear-app --tag=products --tag=categories
```

### Limpar cache do Laravel (geral)
```bash
docker exec teste_tecnico_app php artisan cache:clear
```

## Estrutura de Chaves

### Products
- `products.all` - Todos os produtos
- `products.{id}` - Produto específico
- `products.slug.{slug}` - Produto por slug
- `products.active` - Produtos ativos
- `products.category.{categoryId}` - Produtos por categoria

### Categories
- `categories.all` - Todas as categorias
- `categories.{id}` - Categoria específica
- `categories.slug.{slug}` - Categoria por slug
- `categories.active` - Categorias ativas
- `categories.root` - Categorias raiz

### Tags
- `tags.all` - Todas as tags
- `tags.{id}` - Tag específica
- `tags.slug.{slug}` - Tag por slug

## Invalidação Automática

O cache é automaticamente invalidado quando:

1. **Produto criado/atualizado/deletado**
   - Limpa tag `products`

2. **Categoria criada/atualizada/deletada**
   - Limpa tags `categories` e `products`
   - Produtos são limpos porque dependem de categorias

3. **Tag criada/atualizada/deletada**
   - Limpa tag `tags`

## Benefícios

- ✅ Redução de consultas ao banco de dados
- ✅ Melhoria na performance da API
- ✅ Invalidação seletiva por tags
- ✅ TTL configurável
- ✅ Suporte a múltiplos drivers (database, redis, memcached)

## Monitoramento

Para verificar se o cache está funcionando:

```bash
# Ver logs de queries (desabilitar cache temporariamente)
docker exec teste_tecnico_app php artisan tinker
>>> DB::enableQueryLog();
>>> App\Models\Product::all();
>>> DB::getQueryLog();
```

## Alternar Driver de Cache

No arquivo `.env`:

```env
# Database (padrão)
CACHE_STORE=database

# Redis (requer configuração)
CACHE_STORE=redis

# File
CACHE_STORE=file

# Array (apenas para testes)
CACHE_STORE=array
```

## Boas Práticas

1. **Sempre use tags** para facilitar invalidação
2. **TTL apropriado** - 1 hora para dados que mudam pouco
3. **Invalidação automática** - Limpe cache ao modificar dados
4. **Não cachear filtros complexos** - Apenas consultas simples e frequentes
5. **Monitorar tamanho** - Limpar cache periodicamente se necessário
