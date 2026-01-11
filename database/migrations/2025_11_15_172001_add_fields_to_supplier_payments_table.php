<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            // إضافة الأعمدة الجديدة إذا لم تكن موجودة
            if (!Schema::hasColumn('supplier_payments', 'purchase_id')) {
                $table->foreignId('purchase_id')->nullable()->after('supplier_id')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('supplier_payments', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('date');
            }

            if (!Schema::hasColumn('supplier_payments', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('method');
            }

            if (!Schema::hasColumn('supplier_payments', 'reference')) {
                $table->string('reference')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('supplier_payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('note')->constrained();
            }
        });
    }

    public function down()
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropColumn(['purchase_id', 'payment_date', 'payment_method', 'reference', 'user_id']);
        });
    }
};