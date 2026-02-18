<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip for SQLite in testing
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // Skip if indexes already exist (idempotent)
        try {
            Schema::table('products', function (Blueprint $table) {
                if (!$this->indexExists('products', 'products_slug_index')) $table->index('slug');
                if (!$this->indexExists('products', 'products_active_index')) $table->index('active');
                if (!$this->indexExists('products', 'products_active_category_id_index')) $table->index(['active', 'category_id']);
                if (!$this->indexExists('products', 'products_price_index')) $table->index('price');
            });

            Schema::table('categories', function (Blueprint $table) {
                if (!$this->indexExists('categories', 'categories_slug_index')) $table->index('slug');
                if (!$this->indexExists('categories', 'categories_active_index')) $table->index('active');
                if (!$this->indexExists('categories', 'categories_parent_id_index')) $table->index('parent_id');
                if (!$this->indexExists('categories', 'categories_active_parent_id_index')) $table->index(['active', 'parent_id']);
            });

            Schema::table('tags', function (Blueprint $table) {
                if (!$this->indexExists('tags', 'tags_slug_index')) $table->index('slug');
            });

            Schema::table('orders', function (Blueprint $table) {
                if (!$this->indexExists('orders', 'orders_user_id_index')) $table->index('user_id');
                if (!$this->indexExists('orders', 'orders_status_index')) $table->index('status');
                if (!$this->indexExists('orders', 'orders_user_id_status_index')) $table->index(['user_id', 'status']);
                if (!$this->indexExists('orders', 'orders_created_at_index')) $table->index('created_at');
            });

            Schema::table('order_items', function (Blueprint $table) {
                if (!$this->indexExists('order_items', 'order_items_order_id_index')) $table->index('order_id');
                if (!$this->indexExists('order_items', 'order_items_product_id_index')) $table->index('product_id');
            });

            Schema::table('stock_movements', function (Blueprint $table) {
                if (!$this->indexExists('stock_movements', 'stock_movements_product_id_index')) $table->index('product_id');
                if (!$this->indexExists('stock_movements', 'stock_movements_type_index')) $table->index('type');
                if (!$this->indexExists('stock_movements', 'stock_movements_product_id_type_index')) $table->index(['product_id', 'type']);
                if (!$this->indexExists('stock_movements', 'stock_movements_created_at_index')) $table->index('created_at');
            });

            Schema::table('carts', function (Blueprint $table) {
                if (!$this->indexExists('carts', 'carts_user_id_index')) $table->index('user_id');
                if (!$this->indexExists('carts', 'carts_session_id_index')) $table->index('session_id');
            });

            Schema::table('cart_items', function (Blueprint $table) {
                if (!$this->indexExists('cart_items', 'cart_items_cart_id_index')) $table->index('cart_id');
                if (!$this->indexExists('cart_items', 'cart_items_product_id_index')) $table->index('product_id');
            });
        } catch (\Exception $e) {
            // Indexes already exist, skip
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SELECT indexname FROM pg_indexes WHERE tablename = ? AND indexname = ?", [$table, $index]);
        return count($indexes) > 0;
    }

    public function down(): void
    {
        // Skip for SQLite in testing
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['active']);
            $table->dropIndex(['active', 'category_id']);
            $table->dropIndex(['price']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['active']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['active', 'parent_id']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['slug']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['product_id', 'type']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['session_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['cart_id']);
            $table->dropIndex(['product_id']);
        });
    }
};
