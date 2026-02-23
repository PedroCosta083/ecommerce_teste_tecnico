<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
        $this->middleware('can:products.view');
    }

    public function metrics(): JsonResponse
    {
        $metrics = $this->dashboardService->getMetrics();
        return response()->json(['success' => true, 'data' => $metrics]);
    }
}
