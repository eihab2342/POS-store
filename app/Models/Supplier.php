<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * الأعمدة اللي ينفع نعمل لها mass assignment
     */
    protected $fillable = [
        'code',
        'name',
        'company_name',
        'email',
        'phone',
        'tax_number',
        'country',
        'city',
        'address',
        'opening_balance',
        'current_balance',
        'is_active',
        'notes',
    ];

    /**
     * التحويل التلقائي للأنواع
     */
    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];


    public function products(){
        return $this->hasMany(Product::class);
    }

    public function purchases(){ return $this->hasMany(Purchase::class); }
    public function payments(){ return $this->hasMany(SupplierPayment::class); }
}