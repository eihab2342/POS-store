<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;

class AutoBarcodeService
{
    public static function generateAndPrint(ProductVariant $pv): int
    {
        $type = config('printer.barcode_type', 'code128');
        $host = config('printer.host');
        $port = (int) config('printer.port', 9100);

        // 1) ولّد الباركودات الناقصة حتى يصل العدد = stock
        $created = BarcodeService::syncToStock($pv, $type);
        if ($created->isEmpty())
            return 0;

        // 2) ابنِ ZPL وابعثه للطابعة
        $zpl = '';
        foreach ($created as $b) {
            $zpl .= ZplPrinter::buildLabel(
                code: $b->code,
                type: $b->type ?: $type,
                name: $pv->name ?? null,
                price: $pv->price !== null ? number_format((float) $pv->price, 2) : null,
                sku: $pv->sku ?? null,
                encode: 'sku',
                showHri: true
            );
        }

        try {
            (new ZplPrinter)->send($host, $port, $zpl);
        } catch (\Throwable $e) {
            Log::error('Printer error: ' . $e->getMessage(), ['host' => $host, 'port' => $port, 'pv_id' => $pv->id]);
        }

        return $created->count();
    }
}
