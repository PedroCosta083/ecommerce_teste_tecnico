<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="E-commerce API - Professional Edition",
 *     version="1.0.0",
 *     description="API RESTful completa para sistema de e-commerce com gestão de produtos, categorias, pedidos, carrinho, estoque e autenticação. Inclui sistema de eventos, jobs assíncronos e notificações.",
 *     termsOfService="https://ecommerce.com/terms",
 *     @OA\Contact(
 *         name="API Support Team",
 *         email="api@ecommerce.com",
 *         url="https://ecommerce.com/support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Development Server"
 * )
 * 
 * @OA\Server(
 *     url="https://api.ecommerce.com/v1",
 *     description="Production Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token",
 *     description="Laravel Sanctum Bearer Token. Obtenha o token através do endpoint /auth/login"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints de autenticação e gerenciamento de sessão"
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="Operações CRUD para produtos com filtros, busca e paginação"
 * )
 * 
 * @OA\Tag(
 *     name="Categories",
 *     description="Gerenciamento de categorias hierárquicas e seus produtos"
 * )
 * 
 * @OA\Tag(
 *     name="Tags",
 *     description="Gerenciamento de tags para classificação de produtos"
 * )
 * 
 * @OA\Tag(
 *     name="Cart",
 *     description="Gerenciamento do carrinho de compras (sessão e usuário autenticado)"
 * )
 * 
 * @OA\Tag(
 *     name="Orders",
 *     description="Gestão completa de pedidos com rastreamento de status e histórico"
 * )
 * 
 * @OA\Tag(
 *     name="Stock",
 *     description="Controle de estoque com movimentações, entradas, saídas e relatórios"
 * )
 * 
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Métricas e estatísticas gerais do sistema"
 * )
 */
class SwaggerController extends Controller
{
    //
}
