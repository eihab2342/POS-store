<?php

namespace App\Observers;

use App\Models\SupplierPayment;
use Illuminate\Support\Facades\Cache;

class SupplierTransactionObserver
{
    /**
     * بعد حفظ الدفعة
     */
    public function saved(SupplierPayment $payment)
    {
        // امسح كاش المورد
        Cache::forget("supplier.{$payment->supplier_id}");
        Cache::forget('suppliers:all');
    }

    /**
     * بعد حذف الدفعة
     */
    public function deleted(SupplierPayment $payment)
    {
        Cache::forget("supplier.{$payment->supplier_id}");
        Cache::forget('suppliers:all');
    }
}