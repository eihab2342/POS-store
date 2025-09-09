<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;
    protected $fillable = ['sale_id', 'variant_id', 'qty', 'price', 'discount'];


    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}