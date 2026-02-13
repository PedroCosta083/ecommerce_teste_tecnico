# Guia de Comandos Docker - Teste Técnico

## Containers do Projeto

- **app**: `teste_tecnico_app` - PHP/Laravel
- **nginx**: `teste_tecnico_nginx` - Servidor Web
- **db**: `teste_tecnico_db` - PostgreSQL
- **node**: `teste_tecnico_node` - Node.js/Vite

## Comandos Básicos

### Gerenciar Containers
```bash
# Iniciar todos os containers
docker compose up -d

# Parar todos os containers
docker compose down

# Ver status dos containers
docker ps

# Ver logs
docker compose logs -f app
docker compose logs -f node
```

## Comandos PHP/Laravel

### Artisan
```bash
# Executar comandos artisan
docker exec teste_tecnico_app php artisan [comando]

# Exemplos:
docker exec teste_tecnico_app php artisan route:list
docker exec teste_tecnico_app php artisan migrate
docker exec teste_tecnico_app php artisan db:seed
docker exec teste_tecnico_app php artisan make:controller NomeController
docker exec teste_tecnico_app php artisan make:model Nome
docker exec teste_tecnico_app php artisan cache:clear
docker exec teste_tecnico_app php artisan config:clear
```

### Composer
```bash
# Instalar dependências
docker exec teste_tecnico_app composer install

# Atualizar dependências
docker exec teste_tecnico_app composer update

# Adicionar pacote
docker exec teste_tecnico_app composer require nome/pacote
```

### Testes
```bash
# Executar testes
docker exec teste_tecnico_app php artisan test

# Executar teste específico
docker exec teste_tecnico_app php artisan test --filter NomeDoTeste
```

## Comandos Node/NPM

### NPM
```bash
# Instalar dependências
docker exec teste_tecnico_node npm install

# Compilar assets (desenvolvimento)
docker exec teste_tecnico_node npm run dev

# Compilar assets (produção)
docker exec teste_tecnico_node npm run build

# Executar linter
docker exec teste_tecnico_node npm run lint
```

## Comandos Database

### PostgreSQL
```bash
# Acessar console do PostgreSQL
docker exec -it teste_tecnico_db psql -U laravel -d teste_tecnico

# Backup do banco
docker exec teste_tecnico_db pg_dump -U laravel teste_tecnico > backup.sql

# Restaurar backup
docker exec -i teste_tecnico_db psql -U laravel teste_tecnico < backup.sql
```

## Acesso aos Containers

### Shell Interativo
```bash
# Acessar container PHP
docker exec -it teste_tecnico_app bash

# Acessar container Node
docker exec -it teste_tecnico_node sh

# Acessar container Nginx
docker exec -it teste_tecnico_nginx sh

# Acessar container DB
docker exec -it teste_tecnico_db bash
```

## URLs de Acesso

- **Aplicação**: http://localhost:8000
- **Vite Dev Server**: http://localhost:5173
- **PostgreSQL**: localhost:5432

## Troubleshooting

### Recriar containers
```bash
docker compose down
docker compose up -d --build
```

### Limpar cache do Laravel
```bash
docker exec teste_tecnico_app php artisan cache:clear
docker exec teste_tecnico_app php artisan config:clear
docker exec teste_tecnico_app php artisan route:clear
docker exec teste_tecnico_app php artisan view:clear
```

### Permissões
```bash
docker exec teste_tecnico_app chmod -R 775 storage bootstrap/cache
docker exec teste_tecnico_app chown -R www-data:www-data storage bootstrap/cache
```

## Comandos Úteis para Desenvolvimento

### Criar Token Sanctum (via tinker)
```bash
docker exec -it teste_tecnico_app php artisan tinker
# Dentro do tinker:
$user = User::find(1);
$token = $user->createToken('test-token')->plainTextToken;
echo $token;
```

### Verificar rotas da API
```bash
docker exec teste_tecnico_app php artisan route:list --path=api
```

### Executar seeders
```bash
docker exec teste_tecnico_app php artisan db:seed
docker exec teste_tecnico_app php artisan db:seed --class=ProductSeeder
```

### Migrations
```bash
# Executar migrations
docker exec teste_tecnico_app php artisan migrate

# Rollback
docker exec teste_tecnico_app php artisan migrate:rollback

# Fresh (drop all + migrate)
docker exec teste_tecnico_app php artisan migrate:fresh

# Fresh com seed
docker exec teste_tecnico_app php artisan migrate:fresh --seed
```
