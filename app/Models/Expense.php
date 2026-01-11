<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_date',
        'expense_number',
        'description',
        'amount',
        'payment_method',
        'reference_number',
        'expense_type',
        'supplier_id',
        'employee_id',
        'notes',
        'approved_by',
        'status',
        'attachment',
        'created_by'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            if (empty($expense->expense_number)) {
                $year = now()->format('Y');
                $month = now()->format('m');
                $lastExpense = self::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastExpense ?
                    (int) substr($lastExpense->expense_number, -4) + 1 : 1;

                $expense->expense_number = 'EXP-' . $year . $month . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            if (empty($expense->created_by)) {
                $expense->created_by = auth('')->id();
            }
        });
    }

    // العلاقات
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // النطاقات (Scopes)
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('expense_date', $date);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('expense_date', $month)
                    ->whereYear('expense_date', $year);
    }

    // Getter للمصروفات حسب النوع
    public static function getExpenseTypes()
    {
        return [
            'operational' => 'تشغيلية',
            'administrative' => 'إدارية',
            'marketing' => 'تسويقية',
            'maintenance' => 'صيانة',
            'utilities' => 'مرافق',
            'salary' => 'رواتب',
            'purchase' => 'مشتريات',
            'other' => 'أخرى'
        ];
    }

    public function getExpenseTypeArabic()
    {
        return self::getExpenseTypes()[$this->expense_type] ?? $this->expense_type;
    }

    // Getter لطرق الدفع
    public static function getPaymentMethods()
    {
        return [
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'credit_card' => 'بطاقة ائتمان'
        ];
    }

    public function getPaymentMethodArabic()
    {
        return self::getPaymentMethods()[$this->payment_method] ?? $this->payment_method;
    }

    // Getter للحالات
    public static function getStatuses()
    {
        return [
            'pending' => 'قيد الانتظار',
            'approved' => 'معتمدة',
            'rejected' => 'مرفوضة',
            'paid' => 'مدفوعة'
        ];
    }

    public function getStatusArabic()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }
}