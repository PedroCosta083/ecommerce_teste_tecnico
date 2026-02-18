# Validações Customizadas - Sistema E-commerce

## 📋 Visão Geral

Implementação de 3 regras de validação customizadas conforme requisitos do desafio técnico.

---

## 🎯 Validações Implementadas

### 1. HasStock

**Arquivo:** `app/Rules/HasStock.php`

**Propósito:** Validar se um produto tem estoque suficiente para a quantidade solicitada.

**Uso:**
```php
use App\Rules\HasStock;

'product_id' => ['required', 'exists:products,id', new HasStock($quantity)]
```

**Funcionalidades:**
- ✅ Verifica se o produto existe
- ✅ Compara quantidade disponível vs solicitada
- ✅ Mensagem de erro detalhada com quantidades
- ✅ Integrado em AddToCartRequest

**Exemplo de Erro:**
```json
{
    "success": false,
    "message": "Estoque insuficiente. Disponível: 5, solicitado: 10."
}
```

---

### 2. UniqueSlug

**Arquivo:** `app/Rules/UniqueSlug.php`

**Propósito:** Validar se um slug é único na tabela especificada, com suporte a soft deletes e exclusão de ID.

**Uso:**
```php
use App\Rules\UniqueSlug;

// Criar novo registro
'slug' => ['required', 'string', new UniqueSlug('products')]

// Atualizar registro existente (ignora próprio ID)
'slug' => ['required', 'string', new UniqueSlug('products', $productId)]
```

**Funcionalidades:**
- ✅ Valida unicidade em qualquer tabela
- ✅ Ignora ID específico (útil em updates)
- ✅ Considera soft deletes em products
- ✅ Integrado em CreateProductRequest, UpdateProductRequest, CreateCategoryRequest, UpdateCategoryRequest

**Exemplo de Erro:**
```json
{
    "success": false,
    "message": "Este slug já está em uso."
}
```

---

### 3. ValidParentCategory

**Arquivo:** `app/Rules/ValidParentCategory.php`

**Propósito:** Validar se uma categoria pai é válida, prevenindo auto-referência e referências circulares.

**Uso:**
```php
use App\Rules\ValidParentCategory;

// Criar nova categoria
'parent_id' => ['nullable', new ValidParentCategory()]

// Atualizar categoria existente
'parent_id' => ['nullable', new ValidParentCategory($categoryId)]
```

**Funcionalidades:**
- ✅ Verifica se categoria pai existe
- ✅ Previne auto-referência (categoria não pode ser pai de si mesma)
- ✅ Previne referências circulares (A → B → C → A)
- ✅ Permite parent_id null
- ✅ Integrado em CreateCategoryRequest, UpdateCategoryRequest

**Exemplos de Erro:**
```json
// Categoria pai não existe
{
    "success": false,
    "message": "A categoria pai não existe."
}

// Auto-referência
{
    "success": false,
    "message": "Uma categoria não pode ser pai de si mesma."
}

// Referência circular
{
    "success": false,
    "message": "Esta seleção criaria uma referência circular."
}
```

---

## 🧪 Testes

**Arquivo:** `tests/Feature/CustomValidationRulesTest.php`

### Cobertura de Testes (9 testes, 19 assertions)

#### HasStock (2 testes)
- ✅ `has_stock_rule_validates_sufficient_stock` - Rejeita quantidade maior que estoque
- ✅ `has_stock_rule_passes_with_sufficient_stock` - Aceita quantidade dentro do estoque

#### UniqueSlug (3 testes)
- ✅ `unique_slug_rule_validates_duplicate_slug_in_products` - Rejeita slug duplicado
- ✅ `unique_slug_rule_passes_with_unique_slug` - Aceita slug único
- ✅ `unique_slug_rule_ignores_own_id_on_update` - Permite mesmo slug em update

#### ValidParentCategory (4 testes)
- ✅ `valid_parent_category_rule_validates_non_existent_parent` - Rejeita categoria inexistente
- ✅ `valid_parent_category_rule_prevents_self_reference` - Previne auto-referência
- ✅ `valid_parent_category_rule_prevents_circular_reference` - Previne referência circular
- ✅ `valid_parent_category_rule_passes_with_valid_parent` - Aceita categoria pai válida

**Executar testes:**
```bash
docker exec teste_tecnico_app php artisan test --filter=CustomValidationRulesTest
```

---

## 📦 Integração nos Form Requests

### Products
- **CreateProductRequest:** UniqueSlug
- **UpdateProductRequest:** UniqueSlug (com ignore ID)

