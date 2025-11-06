<?php

namespace App\Services;

use App\Models\Barcode;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BarcodeService
{
    /**
     * يضمن أن إجمالي باركودات الفاريانت == المخزون (stock)
     * ينشئ الباركدات الناقصة فقط.
     */
    public static function syncToStock(ProductVariant $pv, string $type = 'code128'): Collection
    {
        $stock = (int) ($pv->stock ?? 0);
        if ($stock <= 0) {
            return collect();
        }

        $existing = Barcode::where('product_variant_id', $pv->id)->count();
        $toMake = max(0, $stock - $existing);
        if ($toMake === 0) {
            return collect();
        }

        $batch = 'B' . now()->format('YmdHis');

        $rows = [];
        // نكمل الترقيم بناءً على العدد الحالي
        for ($i = 1; $i <= $toMake; $i++) {
            $serial = str_pad((string) ($existing + $i), 6, '0', STR_PAD_LEFT);
            $code = ($pv->sku ?: ('PV' . $pv->id)) . '-' . $serial; // مثال: SKU-000001

            $rows[] = [
                'product_variant_id' => $pv->id,
                'code' => $code,
                'type' => $type,
                'batch_no' => $batch,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 1000) as $chunk) {
                DB::table('barcodes')->insert($chunk);
            }
        });

        // رجّع آخر ما تم إنشاؤه (اختياري)
        return Barcode::where('product_variant_id', $pv->id)
            ->orderByDesc('id')
            ->limit($toMake)
            ->get()
            ->reverse()
            ->values();
    }
}
