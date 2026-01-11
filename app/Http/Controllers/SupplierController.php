<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * عرض قائمة الموردين
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // بحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $suppliers = $query->latest()->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * عرض صفحة إضافة مورد جديد
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * حفظ مورد جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // توليد كود المورد تلقائياً
        $validated['code'] = 'SUP-' . str_pad(Supplier::count() + 1, 5, '0', STR_PAD_LEFT);
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    /**
     * عرض تفاصيل مورد معين
     */
    public function show(Supplier $supplier)
    {
        $purchases = $supplier->purchases()
            ->with('payments')
            ->latest()
            ->paginate(10);

        $payments = $supplier->payments()
            ->with('purchase', 'user')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_purchases' => $supplier->purchases()->sum('total_cost'),
            'total_paid' => $supplier->payments()->sum('amount'),
            'purchases_count' => $supplier->purchases()->count(),
            'payments_count' => $supplier->payments()->count(),
        ];

        return view('suppliers.show', compact('supplier', 'purchases', 'payments', 'stats'));
    }

    /**
     * عرض صفحة تعديل المورد
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * تحديث بيانات المورد
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'تم تحديث بيانات المورد بنجاح');
    }

    /**
     * حذف المورد
     */
    public function destroy(Supplier $supplier)
    {
        // تحقق من عدم وجود فواتير أو مدفوعات
        if ($supplier->purchases()->count() > 0 || $supplier->payments()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المورد لوجود فواتير أو مدفوعات مرتبطة به');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'تم حذف المورد بنجاح');
    }

    /**
     * إعادة حساب الرصيد الحالي للمورد
     */
    public function recalculateBalance(Supplier $supplier)
    {
        $supplier->recalcCurrentBalance();

        return back()->with('success', 'تم إعادة حساب الرصيد بنجاح');
    }
}