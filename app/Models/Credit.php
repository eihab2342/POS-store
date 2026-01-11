<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Credit extends Model
{
    protected $fillable = [
        'customer_id',
        'sale_id',
        'remaining',
        'description',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime',
        'remaining' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    // In your Credit model
    public static function getCachedCredits()
    {
        return Cache::remember('credits_list', 60, function () {
            return static::with('customer', 'sale')->get();
        });
    }
}
