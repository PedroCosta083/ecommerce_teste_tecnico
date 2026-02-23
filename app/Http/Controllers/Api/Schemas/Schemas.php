<?php

namespace App\Http\Controllers\Api\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="Modelo de usuário do sistema",
 *     @OA\Property(property="id", type="integer", example=1, description="ID único do usuário"),
 *     @OA\Property(property="name", type="string", example="João Silva", description="Nome completo"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@example.com", description="Email único"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-01-15T10:30:00Z", description="Data de verificação do email"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T08:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T14:20:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Modelo de produto do e-commerce",
 *     required={"id", "name", "slug", "price", "quantity", "category_id"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID único do produto"),
 *     @OA\Property(property="name", type="string", example="Notebook Dell Inspiron 15", description="Nome do produto"),
 *     @OA\Property(property="slug", type="string", example="notebook-dell-inspiron-15", description="Slug único para URL"),
 *     @OA\Property(property="description", type="string", example="Notebook com processador Intel i7, 16GB RAM, SSD 512GB", description="Descrição detalhada"),
 *     @OA\Property(property="price", type="number", format="float", example=3499.90, description="Preço de venda"),
 *     @OA\Property(property="cost_price", type="number", format="float", example=2800.00, description="Preço de custo"),
 *     @OA\Property(property="quantity", type="integer", example=50, description="Quantidade em estoque"),
 *     @OA\Property(property="min_quantity", type="integer", example=10, description="Estoque mínimo (alerta)"),
 *     @OA\Property(property="category_id", type="integer", example=1, description="ID da categoria"),
 *     @OA\Property(property="active", type="boolean", example=true, description="Produto ativo/inativo"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T08:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T14:20:00Z"),
 *     @OA\Property(property="category", ref="#/components/schemas/Category", description="Categoria do produto")
 * )
 * 
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     description="Modelo de categoria com suporte hierárquico",
 *     required={"id", "name", "slug"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID único da categoria"),
 *     @OA\Property(property="name", type="string", example="Eletrônicos", description="Nome da categoria"),
 *     @OA\Property(property="slug", type="string", example="eletronicos", description="Slug único para URL"),
 *     @OA\Property(property="description", type="string", example="Produtos eletrônicos e tecnologia", description="Descrição da categoria"),
 *     @OA\Property(property="parent_id", type="integer", example=null, nullable=true, description="ID da categoria pai (null para raiz)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T08:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T14:20:00Z"),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         description="Subcategorias",
 *         @OA\Items(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Property(property="products_count", type="integer", example=42, description="Quantidade de produtos na categoria")
 * )
 * 
 * @OA\Schema(
 *     schema="Tag",
 *     type="object",
 *     title="Tag",
 *     description="Modelo de tag para classificação de produtos",
 *     required={"id", "name", "slug"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID único da tag"),
 *     @OA\Property(property="name", type="string", example="Promoção", description="Nome da tag"),
 *     @OA\Property(property="slug", type="string", example="promocao", description="Slug único para URL"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T08:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T14:20:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Cart",
 *     type="object",
 *     title="Cart",
 *     description="Modelo de carrinho de compras",
 *     required={"id"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID único do carrinho"),
 *     @OA\Property(property="user_id", type="integer", example=1, nullable=true, description="ID do usuário (null para guest)"),
 *     @OA\Property(property="session_id", type="string", example="abc123xyz", nullable=true, description="ID da sessão (para guests)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T14:30:00Z"),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="Itens do carrinho",
 *         @OA\Items(ref="#/components/schemas/CartItem")
 *     ),
 *     @OA\Property(property="total", type="number", format="float", example=6999.80, description="Total do carrinho"),
 *     @OA\Property(property="items_count", type="integer", example=3, description="Quantidade de itens")
 * )
 * 
 * @OA\Schema(
 *     schema="CartItem",
 *     type="object",
 *     title="CartItem",
 *     description="Item individual do carrinho",
 *     required={"id", "cart_id", "product_id", "quantity"},
 *     @OA\Property(property="id", type="integer", example=10, description="ID único do item"),
 *     @OA\Property(property="cart_id", type="integer", example=1, description="ID do carrinho"),
 *     @OA\Property(property="product_id", type="integer", example=5, description="ID do produto"),
 *     @OA\Property(property="quantity", type="integer", example=2, description="Quantidade"),
 *     @OA\Property(property="price", type="number", format="float", example=3499.90, description="Preço unitário no momento da adição"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:15:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T14:30:00Z"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product", description="Dados do produto"),
 *     @OA\Property(property="subtotal", type="number", format="float", example=6999.80, description="Subtotal (price * quantity)")
 * )
 * 
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     description="Modelo de pedido completo",
 *     required={"id", "user_id", "status", "total_amount"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID único do pedido"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="ID do usuário"),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "processing", "shipped", "delivered", "cancelled"},
 *         example="processing",
 *         description="Status atual do pedido"
 *     ),
 *     @OA\Property(property="total_amount", type="number", format="float", example=7149.80, description="Valor total do pedido"),
 *     @OA\Property(property="shipping_address", type="string", example="Rua ABC, 123, São Paulo, SP, 01234-567", description="Endereço de entrega"),
 *     @OA\Property(property="billing_address", type="string", example="Rua ABC, 123, São Paulo, SP, 01234-567", description="Endereço de cobrança"),
 *     @OA\Property(property="notes", type="string", example="Entregar no período da manhã", nullable=true, description="Observações"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T11:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T15:30:00Z"),
 *     @OA\Property(property="user", ref="#/components/schemas/User", description="Dados do usuário"),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="Itens do pedido",
 *         @OA\Items(ref="#/components/schemas/OrderItem")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="OrderItem",
 *     type="object",
 *     title="OrderItem",
 *     description="Item individual do pedido",
 *     required={"id", "order_id", "product_id", "quantity", "price"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID único do item"),
 *     @OA\Property(property="order_id", type="integer", example=1, description="ID do pedido"),
 *     @OA\Property(property="product_id", type="integer", example=5, description="ID do produto"),
 *     @OA\Property(property="quantity", type="integer", example=2, description="Quantidade comprada"),
 *     @OA\Property(property="price", type="number", format="float", example=3499.90, description="Preço unitário no momento da compra"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T11:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T11:00:00Z"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product", description="Dados do produto"),
 *     @OA\Property(property="subtotal", type="number", format="float", example=6999.80, description="Subtotal (price * quantity)")
 * )
 * 
 * @OA\Schema(
 *     schema="StockMovement",
 *     type="object",
 *     title="StockMovement",
 *     description="Movimentação de estoque",
 *     required={"id", "product_id", "type", "quantity"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID único da movimentação"),
 *     @OA\Property(property="product_id", type="integer", example=5, description="ID do produto"),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"entrada", "saida", "ajuste", "venda", "devolucao"},
 *         example="venda",
 *         description="Tipo de movimentação"
 *     ),
 *     @OA\Property(property="quantity", type="integer", example=2, description="Quantidade movimentada"),
 *     @OA\Property(property="reference_id", type="integer", example=1, nullable=true, description="ID de referência (ex: order_id)"),
 *     @OA\Property(property="notes", type="string", example="Venda do pedido #1", nullable=true, description="Observações"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T11:05:00Z"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product", description="Dados do produto")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     title="ValidationError",
 *     description="Resposta de erro de validação",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         example={"email": {"The email field is required."}, "password": {"The password must be at least 8 characters."}},
 *         description="Erros de validação por campo"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="ErrorResponse",
 *     description="Resposta padrão de erro",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Resource not found"),
 *     @OA\Property(property="error", type="string", example="Not Found", nullable=true)
 * )
 * 
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="SuccessResponse",
 *     description="Resposta padrão de sucesso",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operation completed successfully"),
 *     @OA\Property(property="data", type="object", description="Dados da resposta")
 * )
 */
class Schemas
{
    // Esta classe existe apenas para conter as anotações dos schemas
}
