<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/dashboard/metrics",
     *     tags={"Dashboard"},
     *     summary="Obtém métricas do dashboard",
     *     description="Retorna estatísticas gerais do sistema incluindo totais de produtos, pedidos, vendas e estoque baixo",
     *     operationId="getDashboardMetrics",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Métricas do dashboard",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_products", type="integer", example=150, description="Total de produtos cadastrados"),
     *                 @OA\Property(property="active_products", type="integer", example=142, description="Produtos ativos"),
     *                 @OA\Property(property="low_stock_products", type="integer", example=8, description="Produtos com estoque baixo"),
     *                 @OA\Property(property="total_orders", type="integer", example=523, description="Total de pedidos"),
     *                 @OA\Property(property="pending_orders", type="integer", example=12, description="Pedidos pendentes"),
     *                 @OA\Property(property="processing_orders", type="integer", example=8, description="Pedidos em processamento"),
     *                 @OA\Property(property="total_revenue", type="number", format="float", example=125430.50, description="Receita total"),
     *                 @OA\Property(property="monthly_revenue", type="number", format="float", example=18750.00, description="Receita do mês atual"),
     *                 @OA\Property(property="total_customers", type="integer", example=342, description="Total de clientes")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Não autorizado")
     * )
     */
    public function metrics(): JsonResponse
    {
        $this->authorize('viewMetrics', \App\Models\User::class);
        
        $metrics = $this->dashboardService->getMetrics();
        return response()->json(['success' => true, 'data' => $metrics]);
    }
}
