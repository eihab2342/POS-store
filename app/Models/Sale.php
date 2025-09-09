<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'cashier_id', 'customer_id', 'subtotal', 'discount', 'tax', 'total', 'payment_method'];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}