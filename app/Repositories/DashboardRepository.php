<?php

namespace App\Repositories;

use App\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getOverviewMetrics(): array
    {
        return [
            'total_products' => DB::table('products')->whereNull('deleted_at')->count(),
            'active_products' => DB::table('products')->where('active', true)->whereNull('deleted_at')->count(),
            'total_categories' => DB::table('categories')->where('active', true)->count(),
            'total_orders' => DB::table('orders')->count(),
            'total_revenue' => DB::table('orders')->sum('total'),
            'pending_orders' => DB::table('orders')->where('status', 'pending')->count(),
            'total_users' => DB::table('users')->count(),
        ];
    }

    public function getSalesByStatus(): array
    {
        return DB::table('orders')
            ->select('status', DB::raw('count(*) as count'), DB::raw('sum(total) as revenue'))
            ->groupBy('status')
            ->get()
            ->toArray();
    }

    public function getTopProducts(int $limit = 5): array
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('sum(order_items.quantity) as total_sold'), DB::raw('sum(order_items.total_price) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getSalesLast7Days(): array
    {
        return DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as orders'), DB::raw('sum(total) as revenue'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public function getProductsByCategory(int $limit = 10): array
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->whereNull('products.deleted_at')
            ->where('products.active', true)
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
