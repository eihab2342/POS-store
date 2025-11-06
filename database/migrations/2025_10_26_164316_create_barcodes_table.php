<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('barcodes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('product_variant_id');
            $table->string('code', 64)->unique();
            $table->string('type', 16)->default('code128');
            $table->string('batch_no', 32)->nullable();
            $table->timestamps();

            $table->index('product_variant_id');
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcodes');
    }
};