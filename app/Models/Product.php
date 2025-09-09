<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'brand', 'category_id', 'is_active','tax_rate'];
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

}