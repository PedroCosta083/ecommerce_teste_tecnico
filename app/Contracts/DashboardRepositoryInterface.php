<?php

namespace App\Contracts;

interface DashboardRepositoryInterface
{
    public function getOverviewMetrics(): array;
    public function getSalesByStatus(): array;
    public function getTopProducts(int $limit = 5): array;
    public function getSalesLast7Days(): array;
    public function getProductsByCategory(int $limit = 10): array;
}
