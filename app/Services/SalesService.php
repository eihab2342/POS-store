<?php

namespace App\Services;

use App\Repositories\ProductVariantRepository;
use App\Repositories\SaleRepository;
use App\DTOs\CartItem;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function __construct(
        protected SaleRepository $sales,
        protected ProductVariantRepository $variants,
    ) {}

    /** @param CartItem[] $items */
    public function checkout(array $items, ?int $cashierId = null, ?int $customerId = null, string $paymentMethod = 'cash'): int
    {
        if (empty($items)) {
            throw new \RuntimeException('السلة فارغة');
        }

        $subtotal = array_reduce($items, fn($s, CartItem $i) => $s + $i->qty * $i->price, 0.0);
        $discount = 0.0;
        $tax = 0.0;
        $total = $subtotal;

        return DB::transaction(function () use ($items, $cashierId, $customerId, $paymentMethod, $subtotal, $discount, $tax, $total) {
            foreach ($items as $i) {
                $this->variants->ensureStockAvailable($i->variant_id, $i->qty);
            }

            $saleId = $this->sales->create(
                cashierId: $cashierId,
                customerId: $customerId,
                subtotal: $subtotal,
                discount: $discount,
                tax: $tax,
                total: $total,
                paymentMethod: $paymentMethod,
                items: array_map(fn(CartItem $i) => [
                    'variant_id' => $i->variant_id,
                    'qty'        => $i->qty,
                    'price'      => $i->price,
                    'discount'   => 0.0,
                ], $items),
            );

            foreach ($items as $i) {
                $this->variants->decrementStock($i->variant_id, $i->qty);
                $this->sales->recordMovement($i->variant_id, -1 * $i->qty, 'out', 'sale', $saleId);
            }

            return $saleId;
        });
    }
}