<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

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
    { return $this->belongsTo(Product::class); }

    public function supplier()
    { return $this->belongsTo(Supplier::class); }
}