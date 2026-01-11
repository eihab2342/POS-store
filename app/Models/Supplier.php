<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

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
        'opening_balance',  // الرصيد الافتتاحي (إجمالي الفواتير)
        'current_balance',  // الرصيد الحالي
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    // العلاقات
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // أضف هذه الدالة في Supplier Model

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    /**
     * إعادة حساب الرصيد الحالي
     */
    public function recalcCurrentBalance()
    {
        // إجمالي المشتريات (المبلغ المستحق)
        $totalPurchases = $this->purchases()->sum('total_cost');

        // إجمالي المدفوعات
        $totalPayments = $this->payments()->sum('amount');

        // الرصيد الحالي = المشتريات - المدفوعات
        $this->update([
            'current_balance' => $totalPurchases - $totalPayments,
        ]);
    }

    /**
     * إجمالي المشتريات
     */
    public function getTotalPurchasesAttribute()
    {
        return $this->purchases()->sum('total_cost');
    }

    /**
     * إجمالي المدفوعات
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * المبلغ المتبقي
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->current_balance;
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('suppliers');
            Cache::forget('suppliers:options');
        });

        static::deleted(function () {
            Cache::forget('suppliers');
            Cache::forget('suppliers:options');
        });
    }
}
