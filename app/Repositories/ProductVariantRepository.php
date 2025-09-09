<?php

namespace App\Repositories;

use App\DTOs\CartItem;
use App\Interfaces\ProductVarientRepoInterface;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;

class ProductVariantRepository implements ProductVarientRepoInterface
{
    public function findCartItemBySku(string $sku): ?CartItem
    {
        $v = ProductVariant::with('product')->where('sku', $sku)->first();

        if (!$v) return null;

        $name = trim($v->product?->name ?? 'منتج' . ' - ' . $v->color ?? '' . ' - ' . $v->size ?? '');
        return new CartItem(
            variant_id: $v->id,
            name: trim(($v->product?->name ?? 'منتج') . ' - ' . ($v->size ?? '') . ' ' . ($v->color ?? '')),
            price: (float) $v->price,
            sku: (string) $v->sku,
            qty: 1,
        );
    }

    public function ensureStockAvailable(int $variantId, int $qty): void
    {
        $v = ProductVariant::find($variantId);
        if (!$v) throw new \RuntimeException('العنصر غير موجود');
        if ($v->stock_qty < $qty) throw new \RuntimeException("الكمية غير متاحة للـ SKU {$v->sku}");
    }

    public function decrementStock(int $variantId, int $qty): void
    {
        ProductVariant::where('id', $variantId)->decrement('stock_qty', $qty);
    }
}