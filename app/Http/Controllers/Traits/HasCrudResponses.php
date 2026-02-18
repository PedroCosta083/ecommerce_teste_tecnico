<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait HasCrudResponses
{
    protected function showResource($model, string $resourceClass, string $notFoundMessage = 'Resource not found'): JsonResponse
    {
        if (!$model) {
            return $this->error($notFoundMessage, 404);
        }
        return $this->success(new $resourceClass($model));
    }

    protected function storeResource($model, string $resourceClass, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success(new $resourceClass($model), $message, 201);
    }

    protected function updateResource($model, string $resourceClass, string $message = 'Resource updated successfully', string $notFoundMessage = 'Resource not found'): JsonResponse
    {
        if (!$model) {
            return $this->error($notFoundMessage, 404);
        }
        return $this->success(new $resourceClass($model), $message);
    }

    protected function destroyResource(bool $deleted, string $message = 'Resource deleted successfully', string $notFoundMessage = 'Resource not found'): JsonResponse
    {
        if (!$deleted) {
            return $this->error($notFoundMessage, 404);
        }
        return $this->success(null, $message);
    }

    protected function paginatedResponse($paginator, string $resourceClass): JsonResponse
    {
        return $this->success([
            'data' => $resourceClass::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        ]);
    }
}
