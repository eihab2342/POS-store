<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory, Cachable;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'name',
        'barcode',
        'sku',
        'color',
        'size',
        'cost',
        'price',
        'stock_qty',
        'reorder_level'
    ];

    protected $with = ['product'];

    // العلاقات
    public function product() { return $this->belongsTo(Product::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function saleItems() { return $this->hasMany(SaleItem::class, 'variant_id'); }
    public function barcodes() { return $this->hasMany(\App\Models\Barcode::class); }
}