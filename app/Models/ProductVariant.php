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
        return $this->belongsTo(Supplier::class);
    }

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('stats.product_variant_count'));
        static::deleted(fn() => Cache::forget('stats.product_variant_count'));
    }
}