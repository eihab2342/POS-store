<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class SaleItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'variant_id',
        'qty',
        'price',
        'discount',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    // تحميل productVariant تلقائياً
    protected $with = ['productVariant'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Accessor لحساب الإجمالي الفرعي
    public function getItemTotalAttribute()
    {
        return number_format($this->qty * $this->price, 2);
    }

    // حساب المجموع الفرعي
    public function getSubtotalAttribute()
    {
        return $this->price * $this->qty;
    }

    // Boot method لمسح الكاش عند التحديث أو الحذف
    protected static function booted()
    {
        static::saved(function ($saleItem) {
            // مسح الكاش للـ ProductVariant المتعلق بهذا SaleItem
            Cache::forget("variant_{$saleItem->variant_id}");

            // مسح الكاش للـ SKU المرتبط بالـ ProductVariant
            Cache::forget("variant_sku_{$saleItem->productVariant->sku}");
        });

        static::deleted(function ($saleItem) {
            // مسح الكاش للـ ProductVariant المتعلق بهذا SaleItem عند الحذف
            Cache::forget("variant_{$saleItem->variant_id}");

            // مسح الكاش للـ SKU المرتبط بالـ ProductVariant
            Cache::forget("variant_sku_{$saleItem->productVariant->sku}");
        });
    }
}
