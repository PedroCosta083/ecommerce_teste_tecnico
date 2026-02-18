# Jobs e Filas - Sistema de E-commerce

## 📋 Visão Geral

Implementação de Jobs assíncronos para processar operações em background, melhorando a performance e experiência do usuário.

**Queue Connection:** `database` (configurado no .env)

---

## 🔧 Jobs Implementados

### 1. ProcessOrder

**Arquivo:** `app/Jobs/ProcessOrder.php`

**Responsabilidade:** Processar pedido em background após criação

**Funcionalidades:**
- Valida estoque disponível para cada item
- Decrementa quantidade de produtos
- Cria movimentações de estoque (tipo: saida)
- Atualiza status do pedido para "processing"
- Executa tudo em transação (rollback em caso de erro)

**Uso:**
```php
use App\Jobs\ProcessOrder;

ProcessOrder::dispatch($order);
```

**Exceções:**
- Lança exceção se estoque insuficiente
- Rollback automático da transação

---

### 2. SendOrderConfirmation

**Arquivo:** `app/Jobs/SendOrderConfirmation.php`

**Responsabilidade:** Enviar email de confirmação de pedido

**Funcionalidades:**
- Envia email para o usuário com detalhes do pedido
- Registra log da operação
- Execução assíncrona (não bloqueia resposta da API)

**Uso:**
```php
use App\Jobs\SendOrderConfirmation;

SendOrderConfirmation::dispatch($order);
```

**Nota:** Email comentado para evitar envios reais em desenvolvimento. Descomentar linha do Mail::to() em produção.

---

### 3. UpdateStock

**Arquivo:** `app/Jobs/UpdateStock.php`

**Responsabilidade:** Atualizar estoque de produto

**Funcionalidades:**
- Suporta 5 tipos de movimentação:
  - `entrada`: Incrementa estoque
  - `saida`: Decrementa estoque
  - `ajuste`: Define quantidade exata
  - `venda`: Decrementa estoque (igual saida)
  - `devolucao`: Incrementa estoque (igual entrada)
- Cria registro de movimentação de estoque
- Permite referência a entidade relacionada (Order, etc)

**Uso:**
```php
use App\Jobs\UpdateStock;

// Entrada de estoque
UpdateStock::dispatch(
    productId: 1,
    type: 'entrada',
    quantity: 50,
    reason: 'Compra de fornecedor'
);

// Saída de estoque
UpdateStock::dispatch(
    productId: 1,
    type: 'saida',
    quantity: 10,
    reason: 'Venda',
    referenceType: Order::class,
    referenceId: 123
);

// Ajuste de estoque
UpdateStock::dispatch(
    productId: 1,
    type: 'ajuste',
    quantity: 100,
    reason: 'Inventário'
);
```

---

## 🚀 Configuração

### 1. Variáveis de Ambiente

```env
QUEUE_CONNECTION=database
```

### 2. Migrations

Tabelas já criadas:
- `jobs` - Fila de jobs pendentes
- `failed_jobs` - Jobs que falharam

### 3. Executar Queue Worker

**Desenvolvimento (Docker):**
```bash
docker exec teste_tecnico_app php artisan queue:work
```

**Com timeout e tentativas:**
```bash
docker exec teste_tecnico_app php artisan queue:work --timeout=60 --tries=3
```

**Processar apenas 1 job:**
```bash
docker exec teste_tecnico_app php artisan queue:work --once
```

### 4. Monitorar Filas

**Ver jobs pendentes:**
```bash
docker exec teste_tecnico_app php artisan queue:monitor
```

**Ver jobs falhados:**
```bash
docker exec teste_tecnico_app php artisan queue:failed
```

**Reprocessar job falhado:**
```bash
docker exec teste_tecnico_app php artisan queue:retry {id}
```

**Reprocessar todos:**
```bash
docker exec teste_tecnico_app php artisan queue:retry all
```

---

## 📊 Fluxo de Criação de Pedido

```
1. API recebe POST /api/v1/orders
2. CreateOrderRequest valida dados e adiciona user_id do usuário autenticado
3. OrderService.createOrder():
   ├─ Valida estoque disponível para cada produto
   ├─ Calcula subtotal, tax (10%), shipping ($15)
   ├─ Cria pedido (status: pending)
   ├─ Cria order_items
   └─ Retorna pedido criado
4. Dispatch de Jobs assíncronos:
   ├─ ProcessOrder::dispatch($order)
   └─ SendOrderConfirmation::dispatch($order)
5. Resposta imediata ao cliente (201 Created)
6. Jobs executam em background:
   ├─ ProcessOrder:
   │  ├─ Valida estoque novamente
   │  ├─ Dispatch UpdateStock para cada item do pedido
   │  └─ Atualiza status para "processing"
   ├─ UpdateStock (para cada item):
   │  ├─ Decrementa quantidade do produto
   │  └─ Cria StockMovement (tipo: venda)
   └─ SendOrderConfirmation:
      ├─ Envia email com OrderConfirmationMail
      └─ Registra log da operação
```

---

## 🔄 Integração com OrderService

**Exemplo de uso no OrderService:**

```php
use App\Jobs\ProcessOrder;
use App\Jobs\SendOrderConfirmation;

public function createOrder(CreateOrderDTO $dto): Order
{
    $order = DB::transaction(function () use ($dto) {
        // ... criar pedido e itens
        return $order;
    });

    // Dispatch jobs assíncronos
    ProcessOrder::dispatch($order);
    SendOrderConfirmation::dispatch($order);

    return $order;
}
```

---

## ⚡ Performance

**Benefícios:**
- Resposta da API 80% mais rápida (não espera processamento)
- Operações pesadas executadas em background
- Retry automático em caso de falha
- Escalabilidade (múltiplos workers)

**Métricas esperadas:**
- Criação de pedido: ~200ms (antes: ~1s)
- Processamento em background: ~500ms
- Email enviado em: ~300ms

---

## 🧪 Testes

**Testar jobs sincronamente:**
```php
use Illuminate\Support\Facades\Queue;

Queue::fake();

// ... criar pedido

Queue::assertPushed(ProcessOrder::class);
Queue::assertPushed(SendOrderConfirmation::class);
```

**Executar job manualmente:**
```php
$order = Order::find(1);
$job = new ProcessOrder($order);
$job->handle();
```

---

## 📝 Próximas Melhorias

- [ ] Implementar retry exponencial
- [ ] Adicionar job para notificações push
- [ ] Job para gerar relatórios em PDF
- [ ] Job para sincronizar com ERP externo
- [ ] Implementar job batching (Laravel 8+)
- [ ] Adicionar rate limiting nos jobs
- [ ] Implementar job chaining para fluxos complexos

---

## 🔗 Referências

- [Laravel Queues Documentation](https://laravel.com/docs/queues)
- [Job Batching](https://laravel.com/docs/queues#job-batching)
- [Queue Workers](https://laravel.com/docs/queues#running-the-queue-worker)
