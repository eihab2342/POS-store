<?php


namespace App\Services;

use App\Models\{Purchase, PurchaseItem, ProductVariant, StockMovement};
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * @param array<int, array{variant_id:int, qty:int, cost:float}> $lines
     */
    public function create(int $supplierId, array $lines): int
    {
        return DB::transaction(function () use ($supplierId, $lines) {
            // 🟢 حساب الإجمالي من البنود
            $total = collect($lines)->sum(fn($l) => $l['qty'] * $l['cost']);

            $purchase = Purchase::create([
                'supplier_id' => $supplierId,
                'date' => now(),
                'total_cost' => $total,
            ]);

            foreach ($lines as $l) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'variant_id' => $l['variant_id'],
                    'qty' => (int) $l['qty'],
                    'cost' => (float) $l['cost'],
                ]);

                ProductVariant::where('id', $l['variant_id'])->increment('stock_qty', $l['qty']);

                StockMovement::create([
                    'variant_id' => $l['variant_id'],
                    'qty' => $l['qty'],
                    'type' => 'in',
                    'reason' => 'purchase',
                    'ref_type' => $purchase->id,
                ]);
            }

            return $purchase->id;
        });
    }
}