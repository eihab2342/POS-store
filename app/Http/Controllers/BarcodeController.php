<?php

// app/Http/Controllers/BarcodeController.php
namespace App\Http\Controllers;
use App\Models\ProductVariant;
use App\Services\BarcodeService;
use PDF;

class BarcodeController extends Controller
{
    public function print(ProductVariant $variant)
    {
        $type = request('type', 'code128');

        // هيكمل لحد المخزون لو فيه نقص ويرجع آخر اللي اتولد
        $barcodes = BarcodeService::syncToStock($variant, $type);

        $pdf = \PDF::loadView('pdf.labels', [
            'pv' => $variant,
            'barcodes' => $barcodes,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('labels-' . ($variant->sku ?? 'variant-' . $variant->id) . '.pdf');
    }
}
