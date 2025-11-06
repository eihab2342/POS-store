<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ProductVariant extends Model
{
    use HasFactory, Cachable;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'name',
        'sku',
        'color',
        'size',
        'cost',
        'price',
        'stock_qty',
        'reorder_level'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function barcodes()
    {
        return $this->hasMany(\App\Models\Barcode::class);
    }

    // app/Models/ProductVariant.php
    protected static function booted()
    {
        static::creating(function ($pv) {
            if (empty($pv->sku)) {
                $pv->sku = \App\Services\SkuService::make($pv);
            }
        });
        static::updating(function ($pv) {
            if ($pv->isDirty('sku')) {
                $pv->sku = $pv->getOriginal('sku'); // امنع تعديل الـSKU بعد الإنشاء
            }
        });
        $flush = fn() => Cache::tags('product_variants')->flush();

        static::created($flush);
        static::saved($flush);
        static::deleted($flush);
        // 
        static::saved(fn() => Cache::forget('stats.product_variant_count'));
        static::deleted(fn() => Cache::forget('stats.product_variant_count'));
    }
}