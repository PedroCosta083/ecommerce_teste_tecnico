# Estrutura do Projeto - Organização

## Controllers

### API Controllers (`app/Http/Controllers/Api/`)
- **ApiController**: Base controller com métodos `success()` e `error()` padronizados
- **CartController**: Gerenciamento de carrinho (API)
- **CategoryController**: CRUD de categorias (API)
- **OrderController**: Gerenciamento de pedidos (API)
- **ProductController**: CRUD de produtos (API)
- **StockMovementController**: Movimentações de estoque (API)
- **TagController**: CRUD de tags (API)

### Web Controllers (`app/Http/Controllers/Web/`)
- **CategoryController**: Interface web para categorias
- **OrderController**: Interface web para pedidos
- **ProductController**: Interface web para produtos
- **TagController**: Interface web para tags
- **RoleController**: Gerenciamento de roles
- **PermissionController**: Gerenciamento de permissions
- **UserRoleController**: Atribuição de roles a usuários

### Settings Controllers (`app/Http/Controllers/Settings/`)
- **PasswordController**: Alteração de senha
- **ProfileController**: Perfil do usuário
- **TwoFactorAuthenticationController**: 2FA

## Policies (`app/Policies/`)
- **ProductPolicy**: Autorização para produtos
- **CategoryPolicy**: Autorização para categorias
- **OrderPolicy**: Autorização para pedidos (inclui lógica de owner)
- **TagPolicy**: Autorização para tags

## Services (`app/Services/`)
Camada de lógica de negócio:
- CartService
- CategoryService
- OrderService
- ProductService
- StockMovementService
- TagService

## Repositories (`app/Repositories/`)
Camada de acesso a dados com interfaces e implementações Eloquent

## DTOs (`app/DTOs/`)
Data Transfer Objects organizados por domínio

## Permissions
Formato: `{recurso}.{ação}`
- products.view, products.create, products.update, products.delete
- categories.view, categories.create, categories.update, categories.delete
- orders.view, orders.create, orders.update, orders.delete
- tags.view, tags.create, tags.update, tags.delete
- users.view, users.create, users.update, users.delete
- roles.view, roles.create, roles.update, roles.delete

## Roles Padrão
- **admin**: Todas as permissões
- **manager**: Gerenciar produtos, categorias, tags e visualizar pedidos
- **user**: Visualizar produtos/categorias e gerenciar próprios pedidos

## Rotas
- `/api/*`: Rotas API (JSON responses)
- `/`: Rotas Web (Inertia/React)
- `/settings/*`: Configurações do usuário

## Autorização
- Web controllers usam `authorizeResource()` para autorização automática
- Policies verificam permissions do Spatie
- API controllers herdam de ApiController para responses padronizadas
