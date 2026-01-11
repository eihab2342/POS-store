<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    /**
     * عرض كل المرتجعات مع الفلاتر
     */
    public function index(Request $request)
    {
        $query = SaleReturn::with(['sale', 'variant', 'user'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sale', function ($q) use ($search) {
                $q->where('sales.id', 'like', "%$search%");
            });
        }

        $returns = $query->paginate(25)->withQueryString();

        return view('sales-return.index', compact('returns'));
    }

    /**
     * عرض نموذج إنشاء مرتجع جديد - هذه الدالة الناقصة
     */
    public function create()
    {
        // يمكنك إرجاع الفاتورة الأولى مباشرة أو عرض قائمة
        $latestSale = Sale::with('customer')->latest()->first();

        return view('sales-return.create', [
            'sale' => $latestSale,
            'sales' => Sale::with('customer')->latest()->take(20)->get(),
        ]);
    }

    /**
     * تسجيل مرتجع جديد وتعديل الفاتورة والمخزن
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => ['required', 'exists:sales,id'],
            'items' => ['required', 'array'],
            'items.*.product_variant_id' => ['required', 'exists:product_variants,id'],
            'items.*.returned_qty' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
            'refund_method' => ['required', 'in:cash,wallet,credit'],
        ]);

        // لازم عنصر واحد على الأقل يكون فيه مرتجع > 0
        $totalReturnedQty = collect($data['items'])->sum(function ($it) {
            return (float) ($it['returned_qty'] ?? 0);
        });

        if ($totalReturnedQty <= 0) {
            return back()
                ->withErrors(['items' => 'لازم تختار كمية مرتجعة (على الأقل منتج واحد).'])
                ->withInput();
        }

        DB::transaction(function () use ($data) {
            $sale = Sale::with(['items.productVariant', 'credit'])->findOrFail($data['sale_id']);

            $totalReturnAmount = 0;
            $itemsData = [];

            // **الخطوة 1: حساب المرتجع مع مراعاة الخصم**
            foreach ($data['items'] as $item) {
                $returnedQty = (float) ($item['returned_qty'] ?? 0);
                if ($returnedQty <= 0) continue;

                $saleItem = $sale->items->firstWhere('variant_id', $item['product_variant_id']);
                if (!$saleItem) continue;

                // ماينفعش يرجع أكتر من المباع
                $originalQty = (float) ($saleItem->qty ?? 0);
                $returnedQty = min($returnedQty, $originalQty);

                // **تحديد السعر بعد الخصم**
                $originalPrice = (float) ($saleItem->original_price ?? $saleItem->price);
                $discountedPrice = (float) ($saleItem->price ?? 0);

                // إذا كان هناك خصم عام على الفاتورة
                $discountRatio = ($sale->discount > 0 && $sale->total > 0)
                    ? (1 - ($sale->total / ($sale->subtotal ?? $sale->total)))
                    : 0;

                $effectivePrice = $discountedPrice;
                if ($discountRatio > 0) {
                    // توزيع الخصم على المنتجات المرتجعة
                    $effectivePrice = $originalPrice * (1 - $discountRatio);
                }

                $itemReturnAmount = $effectivePrice * $returnedQty;
                $totalReturnAmount += $itemReturnAmount;

                $itemsData[] = [
                    'saleItem' => $saleItem,
                    'returnedQty' => $returnedQty,
                    'price' => $effectivePrice,
                    'originalPrice' => $originalPrice,
                    'itemReturnAmount' => $itemReturnAmount,
                    'variant_id' => $item['product_variant_id'],
                ];
            }

            // **الخطوة 2: تحديد الحد الأقصى للمرتجع بناءً على حالة الفاتورة**
            $maxAllowedRefund = $this->calculateMaxRefund($sale, $totalReturnAmount);

            // **الخطوة 3: التحقق من الحد الأقصى (لطرق الدفع النقدية)**
            // if ($data['refund_method'] !== 'credit' && $totalReturnAmount > $maxAllowedRefund) {
            //     throw new \Exception(
            //         "قيمة المرتجع ({$totalReturnAmount}) تجاوزت الحد المسموح ({$maxAllowedRefund}). " .
            //         "الفاتورة {$this->getSaleTypeText($sale)}."
            //     );
            // }

            // **الخطوة 4: تنفيذ المرتجع**
            foreach ($itemsData as $itemData) {
                // تسجيل المرتجع
                SaleReturn::create([
                    'sale_id' => $sale->id,
                    'product_variant_id' => $itemData['variant_id'],
                    'returned_qty' => $itemData['returnedQty'],
                    'returned_amount' => $itemData['itemReturnAmount'],
                    'reason' => $data['reason'] ?? 'مرتجع مبيعات',
                    'refund_method' => $data['refund_method'],
                    'user_id' => auth('')->id(),
                ]);

                // زيادة المخزن
                $itemData['saleItem']->productVariant?->increment('stock_qty', $itemData['returnedQty']);

                // تحديث/حذف سطر الفاتورة
                $newQty = max(0, $itemData['saleItem']->qty - $itemData['returnedQty']);
                if ($newQty == 0) {
                    $itemData['saleItem']->update(['qty' => 0]);
                    $itemData['saleItem']->delete();
                } else {
                    $itemData['saleItem']->update(['qty' => $newQty]);
                }
            }

            // **الخطوة 5: تحديث الفاتورة بناءً على طريقة الاسترداد**
            $this->updateSaleAfterReturn($sale, $totalReturnAmount, $data['refund_method']);

            // **الخطوة 6: تحديث الائتمان إذا كان فاتورة أجل**
            if ($sale->credit) {
                $sale->credit->update([
                    'total_amount' => $sale->total,
                    'remaining' => $sale->remaining,
                ]);
            }
        });

        return redirect()->route('returns.index')->with('success', 'تم تسجيل المرتجع وتحديث الحسابات بنجاح ✅');
    }

    /**
     * حساب الحد الأقصى للمرتجع بناءً على نوع الفاتورة
     */
    private function calculateMaxRefund(Sale $sale, float $totalReturnAmount): float
    {
        $saleType = $sale->sale_type ?? 'cash';
        $paidAmount = (float) $sale->paid;
        $totalAmount = (float) $sale->total;
        $discountAmount = (float) $sale->discount;

        switch ($saleType) {
            case 'credit': // فاتورة أجل
                // في الأجل: يرجع فقط المدفوع + نسبة من الخصم إن وجد
                $maxRefund = $paidAmount;

                // إذا كان هناك خصم، نرجع نسبة منه للمنتجات المرتجعة
                if ($discountAmount > 0 && $totalAmount > 0) {
                    $returnRatio = $totalReturnAmount / $totalAmount;
                    $discountShare = $discountAmount * $returnRatio;
                    $maxRefund += $discountShare;
                }
                return $maxRefund;

            case 'cash': // كاش
                // في الكاش: يرجع نسبة من المدفوع
                if ($paidAmount == $totalAmount) {
                    // دفع بالكامل
                    return $totalReturnAmount; // يرجع قيمة المنتجات نفسها
                } else {
                    // دفع جزئي (نقداً)
                    $paidRatio = $paidAmount / $totalAmount;
                    return $totalReturnAmount * $paidRatio;
                }

            default:
                // أي حالة أخرى
                return $paidAmount;
        }
    }

    /**
     * تحديث الفاتورة بعد المرتجع
     */
    private function updateSaleAfterReturn(Sale $sale, float $totalReturnAmount, string $refundMethod): void
    {
        $oldTotal = (float) $sale->total;
        $oldPaid = (float) $sale->paid;

        // **تحديث الإجمالي**
        $newTotal = max(0, $oldTotal - $totalReturnAmount);

        // **تحديث المدفوع بناءً على طريقة الاسترداد ونوع الفاتورة**
        if ($refundMethod === 'credit') {
            // رصيد: المدفوع يبقى كما هو، المتبقي يزيد
            $newPaid = $oldPaid;
            $newRemaining = max(0, $newTotal - $newPaid);
        } else {
            // نقداً/محفظة: نقلل المدفوع
            // لكن نحسب النسبة إذا كانت الفاتورة أجل
            $saleType = $sale->sale_type ?? 'cash';

            if ($saleType === 'credit') {
                // فاتورة أجل: نقلل المدفوع فقط (لأن المتبقي لم يدفع)
                $returnRatio = $totalReturnAmount / $oldTotal;
                $paidReduction = $oldPaid * $returnRatio;
                $newPaid = max(0, $oldPaid - $paidReduction);
            } else {
                // كاش: نقلل المدفوع بالقيمة الكاملة
                $newPaid = max(0, $oldPaid - $totalReturnAmount);
            }

            $newRemaining = max(0, $newTotal - $newPaid);
        }

        $sale->update([
            'total' => $newTotal,
            'paid' => $newPaid,
            'remaining' => $newRemaining,
        ]);

        // **إغلاق الفاتورة إذا انتهت**
        $freshItemsCount = $sale->items()->count();
        if ($freshItemsCount === 0 || $sale->total == 0) {
            $sale->update(['status' => 'closed']);
        }
    }

    /**
     * وصف نوع الفاتورة
     */
    private function getSaleTypeText(Sale $sale): string
    {
        $types = [
            'cash' => 'كاش',
            'credit' => 'أجل',
            'installment' => 'تقسيط',
        ];

        $type = $sale->sale_type ?? 'cash';
        $paid = (float) $sale->paid;
        $total = (float) $sale->total;

        $text = $types[$type] ?? 'غير معروف';

        if ($type === 'credit') {
            $text .= " (مدفوع: {$paid} / إجمالي: {$total})";
        } elseif ($paid < $total) {
            $text .= " (دفع جزئي: {$paid} / {$total})";
        }

        return $text;
    }

    public function show(SaleReturn $return)
    {
        $return->load(['sale.customer', 'variant', 'user']);

        return view('sales-return.show', compact('return'));
    }

    public function edit(SaleReturn $return)
    {
        $return->load(['sale.customer', 'sale.items', 'variant']);

        $saleItem = $return->sale?->items?->firstWhere('variant_id', $return->product_variant_id);
        $existingQty = (int) ($saleItem->qty ?? 0);
        $maxQty = $existingQty + (int) $return->returned_qty;

        return view('sales-return.edit', [
            'saleReturn' => $return,
            'maxQty' => $maxQty,
        ]);
    }

    /**
     * تحديث مرتجع موجود
     */
    public function update(Request $request, SaleReturn $return)
    {
        $request->validate([
            'returned_qty' => ['required', 'integer', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $return->load(['sale.items.productVariant', 'variant', 'sale.credit']);
        $sale = $return->sale;

        if (!$sale) {
            return back()->with('error', 'خطأ: الفاتورة الأصلية لهذا المرتجع غير موجودة!');
        }

        DB::transaction(function () use ($request, $return, $sale) {
            $saleItem = $sale->items->firstWhere('variant_id', $return->product_variant_id);

            $existingQty = (int) ($saleItem->qty ?? 0);
            $originalQty = $existingQty + (int) $return->returned_qty; // الكمية الأصلية المباعه
            $newReturnedQty = (int) $request->returned_qty;

            if ($newReturnedQty > $originalQty) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'returned_qty' => "أقصى كمية مسموح بها لهذا المنتج هي: {$originalQty}",
                ]);
            }

            $qtyDifference = $newReturnedQty - (int) $return->returned_qty; // + يعني زودنا المرتجع / - يعني قللنا المرتجع

            $unitPrice = (float) ($saleItem->price ?? ($return->sale->items()->where('variant_id', $return->product_variant_id)->value('price') ?? 0));
            $unitCost = (float) ($return->variant->cost ?? 0);

            $amountDifference = $qtyDifference * $unitPrice;

            // ✅ **التحقق من الحد الأقصى للمرتجع**
            if ($amountDifference > 0 && $return->refund_method !== 'credit') {
                $newTotalRefund = ($return->returned_qty + $qtyDifference) * $unitPrice;
                $maxAllowedRefund = $this->calculateMaxRefund($sale, $newTotalRefund);

                if ($newTotalRefund > $maxAllowedRefund) {
                    throw new \Exception("قيمة المرتجع ({$newTotalRefund}) تجاوزت الحد المسموح ({$maxAllowedRefund})");
                }
            }

            if ($return->variant) {
                $return->variant->increment('stock_qty', $qtyDifference);
            }

            $newSaleItemQty = $existingQty - $qtyDifference; // لاحظ الإشارة
            if ($newSaleItemQty <= 0) {
                if ($saleItem) {
                    $saleItem->delete();
                }
            } else {
                if ($saleItem) {
                    $saleItem->update(['qty' => $newSaleItemQty]);
                } else {
                    $sale->items()->create([
                        'variant_id' => $return->product_variant_id,
                        'qty' => $newSaleItemQty,
                        'price' => $unitPrice,
                        'discount' => 0,
                    ]);
                }
            }

            // ✅ **تحديث الفاتورة بالمنطق الصحيح**
            $newTotal = max(0, (float) $sale->total - $amountDifference);

            if ($return->refund_method === 'credit') {
                $newPaid = (float) $sale->paid; // المدفوع يبقى كما هو
                $newRemaining = max(0, $newTotal - $newPaid);
            } else {
                $newPaid = max(0, (float) $sale->paid - $amountDifference);
                $newRemaining = max(0, $newTotal - $newPaid);
            }

            $sale->update([
                'total' => $newTotal,
                'paid' => $newPaid,
                'remaining' => $newRemaining,
            ]);

            // تحديث المرتجع نفسه
            $return->update([
                'returned_qty' => $newReturnedQty,
                'reason' => $request->reason,
            ]);

            // Close / Open للفاتورة
            $itemsCount = $sale->items()->count();
            if ($itemsCount === 0 || (float) $sale->total == 0) {
                $sale->update(['status' => 'closed']);
            } else {
                if ($sale->status === 'closed') {
                    $sale->update(['status' => 'open']);
                }
            }

            // تحديث الائتمان
            if ($sale->credit) {
                $sale->credit->update([
                    'total_amount' => $sale->total,
                    'remaining_amount' => $sale->remaining,
                ]);
            }
        });

        return redirect()->route('returns.index')->with('success', 'تم تحديث المرتجع بنجاح ✅');
    }

    /**
     * حذف المرتجع
     */
    public function destroy(SaleReturn $return)
    {
        $return->load('sale');
        $sale = $return->sale;

        DB::transaction(function () use ($return, $sale) {
            if ($sale) {
                $saleItem = $sale->items()->where('variant_id', $return->product_variant_id)->first();
                $unitPrice = $saleItem ? $saleItem->price : 0;

                // إعادة الفلوس للفاتورة
                $refundAmount = $return->returned_qty * $unitPrice;
                $newTotal = $sale->total + $refundAmount;

                if ($return->refund_method === 'credit') {
                    $newPaid = $sale->paid; // المدفوع يبقى كما هو
                } else {
                    $newPaid = $sale->paid + $refundAmount;
                }

                $newRemaining = max(0, $newTotal - $newPaid);

                $sale->update([
                    'total' => $newTotal,
                    'paid' => $newPaid,
                    'remaining' => $newRemaining,
                ]);

                // خصم المخزن
                if ($return->variant) {
                    $return->variant->decrement('stock_qty', $return->returned_qty);
                }
            }
            $return->delete();
        });

        return redirect()->route('returns.index')->with('success', 'تم حذف المرتجع بنجاح 🗑️');
    }

    public function getSaleDetails($id)
    {
        $sale = Sale::with(['items.productVariant', 'customer'])->find($id);

        if (!$sale) {
            return response()->json(['error' => 'الفاتورة غير موجودة'], 404);
        }

        // حساب الحدود المسموحة للمرتجع
        $maxRefundData = $this->calculateMaxRefundForApi($sale);

        return response()->json([
            'id' => $sale->id,
            'customer' => $sale->customer->name ?? '-',
            'total' => (float) $sale->total,
            'paid' => (float) $sale->paid,
            'remaining' => (float) $sale->remaining,
            'sale_type' => $sale->sale_type ?? 'cash',
            'sale_type_text' => $this->getSaleTypeText($sale),
            'max_refund' => $maxRefundData,
            'items' => $sale->items->map(fn ($item) => [
                'id' => $item->id,
                'variant_id' => $item->productVariant->id ?? null,
                'variant_name' => $item->productVariant->name ?? '',
                'price' => (float) $item->price,
                'original_price' => (float) ($item->original_price ?? $item->price),
                'qty' => (int) $item->qty,
            ]),
        ]);
    }

    /**
     * حساب الحد الأقصى للمرجع للـ API
     */
    private function calculateMaxRefundForApi(Sale $sale): array
    {
        $saleType = $sale->sale_type ?? 'cash';
        $paid = (float) $sale->paid;
        $total = (float) $sale->total;

        switch ($saleType) {
            case 'credit':
                return [
                    'max_cash' => $paid, // الحد الأقصى للنقد
                    'max_credit' => $total, // الحد الأقصى للرصيد (يمكن إرجاع الكل كرصيد)
                    'message' => "فاتورة أجل: المدفوع {$paid} ج.م، يمكن إرجاعه نقداً أو كرصيد"
                ];

            case 'cash':
                if ($paid === $total) {
                    return [
                        'max_cash' => $total,
                        'max_credit' => $total,
                        'message' => "فاتورة كاش مدفوعة بالكامل"
                    ];
                } else {
                    return [
                        'max_cash' => $paid,
                        'max_credit' => $total,
                        'message' => "فاتورة كاش مدفوعة جزئياً: {$paid} من {$total} ج.م"
                    ];
                }

            default:
                return [
                    'max_cash' => $paid,
                    'max_credit' => $total,
                    'message' => "فاتورة عادية"
                ];
        }
    }
}