### Categories
- **CreateCategoryRequest:** UniqueSlug + ValidParentCategory
- **UpdateCategoryRequest:** UniqueSlug + ValidParentCategory (com ignore ID)

### Cart
- **AddToCartRequest:** HasStock

---

## 🎯 Exemplos de Uso na API

### 1. Adicionar ao Carrinho (HasStock)

**Request:**
```bash
curl -X POST http://localhost:8000/api/v1/cart/items \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 100
  }'
```

**Response (Estoque Insuficiente):**
```json
{
    "success": false,
    "message": "The product id field is invalid.",
    "errors": {
        "product_id": [
            "Estoque insuficiente. Disponível: 10, solicitado: 100."
        ]
    }
}
```

### 2. Criar Produto (UniqueSlug)

**Request:**
```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Produto Teste",
    "slug": "produto-existente",
    "price": 100,
    "cost_price": 50,
    "quantity": 10,
    "min_quantity": 2,
    "category_id": 1
  }'
```

**Response (Slug Duplicado):**
```json
{
    "success": false,
    "message": "The slug field is invalid.",
    "errors": {
        "slug": [
            "Este slug já está em uso."
        ]
    }
}
```

### 3. Criar Categoria (ValidParentCategory)

**Request:**
```bash
curl -X POST http://localhost:8000/api/v1/categories \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Subcategoria",
    "slug": "subcategoria",
    "parent_id": 999
  }'
```

**Response (Categoria Pai Inexistente):**
```json
{
    "success": false,
    "message": "The parent id field is invalid.",
    "errors": {
        "parent_id": [
            "A categoria pai não existe."
        ]
    }
}
```

---

## 🔧 Detalhes Técnicos

### HasStock

**Lógica:**
1. Busca produto pelo ID
2. Verifica se produto existe
3. Compara `product->quantity` com `$quantity` solicitada
4. Retorna erro se insuficiente

**Parâmetros:**
- `$quantity` (int): Quantidade solicitada

### UniqueSlug

**Lógica:**
1. Query na tabela especificada
2. Filtra por slug
3. Exclui ID se fornecido (para updates)
4. Considera soft deletes em products
5. Retorna erro se slug já existe

**Parâmetros:**
- `$table` (string): Nome da tabela
- `$ignoreId` (int|null): ID a ignorar (opcional)

### ValidParentCategory

**Lógica:**
1. Permite null (categoria raiz)
2. Verifica se categoria pai existe
3. Previne auto-referência (parent_id == category_id)
4. Percorre hierarquia para detectar ciclos
5. Retorna erro se inválido

**Parâmetros:**
- `$categoryId` (int|null): ID da categoria sendo editada (opcional)

**Algoritmo de Detecção de Ciclos:**
```php
private function wouldCreateCircularReference(int $parentId, int $childId): bool
{
    $current = Category::find($parentId);
    
    while ($current && $current->parent_id) {
        if ($current->parent_id == $childId) {
            return true; // Ciclo detectado
        }
        $current = $current->parent;
    }
    
    return false;
}
```

---

## ✅ Conformidade com Requisitos

| Requisito | Status | Implementação |
|-----------|--------|---------------|
| Validar estoque suficiente | ✅ | HasStock |
| Validar categoria pai existe | ✅ | ValidParentCategory |
| Validar slug único | ✅ | UniqueSlug |
| Testes abrangentes | ✅ | 9 testes, 19 assertions |
| Integração em Form Requests | ✅ | 5 Form Requests |
| Mensagens de erro claras | ✅ | Português, detalhadas |

---

## 📊 Estatísticas

- **3 Rules criadas**
- **5 Form Requests integrados**
- **9 testes implementados**
- **19 assertions**
- **100% de testes passando**
- **Cobertura completa dos cenários**

---

## 🚀 Próximos Passos (Opcional)

1. **Validações Adicionais:**
   - ValidPrice (cost_price < price)
   - ValidQuantity (quantity >= min_quantity)
   - ValidEmail (formato customizado)

2. **Melhorias:**
   - Cache de validações frequentes
   - Mensagens de erro multilíngue
   - Logging de validações falhadas

3. **Testes:**
   - Testes de performance
   - Testes de edge cases
   - Testes de concorrência

---

## 📚 Referências

- [Laravel Validation Rules](https://laravel.com/docs/11.x/validation#custom-validation-rules)
- [Form Request Validation](https://laravel.com/docs/11.x/validation#form-request-validation)
- [Testing Validation](https://laravel.com/docs/11.x/testing#testing-validation)
