<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->constrained('sales')
                ->onDelete('cascade')
                ->index(); // 🔁 Index

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->onDelete('cascade');

            $table->integer('returned_qty');

            $table->text('reason')->nullable();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->index(); // 🔁 Index

            $table->timestamps();

            //  Composite index لو هتبحث بترتيب أو تحليل مشترك
            $table->index(['sale_id', 'product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_returns');
    }
};
