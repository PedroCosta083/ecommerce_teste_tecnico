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

        Schema::table('products', function (Blueprint $table) {
            $table->index('slug');
            $table->index('active');
            $table->index(['active', 'category_id']);
            $table->index('price');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('slug');
            $table->index('active');
            $table->index('parent_id');
            $table->index(['active', 'parent_id']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->index('slug');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('type');
            $table->index(['product_id', 'type']);
            $table->index('created_at');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('session_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index('cart_id');
            $table->index('product_id');
        });
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
