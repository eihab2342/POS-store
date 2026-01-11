<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SaleReturn extends Model
{
    protected $table = 'sale_returns';

    protected $fillable = [
        'sale_id',
        'product_variant_id',
        'returned_qty',
        'reason',
        'user_id',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::saved(function ($saleItem) {
            // مسح الكاش
            Cache::forget("variant_{$saleItem->variant_id}");

            // تحقق إذا كان productVariant موجود قبل الوصول إلى sku
            if ($saleItem->productVariant) {
                Cache::forget("variant_sku_{$saleItem->productVariant->sku}");
            }
        });

        static::deleted(function ($saleItem) {
            // تحقق إذا كان productVariant موجود قبل الوصول إلى sku
            if ($saleItem->productVariant) {
                Cache::forget("variant_sku_{$saleItem->productVariant->sku}");
            }

            // مسح الكاش
            Cache::forget("variant_{$saleItem->variant_id}");
        });
    }
}
