<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends Controller
{
    /**
     * يبني السلة من الـ session (sales_cart)
     */
    protected function buildCartFromRaw(array $raw): array
    {
        $cart = [];

        foreach ($raw as $index => $row) {
            if (empty($row['variant_id'])) {
                continue;
            }

            $variant = ProductVariant::select('id', 'sku', 'name', 'price', 'stock_qty')
                ->find($row['variant_id']);

            if (!$variant) {
                continue;
            }

            $qty = (float) ($row['qty'] ?? 1);

            $cart[] = [
                'index' => $index,
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'name' => $variant->name,
                'price' => (float) $variant->price,
                'qty' => $qty,
                'stock_qty' => $variant->stock_qty,
            ];
        }

        return $cart;
    }

    /**
     * يحسب الإجمالي والخصم والمبلغ المدفوع والمتبقي وحالة الدفع
     */
    protected function calculateTotals(array $cart, ?float $paid, float $discount, string $paymentStatus): array
    {
        $subtotal = collect($cart)->sum(function ($i) {
            return floatval($i['price'] ?? 0) * floatval($i['qty'] ?? 0);
        });

        $manualDiscount = $discount;

        if ($paymentStatus === 'discount' && $paid !== null && $paid < $subtotal) {
            $discount = $subtotal - floatval($paid);
        } else {
            $discount = min($manualDiscount, $subtotal);
        }

        $total = max(0, $subtotal - $discount);

        if ($paymentStatus === 'credit') {
            if ($paid === null) {
                $paid = 0.0;
            }
        } else {
            if ($paid === null || ($paymentStatus !== 'discount' && $paid == 0.0)) {
                $paid = $total;
            }
        }

        $remaining = floatval($paid) - $total;

        if ($discount > 0.01 && abs($remaining) < 0.01) {
            $paymentStatus = 'discount';
        } elseif ($remaining < -0.01) {
            $paymentStatus = 'credit';
        } else {
            $paymentStatus = 'full';
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'paid' => $paid,
            'remaining' => $remaining,
            'payment_status' => $paymentStatus,
        ];
    }

    /**
     * عرض شاشة الفاتورة (للمرة الأولى فقط)
     */
    public function show()
    {
        return view('sales.invoice');
    }

    /**
     * جلب بيانات السلة عبر Ajax
     */
    public function getCart()
    {
        $rawCart = session('sales_cart', []);
        $cart = $this->buildCartFromRaw($rawCart);

        return response()->json([
            'ok' => true,
            'cart' => $cart
        ]);
    }

    /**
     * إضافة صنف للسلة عن طريق الـ SKU (Ajax)
     */
    public function addItem(Request $request)
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:50'],
        ]);

        $sku = trim($data['sku']);

        $variant = ProductVariant::select('id', 'sku', 'name', 'price', 'stock_qty')
            ->where('sku', $sku)
            ->first();

        if (!$variant) {
            return response()->json([
                'ok' => false,
                'message' => 'الصنف غير موجود'
            ], 404);
        }

        if ($variant->stock_qty <= 0) {
            return response()->json([
                'ok' => false,
                'message' => 'لا يوجد مخزون لهذا الصنف'
            ], 422);
        }

        $rawCart = session('sales_cart', []);

        // البحث عن الصنف إذا كان موجوداً مسبقاً
        $existingIndex = null;
        foreach ($rawCart as $index => $item) {
            if (isset($item['variant_id']) && $item['variant_id'] == $variant->id) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            $currentQty = floatval($rawCart[$existingIndex]['qty'] ?? 0);
            $newQty = $currentQty + 1;

            if ($newQty > $variant->stock_qty) {
                return response()->json([
                    'ok' => false,
                    'message' => 'المتوفر ' . $variant->stock_qty . ' فقط'
                ], 422);
            }

            $rawCart[$existingIndex]['qty'] = $newQty;
        } else {
            $rawCart[] = [
                'variant_id' => $variant->id,
                'qty' => 1,
            ];
        }

        session(['sales_cart' => $rawCart]);

        $cart = $this->buildCartFromRaw($rawCart);

        return response()->json([
            'ok' => true,
            'message' => 'تمت إضافة الصنف بنجاح',
            'cart' => $cart
        ]);
    }

    /**
     * تحديث كمية صنف في السلة (Ajax)
     */
    public function updateItem(Request $request)
    {
        $data = $request->validate([
            'index' => ['required', 'integer', 'min:0'],
            'qty' => ['required', 'numeric', 'min:1'],
        ]);

        $rawCart = session('sales_cart', []);
        $index = $data['index'];

        if (!isset($rawCart[$index])) {
            return response()->json([
                'ok' => false,
                'message' => 'الصنف غير موجود في السلة'
            ], 404);
        }

        $variant = ProductVariant::select('id', 'stock_qty')
            ->find($rawCart[$index]['variant_id']);

        if (!$variant) {
            return response()->json([
                'ok' => false,
                'message' => 'الصنف غير موجود في النظام'
            ], 404);
        }

        $newQty = floatval($data['qty']);

        if ($newQty > $variant->stock_qty) {
            return response()->json([
                'ok' => false,
                'message' => 'المتوفر ' . $variant->stock_qty . ' فقط'
            ], 422);
        }

        $rawCart[$index]['qty'] = $newQty;
        session(['sales_cart' => $rawCart]);

        $cart = $this->buildCartFromRaw($rawCart);

        return response()->json([
            'ok' => true,
            'cart' => $cart
        ]);
    }

    /**
     * إزالة صنف من السلة (Ajax) - معدل
     */
    public function removeItem(Request $request)
    {
        $data = $request->validate([
            'index' => ['required', 'integer', 'min:0'],
        ]);

        $rawCart = session('sales_cart', []);
        $index = $data['index'];

        // التأكد من وجود العنصر
        if (!isset($rawCart[$index])) {
            return response()->json([
                'ok' => false,
                'message' => 'الصنف غير موجود في السلة'
            ], 404);
        }

        // إزالة العنصر
        unset($rawCart[$index]);

        // إعادة ترتيب المفاتيح
        $rawCart = array_values($rawCart);

        // حفظ السلة الجديدة
        session(['sales_cart' => $rawCart]);

        // بناء السلة للعرض
        $cart = $this->buildCartFromRaw($rawCart);

        return response()->json([
            'ok' => true,
            'message' => 'تم حذف الصنف بنجاح',
            'cart' => $cart
        ]);
    }

    /**
     * تفريغ السلة بالكامل (Ajax)
     */
    public function resetCart()
    {
        session()->forget('sales_cart');

        return response()->json([
            'ok' => true,
            'message' => 'تم تفريغ السلة بنجاح'
        ]);
    }

    /**
     * إنهاء الفاتورة (Checkout - Ajax)
     */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['required', 'in:cash,wallet,instapay'],
            'payment_status' => ['required', 'in:full,discount,credit'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'paid' => ['nullable', 'numeric', 'min:0'],
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'cart.*.qty' => ['required', 'numeric', 'min:1'],
        ]);

        $cartInput = $data['cart'];
        $cart = [];

        // التحقق من جميع الأصناف
        foreach ($cartInput as $row) {
            $variant = ProductVariant::select('id', 'sku', 'name', 'price', 'stock_qty')
                ->find($row['variant_id']);

            if (!$variant) {
                return response()->json([
                    'ok' => false,
                    'message' => 'أحد الأصناف غير موجود'
                ], 422);
            }

            $qty = floatval($row['qty']);
            if ($qty <= 0) {
                continue;
            }

            if ($variant->stock_qty < $qty) {
                return response()->json([
                    'ok' => false,
                    'message' => "المخزون غير كاف للصنف {$variant->name}، المتوفر {$variant->stock_qty}"
                ], 422);
            }

            $cart[] = [
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'name' => $variant->name,
                'price' => (float) $variant->price,
                'qty' => $qty,
                'stock_qty' => $variant->stock_qty,
            ];
        }

        if (empty($cart)) {
            return response()->json([
                'ok' => false,
                'message' => 'السلة فارغة'
            ], 422);
        }

        $discount = floatval($data['discount'] ?? 0);
        $paidInput = $data['paid'] ?? null;
        $paid = $paidInput !== null && $paidInput !== '' ? floatval($paidInput) : null;
        $payment_status = $data['payment_status'];
        $payment_method = $data['payment_method'];
        $phone = $data['phone'] ?? null;

        $totals = $this->calculateTotals($cart, $paid, $discount, $payment_status);

        $subtotal = $totals['subtotal'];
        $discount = $totals['discount'];
        $total = $totals['total'];
        $paid = $totals['paid'];
        $remaining = $totals['remaining'];
        $payment_status = $totals['payment_status'];

        if (($payment_status === 'credit' || $remaining < -0.01) && blank($phone)) {
            return response()->json([
                'ok' => false,
                'message' => 'رقم الهاتف مطلوب للدفع الآجل'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $customer = null;

            if (filled($phone)) {
                Cache::forget("customer_{$phone}");

                $customer = Customer::firstOrCreate(
                    ['phone' => $phone],
                    ['name' => 'عميل - ' . $phone]
                );
            }

            $sale = Sale::create([
                'cashier_id' => Auth::id(),
                'customer_id' => $customer?->id,
                'customer_data' => $phone,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => 0,
                'total' => $total,
                'paid' => $paid ?? 0,
                'remaining' => $remaining < 0 ? abs($remaining) : 0,
                'payment_method' => $payment_method,
                'sale_type' => $payment_status,
                'date' => now(),
            ]);

            $cashAmount = $payment_method === 'cash' ? ($paid ?? 0) : 0;
            $walletAmount = $payment_method === 'wallet' ? ($paid ?? 0) : 0;
            $instaAmount = $payment_method === 'instapay' ? ($paid ?? 0) : 0;

            Balance::create([
                'customer_id' => $customer?->id,
                'sale_id' => $sale->id,
                'cash_amount' => $cashAmount,
                'wallet_amount' => $walletAmount,
                'instapay_amount' => $instaAmount,
                'total_amount' => $paid ?? 0,
                'payment_date' => now(),
                'status' => 'completed',
            ]);

            foreach ($cart as $item) {
                $variantId = $item['variant_id'];
                $qty = floatval($item['qty']);

                $sale->items()->create([
                    'variant_id' => $variantId,
                    'price' => $item['price'],
                    'qty' => $qty,
                ]);

                ProductVariant::where('id', $variantId)->decrement('stock_qty', $qty);

                Cache::forget("variant_{$variantId}");
                Cache::forget("sku_{$item['sku']}");
            }

            if ($remaining < -0.01 && $customer) {
                $sale->credit()->create([
                    'customer_id' => $customer->id,
                    'sale_id' => $sale->id,
                    'remaining' => abs($remaining),
                    'description' => 'دين من فاتورة رقم #' . $sale->id,
                    'date' => now(),
                ]);
            }

            DB::commit();

            session()->forget('sales_cart');

            $receiptUrl = route('receipt.show', $sale->id);

            return response()->json([
                'ok' => true,
                'message' => "تم إنشاء الفاتورة #{$sale->id} بنجاح",
                'sale_id' => $sale->id,
                'receipt_url' => $receiptUrl
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'message' => 'فشل في إنشاء الفاتورة: ' . $e->getMessage()
            ], 500);
        }
    }
}