<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('reports/index');
    }

    public function revenue(Request $request): Response
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $status = $request->get('status');

        $query = Order::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->with('user')->orderBy('created_at', 'desc')->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();
        $averageTicket = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $revenueByStatus = $orders->groupBy('status')->map(function($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->sum('total'),
            ];
        });

        $revenueByDay = $orders->groupBy(function($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->sum('total'),
            ];
        })->sortKeys();

        return Inertia::render('reports/revenue', [
            'orders' => $orders,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'average_ticket' => $averageTicket,
            ],
            'revenue_by_status' => $revenueByStatus,
            'revenue_by_day' => $revenueByDay,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $status,
            ],
        ]);
    }

    public function lowStock(): Response
    {
        $products = Product::with('category')
            ->whereColumn('quantity', '<=', 'min_quantity')
            ->orWhere(function($query) {
                $query->whereNull('min_quantity')
                      ->where('quantity', '<=', 10);
            })
            ->orderBy('quantity', 'asc')
            ->get();

        return Inertia::render('reports/low-stock', [
            'products' => $products,
        ]);
    }

    public function stockMovements(Request $request): Response
    {
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = StockMovement::with('product');

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $query->orderBy($sortBy, $sortOrder);

        $movements = $query->paginate(50);
        $products = Product::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('reports/stock-movements', [
            'movements' => [
                'data' => $movements->items(),
                'meta' => [
                    'current_page' => $movements->currentPage(),
                    'last_page' => $movements->lastPage(),
                    'per_page' => $movements->perPage(),
                    'total' => $movements->total(),
                ],
            ],
            'filters' => $request->only(['product_id', 'type', 'date_from', 'date_to', 'sort_by', 'sort_order']),
            'products' => $products,
        ]);
    }
}
