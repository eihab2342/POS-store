<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SupplierPayment extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'purchase_id',
        'amount',
        'date',
        'method',
        'reference',
        'note',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    // العلاقات
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * اسم طريقة الدفع بالعربي
     */
    public function getMethodNameAttribute(): string
    {
        return match ($this->method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'other' => 'أخرى',
            default => 'غير محدد',
        };
    }

    protected static function booted()
    {
        // عند إضافة دفعة جديدة
        static::creating(function (SupplierPayment $payment) {
            // احفظ user_id تلقائي
            if (!$payment->user_id && Auth::check()) {
                $payment->user_id = Auth::id();
            }
        });

        // بعد إضافة الدفعة
        static::created(function (SupplierPayment $payment) {
            $payment->supplier?->recalcCurrentBalance();
            Cache::forget('suppliers');
        });

        // بعد تعديل الدفعة
        static::updated(function (SupplierPayment $payment) {
            $payment->supplier?->recalcCurrentBalance();
            Cache::forget('suppliers');
        });

        // بعد حذف الدفعة
        static::deleted(function (SupplierPayment $payment) {
            $payment->supplier?->recalcCurrentBalance();
            Cache::forget('suppliers');
        });
    }
}