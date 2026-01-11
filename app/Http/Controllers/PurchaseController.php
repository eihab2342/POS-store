<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController
{
    public function create(Supplier $supplier)
    {
        return view('purchases.create', compact('supplier'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'invoice_no'  => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'total_cost' => ['required', 'numeric'],
        ]);

        $purchase = Purchase::create($data);

        return redirect()
            ->route('purchases.edit', $purchase)
            ->with('success', 'Purchase created successfully');
    }

    public function show(Purchase $purchase)
    {
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        return view('purchases.edit', compact('purchase'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            // باقي الحقول
        ]);

        $purchase->update($data);

        return redirect()
            ->route('purchases.edit', $purchase)
            ->with('success', 'Purchase updated successfully');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()
            ->back()
            ->with('success', 'Purchase deleted successfully');
    }
}
