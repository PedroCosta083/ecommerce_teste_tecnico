# 🛒 E-commerce API - Sistema Completo

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://php.net)
[![Tests](https://img.shields.io/badge/Tests-89%20passing-success.svg)](tests/)
[![Coverage](https://img.shields.io/badge/Coverage-35%25-yellow.svg)](tests/)
[![API](https://img.shields.io/badge/API-RESTful-green.svg)](docs/SWAGGER_DOCUMENTATION.md)
[![Swagger](https://img.shields.io/badge/Swagger-OpenAPI%203.0-success.svg)](http://localhost:8000/api/documentation)

Sistema completo de e-commerce com API RESTful, frontend React/Inertia, jobs assíncronos, eventos, autenticação Sanctum e documentação Swagger profissional.

---

## 🚀 Recursos Principais

### Backend
- ✅ **API RESTful** versionada (v1) com 25+ endpoints
- ✅ **Arquitetura em Camadas** (Services, Repositories, DTOs)
- ✅ **Autenticação Sanctum** com Bearer Token
- ✅ **Jobs Assíncronos** (ProcessOrder, UpdateStock, SendOrderConfirmation)
- ✅ **Eventos e Listeners** (ProductCreated, OrderCreated, StockLow)
- ✅ **Cache Inteligente** com tags e invalidação automática
- ✅ **Swagger/OpenAPI** com documentação interativa completa
- ✅ **89 Testes** (282 assertions) - PHPUnit
- ✅ **Policies e Autorização** com Spatie Permissions
- ✅ **Rate Limiting** (100 req/min API, 5 req/min login)

### Frontend
- ✅ **React + Inertia.js** com TypeScript
- ✅ **Tailwind CSS** para estilização
- ✅ **Páginas Públicas** (Home, Produtos, Detalhes)
- ✅ **Área Autenticada** (Carrinho, Checkout, Pedidos, Perfil)
- ✅ **Dashboard Admin** com CRUD completo

---

## 📋 Índice

- [Instalação](#-instalação)
- [Documentação da API](#-documentação-da-api)
- [Arquitetura](#-arquitetura)
- [Endpoints](#-endpoints-principais)
- [Testes](#-testes)
- [Jobs e Eventos](#-jobs-e-eventos)
- [Documentação Completa](#-documentação-completa)

---

## 🔧 Instalação

### Pré-requisitos
- Docker e Docker Compose
- Git

### Passo a Passo

```bash
# 1. Clonar repositório
git clone <repo-url>
cd teste_tecnico

# 2. Copiar .env
cp .env.example .env

# 3. Subir containers Docker
docker-compose up -d

# 4. Instalar dependências
docker exec teste_tecnico_app composer install
docker exec teste_tecnico_node npm install

# 5. Gerar chave da aplicação
docker exec teste_tecnico_app php artisan key:generate

# 6. Executar migrations e seeders
docker exec teste_tecnico_app php artisan migrate --seed

# 7. Compilar assets
docker exec teste_tecnico_node npm run build

# 8. Processar jobs (em terminal separado)
docker exec teste_tecnico_app php artisan queue:work
```

### Acessar Aplicação

- **Frontend:** http://localhost:8000
- **Swagger UI:** http://localhost:8000/api/documentation
- **API Base:** http://localhost:8000/api/v1

### Credenciais de Teste

```
Admin:
Email: admin@example.com
Password: password

User:
Email: user@example.com
Password: password
```

---

## 📚 Documentação da API

### Swagger UI (Interativo)

Acesse a documentação interativa completa:

**URL:** http://localhost:8000/api/documentation

**Recursos:**
- 25 endpoints documentados
- 11 schemas completos
- Autenticação integrada
- Exemplos de request/response
- Teste direto no navegador

### Guias Completos

- **[Documentação Completa](docs/SWAGGER_DOCUMENTATION.md)** - Visão geral e exemplos
- **[Guia do Swagger](docs/SWAGGER_README.md)** - Como usar o Swagger UI
- **[Guia de Testes](docs/API_TESTING_GUIDE.md)** - Testes com cURL/Postman

---

## 🏗️ Arquitetura

### Camadas

```
┌─────────────────────────────────────┐
│         Controllers (API)           │
├─────────────────────────────────────┤
│      Form Requests (Validação)      │
├─────────────────────────────────────┤
│         Services (Lógica)           │
├─────────────────────────────────────┤
│    Repositories (Persistência)      │
├─────────────────────────────────────┤
│         Models (Eloquent)           │
└─────────────────────────────────────┘
```

### Padrões Implementados

- **Repository Pattern** - Abstração de persistência
- **Service Layer** - Lógica de negócio
- **DTO Pattern** - Transferência de dados
- **Resource Pattern** - Formatação JSON
- **Policy Pattern** - Autorização
- **Observer Pattern** - Eventos e Listeners
- **Queue Pattern** - Jobs assíncronos

### Estrutura de Pastas

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/          # Controllers da API
│   │   └── Schemas/      # Schemas Swagger
│   ├── Requests/         # Form Requests
│   └── Resources/        # API Resources
├── Services/             # Lógica de negócio
├── Repositories/         # Persistência
├── DTOs/                 # Data Transfer Objects
├── Jobs/                 # Jobs assíncronos
├── Events/               # Eventos
├── Listeners/            # Listeners
├── Mail/                 # Mailables
└── Policies/             # Autorização
```

---

## 🔌 Endpoints Principais

### Authentication
```
POST   /api/v1/login          # Login
POST   /api/v1/logout         # Logout
GET    /api/v1/me             # Usuário autenticado
```

### Products
```
GET    /api/v1/products       # Listar (filtros: search, category_id, min_price, max_price)
GET    /api/v1/products/{id}  # Detalhes
POST   /api/v1/products       # Criar
PUT    /api/v1/products/{id}  # Atualizar
DELETE /api/v1/products/{id}  # Remover
```

### Categories
```
GET    /api/v1/categories                # Listar
GET    /api/v1/categories/{id}           # Detalhes
POST   /api/v1/categories                # Criar
PUT    /api/v1/categories/{id}           # Atualizar
DELETE /api/v1/categories/{id}           # Remover
GET    /api/v1/categories/{id}/products  # Produtos da categoria
```

### Cart
```
GET    /api/v1/cart                # Obter carrinho
POST   /api/v1/cart/items         # Adicionar item
PUT    /api/v1/cart/items/{id}    # Atualizar quantidade
DELETE /api/v1/cart/items/{id}    # Remover item
DELETE /api/v1/cart/{id}/clear    # Limpar carrinho
```

### Orders
```
GET    /api/v1/orders              # Listar (filtros: user_id, status, start_date, end_date)
GET    /api/v1/orders/{id}         # Detalhes
POST   /api/v1/orders              # Criar
PUT    /api/v1/orders/{id}         # Atualizar
PATCH  /api/v1/orders/{id}/status  # Atualizar status
DELETE /api/v1/orders/{id}         # Remover
```

**Total:** 25 endpoints documentados

---

## 🧪 Testes

### Executar Testes

```bash
# Todos os testes
docker exec teste_tecnico_app php artisan test

# Testes específicos
docker exec teste_tecnico_app php artisan test --filter=JobsTest
docker exec teste_tecnico_app php artisan test --filter=EventsTest
docker exec teste_tecnico_app php artisan test --filter=ProductTest

# Com coverage
docker exec teste_tecnico_app php artisan test --coverage
```

### Estatísticas

- **89 testes** passando
- **282 assertions**
- **~35% coverage**
- **Tempo:** ~44 segundos

### Testes Implementados

- ✅ ProductApiTest (5 testes)
- ✅ AuthApiTest (4 testes)
- ✅ CartApiTest (4 testes)
- ✅ ProductTest unitário (5 testes)
- ✅ ProductTest feature (7 testes)
- ✅ JobsTest (5 testes)
- ✅ OrderFlowTest (2 testes)
- ✅ EventsTest (3 testes)

---

## ⚙️ Jobs e Eventos

### Jobs Assíncronos

**ProcessOrder**
- Valida estoque disponível
- Atualiza status para "processing"
- Dispara UpdateStock para cada item

**UpdateStock**
- Atualiza quantidade em estoque
- Cria StockMovement (tipo: venda)
- Dispara StockLow se quantity < min_quantity

**SendOrderConfirmation**
- Envia email de confirmação
- Template HTML responsivo
- Registra logs de sucesso/erro

### Eventos e Listeners

**ProductCreated**
- LogProductCreation → Registra log
- SendProductCreatedNotification → Notifica admins

**OrderCreated**
- ProcessOrderCreated → Dispara jobs
- SendOrderCreatedNotification → Registra log

**StockLow**
- NotifyLowStock → Alerta admins
- LogLowStock → Log detalhado

### Processar Jobs

```bash
# Processar jobs continuamente
docker exec teste_tecnico_app php artisan queue:work

# Processar um job
docker exec teste_tecnico_app php artisan queue:work --once

# Monitorar fila
docker exec teste_tecnico_app php artisan queue:monitor
```

---

## 📖 Documentação Completa

### Arquitetura e Padrões
- [Estrutura do Projeto](docs/project-structure.md)
- [Fluxo de Arquitetura](docs/product-flow.md)
- [Versionamento da API](docs/api-versioning.md)

### Recursos Avançados
- [Jobs e Queues](docs/jobs-queues.md)
- [Eventos e Listeners](docs/events-listeners.md)
- [Sistema de Cache](docs/cache-system.md)
- [Query Optimization](docs/query-optimization.md)
- [Query Scopes](docs/query-scopes.md)

### API e Testes
- [Documentação Swagger](docs/SWAGGER_DOCUMENTATION.md)
- [Guia do Swagger UI](docs/SWAGGER_README.md)
- [Guia de Testes](docs/API_TESTING_GUIDE.md)
- [Testes Implementados](docs/tests-summary.md)

### Sistemas Específicos
- [Sistema de Carrinho](docs/cart-system.md)
- [Roles e Permissions](docs/roles-permissions.md)
- [Comandos Docker](docs/docker-commands.md)

### Avaliação
- [Avaliação de Progresso](docs/avaliacao-progresso.md)
- [Implementação Swagger](docs/SWAGGER_IMPLEMENTATION_SUMMARY.md)

---

## 🛠️ Tecnologias

### Backend
- Laravel 10.x
- PHP 8.2
- PostgreSQL 15
- Redis (Cache)
- Laravel Sanctum (Auth)
- Spatie Permissions
- L5-Swagger

### Frontend
- React 18
- Inertia.js
- TypeScript
- Tailwind CSS
- Vite

### DevOps
- Docker & Docker Compose
- Nginx
- Node.js 18

---

## 📊 Status do Projeto

**Conformidade com Desafio:** 97% ✅

| Requisito | Status |
|-----------|--------|
| Arquitetura em Camadas | ✅ 100% |
| Models e Relacionamentos | ✅ 100% |
| Migrations e Seeders | ✅ 100% |
| API RESTful | ✅ 100% |
| Autenticação | ✅ 100% |
| Jobs e Queues | ✅ 100% |
| Eventos e Listeners | ✅ 100% |
| Swagger/OpenAPI | ✅ 100% |
| Cache | ✅ 100% |
| Testes | ⚠️ 35% |
| Frontend | ✅ 85% |

---

## 🤝 Contribuindo

```bash
# 1. Fork o projeto
# 2. Criar branch
git checkout -b feature/nova-funcionalidade

# 3. Commit
git commit -m 'feat: adiciona nova funcionalidade'

# 4. Push
git push origin feature/nova-funcionalidade

# 5. Abrir Pull Request
```

---

## 📝 Licença

Este projeto está sob a licença MIT.

---

## 📞 Suporte

- **Email:** api@ecommerce.com
- **Swagger UI:** http://localhost:8000/api/documentation
- **Documentação:** [docs/](docs/)

---

**Desenvolvido com ❤️ usando Laravel e React**
