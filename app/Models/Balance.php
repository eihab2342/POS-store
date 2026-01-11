<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'sale_id',
        'customer_id',
        'cash_amount',
        'wallet_amount',
        'instapay_amount',
        'total_amount',
        'wallet_phone',
        'instapay_reference',
        'notes',
        'status',
        'payment_date',
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'wallet_amount' => 'decimal:2',
        'instapay_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // توليد رقم مرجعي تلقائي
        static::creating(function ($balance) {
            if (empty($balance->reference_number)) {
                $balance->reference_number = 'BAL-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }

            // حساب الإجمالي تلقائيا
            $balance->total_amount = $balance->cash_amount + $balance->wallet_amount + $balance->instapay_amount;
        });

        static::updating(function ($balance) {
            // تحديث الإجمالي عند التعديل
            $balance->total_amount = $balance->cash_amount + $balance->wallet_amount + $balance->instapay_amount;
        });
    }

    // العلاقات
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // دوال مساعدة
    public function hasMultiplePaymentMethods(): bool
    {
        $methods = 0;
        if ($this->cash_amount > 0)
            $methods++;
        if ($this->wallet_amount > 0)
            $methods++;
        if ($this->instapay_amount > 0)
            $methods++;

        return $methods > 1;
    }

    public function getPaymentMethodsAttribute(): string
    {
        $methods = [];
        if ($this->cash_amount > 0)
            $methods[] = "كاش: {$this->cash_amount} ج.م";
        if ($this->wallet_amount > 0)
            $methods[] = "محفظة: {$this->wallet_amount} ج.م";
        if ($this->instapay_amount > 0)
            $methods[] = "InstaPay: {$this->instapay_amount} ج.م";

        return implode(' | ', $methods);
    }
}