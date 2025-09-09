<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Supplier extends Model
{
    use SoftDeletes, HasFactory, Cachable;

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


    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
    protected static function booted()
    {
        static::saved(fn() => Cache::forget('suppliers_options'));
        static::deleted(fn() => Cache::forget('suppliers_options'));
    }

    // App\Models\Supplier.php

    public function purchaseInvoices()
    {
        return $this->hasMany(Purchase::class);
    }

    public function recalcCurrentBalance(): void
    {
        $purchases = $this->purchaseInvoices()->when(
            Schema::hasColumn('purchase_invoices', 'status'),
            fn($q) => $q->where('status', 'posted')
        )->sum('total');

        $payments  = $this->payments()->sum('amount');
        $returns   = method_exists($this, 'purchaseReturns')
            ? $this->purchaseReturns()->sum('total') : 0;

        $this->updateQuietly([
            'current_balance' => ($this->opening_balance + $purchases - $payments - $returns),
        ]);
    }
}