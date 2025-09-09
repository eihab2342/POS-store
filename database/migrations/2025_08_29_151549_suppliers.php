<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                  // كود المورد
            $table->string('name');                            // اسم المورد
            $table->string('company_name')->nullable();        // اسم الشركة (اختياري)
            $table->string('email')->nullable()->unique();     // البريد
            $table->string('phone')->nullable();               // الهاتف
            $table->string('tax_number')->nullable()->unique(); // الرقم الضريبي
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);  // رصيد افتتاحي
            $table->decimal('current_balance', 15, 2)->default(0);  // رصيد حالي
            $table->boolean('is_active')->default(true);       // نشط / غير نشط
            $table->text('notes')->nullable();                 // ملاحظات
            $table->softDeletes();                             // Soft delete
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};