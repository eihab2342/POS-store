<?php

namespace App\Http\Controllers;

use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\Purchase;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    /**
     * عرض صفحة إضافة دفعة
     */
    public function create(Supplier $supplier)
    {
        // جلب الفواتير الغير مسددة أو المسددة جزئياً
        $purchases = $supplier->purchases()
            ->whereRaw('total_cost > (SELECT COALESCE(SUM(amount), 0) FROM supplier_payments WHERE purchase_id = purchases.id)')
            ->get();

        return view('supplier-payments.create', compact('supplier', 'purchases'));
    }

    /**
     * حفظ دفعة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_id' => 'nullable|exists:purchases,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'method' => 'required|in:cash,bank_transfer,check,other',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        // التحقق من أن المبلغ لا يتجاوز المتبقي
        if ($request->purchase_id) {
            $purchase = Purchase::findOrFail($request->purchase_id);
            if ($request->amount > $purchase->remaining_amount) {
                return back()->withErrors(['amount' => 'المبلغ أكبر من المبلغ المتبقي للفاتورة'])
                    ->withInput();
            }
        }

        SupplierPayment::create($validated);

        return redirect()->route('suppliers.show', $validated['supplier_id'])
            ->with('success', 'تم إضافة الدفعة بنجاح');
    }

    /**
     * عرض صفحة تعديل دفعة
     */
    public function edit(SupplierPayment $payment)
    {
        $purchases = $payment->supplier->purchases()->get();
        return view('supplier.payments.edit', compact('payment', 'purchases'));
    }

    /**
     * تحديث دفعة
     */
    public function update(Request $request, SupplierPayment $payment)
    {
        $validated = $request->validate([
            'purchase_id' => 'nullable|exists:purchases,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'method' => 'required|in:cash,bank_transfer,check,other',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $payment->update($validated);

        return redirect()->route('suppliers.show', $payment->supplier_id)
            ->with('success', 'تم تحديث الدفعة بنجاح');
    }

    /**
     * حذف دفعة
     */
    public function destroy(SupplierPayment $payment)
    {
        $supplierId = $payment->supplier_id;
        $payment->delete();

        return redirect()->route('suppliers.show', $supplierId)
            ->with('success', 'تم حذف الدفعة بنجاح');
    }
}