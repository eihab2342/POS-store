<?php

namespace App\Observers;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;
use App\Services\SkuService;

class ProductVariantObserver
{
    private $cacheDuration = 1440; // 24 ساعة بالدقائق

    public function creating(ProductVariant $variant)
    {
        // توليد SKU تلقائي لو مش موجود
        if (empty($variant->sku)) {
            $variant->sku = SkuService::make();
        }
        
        // ربط الباركود بالـ SKU تلقائياً
        if (empty($variant->barcode)) {
            $variant->barcode = $variant->sku;
        }
    }

    public function updating(ProductVariant $variant)
    {
        // منع تعديل الـ SKU نهائياً: لو اتغير، رجع القيمة الأصلية
        if ($variant->isDirty('sku')) {
            $variant->sku = $variant->getOriginal('sku');
        }
    }

    public function saved(ProductVariant $variant)
    {
        // تحديث كاش المنتج الفردي
        Cache::put("variant.{$variant->id}", $variant, $this->cacheDuration);
        
        // مسح كاش القوائم (Pagination)
        $this->clearPaginationCache();
        
        Cache::forget('suppliers:options');
        Cache::forget('variants.latest.50');
    }

    public function deleted(ProductVariant $variant)
    {
        Cache::forget("variant.{$variant->id}");
        $this->clearPaginationCache();
    }

    private function clearPaginationCache()
    {
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("variants.index.page.{$i}");
        }
    }
}