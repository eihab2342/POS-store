<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'cashier_id',
        'customer_id',
	    'customer_data',
        'paid',
        'remaining',
        'subtotal',
        'discount',
        'tax',
        'total',
        'payment_method',
        'sale_type',
        'status',
        'notes'
    ];

    protected $casts = [
        'date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
        'sale_type' => 'string',
        'remaining' => 'decimal:2',
    ];

    // تحميل العلاقات تلقائياً
    protected $with = ['items.productVariant', 'customer', 'cashier'];

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
    public function credit()
    {
        return $this->hasOne(Credit::class);
    }

    public function getProfitAttribute()
    {
        $totalProfit = 0;
        foreach ($this->items as $item) {
            $totalProfit += ($item->price - $item->productVariant->cost) * $item->qty;
        }
        // dd($item);
        return $totalProfit;
    }
}