<?php

namespace App\Http\Controllers;

use App\Models\Purchase;

class PurchasePrintController extends Controller
{
    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.variant.product');
        return view('purchases.receipt', compact('purchase'));
    }
}