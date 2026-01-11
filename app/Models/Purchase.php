<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'invoice_no',
        'date',
        'total_cost', // المبلغ الكلي
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
        'date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    // كام اتدفع من الفاتورة دي؟
    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }

    // كام باقي من الفاتورة دي؟
    public function getRemainingAmountAttribute()
    {
        return $this->total_cost - $this->paid_amount;
    }

    // حالة الدفع
    public function getPaymentStatusAttribute()
    {
        if ($this->remaining_amount <= 0) {
            return 'paid'; // اتدفعت
        } elseif ($this->paid_amount > 0) {
            return 'partial'; // اتدفع منها جزء
        } else {
            return 'unpaid'; // لسه مادفعتش
        }
    }


    protected static function booted()
    {
        // لما تضيف فاتورة → زود المبلغ اللي على المورد
        static::created(function ($purchase) {
            $purchase->supplier->increment('current_balance', $purchase->total_cost);
        });

        // لما تحذف فاتورة → انقص المبلغ
        static::deleted(function ($purchase) {
            $purchase->supplier->decrement('current_balance', $purchase->total_cost);
        });

        // لما تعدل الفاتورة → اعد الحساب
        static::updated(function ($purchase) {
            if ($purchase->isDirty('total_cost')) {
                $old = $purchase->getOriginal('total_cost');
                $new = $purchase->total_cost;
                $diff = $new - $old;
                $purchase->supplier->increment('current_balance', $diff);
            }
        });
    }
}
