<?php

namespace App\Repositories;

use App\Models\{Sale, SaleItem, StockMovement};

class SaleRepository
{
    public function create(?int $cashierId, ?int $customerId, float $subtotal, float $discount, float $tax, float $total, string $paymentMethod, array $items): int
    {
        $sale = Sale::create([
            'date'           => now(),
            'cashier_id'     => $cashierId,
            'customer_id'    => $customerId,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'tax'            => $tax,
            'total'          => $total,
            'payment_method' => $paymentMethod,
        ]);

        foreach ($items as $i) {
            SaleItem::create([
                'sale_id'    => $sale->id,
                'variant_id' => $i['variant_id'],
                'qty'        => (int) $i['qty'],
                'price'      => (float) $i['price'],
                'discount'   => (float) ($i['discount'] ?? 0),
            ]);
        }

        return $sale->id;
    }

    public function recordMovement(int $variantId, int $qty, string $type, string $reason, int $refId): void
    {
        StockMovement::create([
            'variant_id' => $variantId,
            'qty'        => $qty,
            'type'       => $type,
            'reason'     => $reason,
            'ref_id'     => $refId,
            'ref_type'   => Sale::class,
        ]);
    }
}