<?php

// app/Models/Barcode.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    protected $fillable = ['product_variant_id', 'code', 'type', 'batch_no'];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}