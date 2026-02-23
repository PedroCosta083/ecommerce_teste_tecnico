<?php

namespace App\Services;

use App\Contracts\DashboardRepositoryInterface;

class DashboardService
{
    public function __construct(
        private DashboardRepositoryInterface $repository
    ) {}

    public function getMetrics(): array
    {
        return [
            'overview' => $this->repository->getOverviewMetrics(),
            'sales_by_status' => $this->repository->getSalesByStatus(),
            'top_products' => $this->repository->getTopProducts(5),
            'sales_last_7_days' => $this->repository->getSalesLast7Days(),
            'products_by_category' => $this->repository->getProductsByCategory(10),
        ];
    }
}
