<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Services\BarcodeService;

class BarcodeController extends Controller
{
    /**
     * معاينة وطباعة الاستيكرات
     */
    public function preview(ProductVariant $variant)
    {
        // توليد/تحديث الباركودات
        $barcodes = BarcodeService::syncToStock($variant);
        dd($barcodes);
        if ($barcodes->isEmpty()) {
            return back()->with('error', 'لا يوجد مخزون لهذا المنتج!');
        }

        return view('barcodes.preview', [
            'variant' => $variant,
            'barcodes' => $barcodes,
        ]);
    }
}