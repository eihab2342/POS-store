<?php

namespace App\Services;

use App\Models\Barcode;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class BarcodeService
{
    /**
     * يضمن أن إجمالي باركودات الفاريانت == المخزون (stock)
     * وينشئ صورة base64 لكل باركود
     */
    public static function syncToStock(ProductVariant $pv, string $type = 'C128'): Collection
    {
        $stock = (int) ($pv->stock_qty ?? 0);
        if ($stock <= 0) {
            return collect();
        }

        $existing = Barcode::where('product_variant_id', $pv->id)->count();
        $toMake = max(0, $stock - $existing);

        // لو الباركودات موجودة بالفعل، نرجعها مع الصور
        if ($toMake === 0) {
            return self::getBarcodes($pv, $type);
        }

        $batch = 'B' . now()->format('YmdHis');

        $rows = [];
        for ($i = 1; $i <= $toMake; $i++) {
            $serial = str_pad((string) ($existing + $i), 6, '0', STR_PAD_LEFT);
            $code = ($pv->sku ?: ('PV' . $pv->id)) . '-' . $serial;

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

        return self::getBarcodes($pv, $type);
    }

    /**
     * يجيب كل الباركودات مع توليد الصور
     */
    private static function getBarcodes(ProductVariant $pv, string $type = 'C128'): Collection
    {
        $barcodes = Barcode::where('product_variant_id', $pv->id)
            ->orderBy('id')
            ->get();

        // نضيف صورة base64 لكل باركود
        $barcodes->transform(function ($barcode) use ($type) {
            try {
                // استخدام الـ Facade بطريقة صحيحة
                $barcode->image_base64 = base64_encode(
                    DNS1D::getBarcodePNG(
                        $barcode->code,
                        $type,
                        3,      // العرض (width multiplier)
                        50,     // الارتفاع بالبكسل
                        [0, 0, 0], // اللون RGB (أسود)
                        true    // Show code under barcode
                    )
                );
            } catch (\Exception $e) {
                \Log::error('Barcode generation failed: ' . $e->getMessage());
                $barcode->image_base64 = self::createPlaceholder($barcode->code);
            }
            return $barcode;
        });

        return $barcodes;
    }

    /**
     * ينشئ صورة placeholder لو فشل توليد الباركود
     */
    private static function createPlaceholder(string $code): string
    {
        // Simple SVG placeholder
        $svg = '<svg width="300" height="80" xmlns="http://www.w3.org/2000/svg">
            <rect width="300" height="80" fill="#f0f0f0"/>
            <text x="50%" y="50%" text-anchor="middle" font-size="15" fill="#333">' .
            htmlspecialchars($code) .
            '</text>
        </svg>';

        return base64_encode($svg);
    }
}