<?php

namespace App\Http\Controllers;

use App\Models\{Product, Sale, SaleItem, StockMovement};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class POSController extends Controller
{
    public function index()
    {
        return Inertia::render('POS');
    }


    public function scan(Request $request)
    {
        $variant = \App\Models\ProductVariant::where('sku', $request->code)->first();

        if (!$variant) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json([
            'id'    => $variant->id,
            'sku'   => $variant->sku,
            'name'  => ($variant->size ?? '') . ' ' . ($variant->color ?? ''),
            'price' => $variant->price,
        ]);
    }

    public function checkout(Request $request)
    {
        $items = $request->input('items', []);

        $sale = DB::transaction(function () use ($items) {
            $subtotal = collect($items)->sum(fn($i) => $i['qty'] * $i['price']);

            $sale = Sale::create([
                'date'           => now(),
                'cashier_id'     => Auth::id() ?? 1,
                'customer_id'    => null,
                'subtotal'       => $subtotal,
                'discount'       => 0,
                'tax'            => 0,
                'total'          => $subtotal,
                'payment_method' => 'cash',
            ]);

            foreach ($items as $i) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'variant_id' => $i['variant_id'],
                    'qty'        => $i['qty'],
                    'price'      => $i['price'],
                    'discount'   => $i['discount'] ?? 0,
                ]);

                \App\Models\ProductVariant::where('id', $i['variant_id'])
                    ->decrement('stock_qty', $i['qty']);
            }

            return $sale;
        });

        return response()->json([
            'status'  => 'ok',
            'sale_id' => $sale->id,
        ]);
    }
    public function receipt(Sale $sale)
    {
        return view('receipt', [
            'sale' => $sale->load('items.variant.product')
        ]);
    }
}