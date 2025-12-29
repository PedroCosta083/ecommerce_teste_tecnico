<?php

namespace App\Http\Controllers;

use App\DTOs\StockMovement\CreateStockMovementDTO;
use App\Http\Requests\StockMovement\CreateStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Services\StockMovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function __construct(
        private StockMovementService $stockMovementService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $productId = $request->query('product_id');
        $type = $request->query('type');
        $limit = $request->query('limit', 50);

        if ($productId) {
            $movements = $this->stockMovementService->getMovementsByProduct($productId);
        } elseif ($type) {
            $movements = $this->stockMovementService->getMovementsByType($type);
        } else {
            $movements = $this->stockMovementService->getRecentMovements($limit);
        }

        return response()->json(StockMovementResource::collection($movements));
    }

    public function store(CreateStockMovementRequest $request): JsonResponse
    {
        try {
            $dto = CreateStockMovementDTO::fromRequest($request->validated());
            $movement = $this->stockMovementService->createMovement($dto);

            return response()->json(new StockMovementResource($movement), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function summary(int $productId): JsonResponse
    {
        $summary = $this->stockMovementService->getProductStockSummary($productId);
        return response()->json($summary);
    }
}