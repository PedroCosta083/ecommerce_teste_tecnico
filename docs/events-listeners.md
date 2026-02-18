# Eventos e Listeners - Sistema de E-commerce

## 📋 Visão Geral

Implementação de Eventos e Listeners para desacoplar lógica de negócio e permitir ações assíncronas baseadas em eventos do sistema.

---

## 🎯 Eventos Implementados

### 1. ProductCreated

**Arquivo:** `app/Events/ProductCreated.php`

**Disparado quando:** Um novo produto é criado

**Dados:** Product model

**Listeners:**
- `LogProductCreation` - Registra log da criação
- `SendProductCreatedNotification` - Notifica admins

**Uso:**
```php
use App\Events\ProductCreated;

ProductCreated::dispatch($product);
```

---

### 2. OrderCreated

**Arquivo:** `app/Events/OrderCreated.php`

**Disparado quando:** Um novo pedido é criado

**Dados:** Order model

**Listeners:**
- `ProcessOrderCreated` - Dispara jobs de processamento (ProcessOrder, SendOrderConfirmation)
- `SendOrderCreatedNotification` - Registra log do pedido

**Uso:**
```php
use App\Events\OrderCreated;

OrderCreated::dispatch($order);
```

---

### 3. StockLow

**Arquivo:** `app/Events/StockLow.php`

**Disparado quando:** Estoque de um produto fica abaixo do mínimo

**Dados:** Product model

**Listeners:**
- `NotifyLowStock` - Notifica admins sobre estoque baixo
- `LogLowStock` - Registra log no canal 'stock'

**Uso:**
```php
use App\Events\StockLow;

if ($product->quantity < $product->min_quantity) {
    StockLow::dispatch($product);
}
```

---

## 👂 Listeners Implementados

### ProductCreated Listeners

#### LogProductCreation
**Arquivo:** `app/Listeners/LogProductCreation.php`

**Ação:** Registra log da criação do produto

**Log:**
```php
Log::info('Product created', [
    'product_id' => $product->id,
    'name' => $product->name,
    'price' => $product->price,
    'quantity' => $product->quantity,
]);
```

#### SendProductCreatedNotification
**Arquivo:** `app/Listeners/SendProductCreatedNotification.php`

**Ação:** Notifica admins sobre novo produto (preparado para envio de email)

---

### OrderCreated Listeners

#### ProcessOrderCreated
**Arquivo:** `app/Listeners/ProcessOrderCreated.php`

**Ação:** Dispara jobs para processar pedido

**Jobs disparados:**
- `ProcessOrder::dispatch($order)`
- `SendOrderConfirmation::dispatch($order)`

#### SendOrderCreatedNotification
**Arquivo:** `app/Listeners/SendOrderCreatedNotification.php`

**Ação:** Registra log da criação do pedido

**Log:**
```php
Log::info('Order created', [
    'order_id' => $order->id,
    'user_id' => $order->user_id,
    'total' => $order->total,
    'status' => $order->status,
]);
```

---

### StockLow Listeners

#### NotifyLowStock
**Arquivo:** `app/Listeners/NotifyLowStock.php`

**Ação:** Notifica admins sobre estoque baixo (preparado para envio de email/SMS)

**Log:**
```php
Log::warning('Low stock alert', [
    'product_id' => $product->id,
    'name' => $product->name,
    'current_quantity' => $product->quantity,
    'min_quantity' => $product->min_quantity,
]);
```

#### LogLowStock
**Arquivo:** `app/Listeners/LogLowStock.php`

**Ação:** Registra log detalhado no canal 'stock'

**Log:**
```php
Log::channel('stock')->warning('Product stock is low', [
    'product_id' => $product->id,
    'name' => $product->name,
    'slug' => $product->slug,
    'quantity' => $product->quantity,
    'min_quantity' => $product->min_quantity,
    'difference' => $product->min_quantity - $product->quantity,
]);
```

---

## 🔧 Registro de Eventos

**Arquivo:** `app/Providers/AppServiceProvider.php`

```php
Event::listen(ProductCreated::class, [
    LogProductCreation::class,
    SendProductCreatedNotification::class,
]);

Event::listen(OrderCreated::class, [
    ProcessOrderCreated::class,
    SendOrderCreatedNotification::class,
]);

Event::listen(StockLow::class, [
    NotifyLowStock::class,
    LogLowStock::class,
]);
```

---

## 🔄 Fluxo de Eventos

### Criação de Produto
```
1. ProductService::createProduct()
2. Product criado no banco
3. ProductCreated::dispatch($product)
4. Listeners executam:
   ├─ LogProductCreation (registra log)
   └─ SendProductCreatedNotification (notifica admins)
```

### Criação de Pedido
```
1. OrderService::createOrder()
2. Order criado no banco
3. OrderCreated::dispatch($order)
4. Listeners executam:
   ├─ ProcessOrderCreated:
   │  ├─ ProcessOrder::dispatch($order)
   │  └─ SendOrderConfirmation::dispatch($order)
   └─ SendOrderCreatedNotification (registra log)
```

### Estoque Baixo
```
1. UpdateStock::handle()
2. Estoque atualizado
3. Verifica: quantity < min_quantity
4. StockLow::dispatch($product)
5. Listeners executam:
   ├─ NotifyLowStock (alerta admins)
   └─ LogLowStock (registra no canal stock)
```

---

## 🧪 Testes

**Arquivo:** `tests/Feature/EventsTest.php`

```php
// Testar se evento é disparado
Event::fake([ProductCreated::class]);

// ... criar produto

Event::assertDispatched(ProductCreated::class);
```

**Testes implementados:**
- `test_product_created_event_is_dispatched`
- `test_order_created_event_is_dispatched`
- `test_stock_low_event_is_dispatched`

**Resultado:** 3 testes passando ✅

---

## 📊 Benefícios

✅ **Desacoplamento**: Lógica de negócio separada de ações secundárias  
✅ **Extensibilidade**: Fácil adicionar novos listeners  
✅ **Manutenibilidade**: Cada listener tem responsabilidade única  
✅ **Testabilidade**: Eventos podem ser facilmente mockados  
✅ **Assíncrono**: Listeners podem ser executados em background (ShouldQueue)  

---

## 🔮 Próximas Melhorias

- [ ] Implementar Mailables para notificações por email
- [ ] Adicionar listeners para envio de SMS
- [ ] Implementar notificações push
- [ ] Criar evento ProductUpdated
- [ ] Criar evento OrderStatusChanged
- [ ] Adicionar listeners para métricas/analytics
- [ ] Implementar event sourcing para auditoria

---

## 🔗 Referências

- [Laravel Events Documentation](https://laravel.com/docs/events)
- [Event Discovery](https://laravel.com/docs/events#event-discovery)
- [Queued Event Listeners](https://laravel.com/docs/events#queued-event-listeners)
