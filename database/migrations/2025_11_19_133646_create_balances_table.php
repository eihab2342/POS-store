<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique()->nullable(); // رقم مرجعي
            $table->foreignId('sale_id')->nullable()->constrained()->cascadeOnDelete(); // ربط بالفاتورة
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete(); // العميل
            
            // طرق الدفع
            $table->decimal('cash_amount', 10, 2)->default(0); // كاش
            $table->decimal('wallet_amount', 10, 2)->default(0); // محفظة موبايل
            $table->decimal('instapay_amount', 10, 2)->default(0); // InstaPay
            
            // الإجمالي
            $table->decimal('total_amount', 10, 2); // إجمالي المبلغ
            
            // بيانات إضافية
            $table->string('wallet_phone')->nullable(); // رقم المحفظة
            $table->string('instapay_reference')->nullable(); // رقم معاملة InstaPay
            $table->text('notes')->nullable(); // ملاحظات
            
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->timestamp('payment_date')->useCurrent(); // تاريخ الدفع
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};