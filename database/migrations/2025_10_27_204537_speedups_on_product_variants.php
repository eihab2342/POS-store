<?php

// database/migrations/2025_10_27_000000_speedups_on_product_variants.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // فهارس أساسية
            
            $table->index('supplier_id');
            $table->index('price');
            $table->index('stock_qty');
            $table->index('reorder_level');

            // عمود مولد للمقارنة (قابل للفهرسة)
            $table->boolean('is_low_stock')->storedAs('stock_qty < reorder_level');
            $table->index('is_low_stock');
        });

        // (اختياري) فهرس Fulltext للبحث السريع في الاسم/الـSKU (MySQL 8+)
        // DB::statement('ALTER TABLE product_variants ADD FULLTEXT ft_name_sku (name, sku)');
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['is_low_stock']);
            $table->dropColumn('is_low_stock');
            $table->dropUnique(['sku']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['price']);
            $table->dropIndex(['stock_qty']);
            $table->dropIndex(['reorder_level']);
            // DB::statement('ALTER TABLE product_variants DROP INDEX ft_name_sku');
        });
    }
};
