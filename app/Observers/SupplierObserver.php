<?php

namespace App\Observers;

use App\Models\Supplier;
use Illuminate\Support\Facades\Cache;

class SupplierObserver
{
    /**
     * بعد حفظ/تعديل المورد
     */
    public function saved(Supplier $supplier)
    {
        // حدّث كاش المورد
        Cache::put("supplier.{$supplier->id}", $supplier, 60 * 24);

        // امسح الكاش العام
        Cache::forget('suppliers:all');
        Cache::forget('suppliers:options');
        Cache::forget('suppliers:with-debt');
    }

    /**
     * بعد حذف المورد
     */
    public function deleted(Supplier $supplier)
    {
        // امسح كاش المورد
        Cache::forget("supplier.{$supplier->id}");

        // امسح الكاش العام
        Cache::forget('suppliers:all');
        Cache::forget('suppliers:options');
        Cache::forget('suppliers:with-debt');
    }
}
