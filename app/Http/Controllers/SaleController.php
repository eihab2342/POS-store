<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // تحديد الجدول في orderBy لتجنب الأخطاء عند عمل Join
        $query = Sale::query()
            ->with(['items.productVariant']) 
            ->orderByDesc('sales.date');

        // 🔍 بحث برقم الفاتورة - تم تحديد sales.id لحل مشكلة التداخل
        if ($search = $request->input('search')) {
            $query->where('sales.id', $search);
        }

        // 📅 فلاتر التاريخ والمدد
        $this->applyDateFilters($query, $request);

        // 💳 طريقة الدفع
        if ($payment = $request->input('payment_method')) {
            $query->where('payment_method', $payment);
        }

        // 🔢 حساب الإجماليات قبل الـ Pagination باستخدام clone
        $aggregateQuery = clone $query;
        
        // استخدام sales.total لتجنب أي تداخل
        $totalRevenue = (clone $aggregateQuery)->sum('sales.total');
        
        // حساب إجمالي الربح باستخدام Join
        $totalProfit = (clone $aggregateQuery)
            ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('product_variants', 'sale_items.variant_id', '=', 'product_variants.id')
            ->selectRaw('COALESCE(SUM((sale_items.price - product_variants.cost) * sale_items.qty), 0) as profit_sum')
            ->value('profit_sum');

        // جلب البيانات
        $sales = $query->paginate(25)->withQueryString();

        // حساب الربح لكل فاتورة يدوياً للعرض في الجدول
        $sales->getCollection()->transform(function (Sale $sale) {
            $sale->calculated_profit = $sale->items->sum(function ($item) {
                return (($item->price ?? 0) - ($item->productVariant->cost ?? 0)) * ($item->qty ?? 0);
            });
            return $sale;
        });

        return view('sales.index', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'totalProfit' => $totalProfit,
            'filters' => $request->all()
        ]);
    }

    private function applyDateFilters($query, $request)
    {
        $period = $request->input('period');
        if ($period) {
            switch ($period) {
                case 'today': $query->whereDate('sales.date', today()); break;
                case 'yesterday': $query->whereDate('sales.date', today()->subDay()); break;
                case 'this_week': $query->whereBetween('sales.date', [now()->startOfWeek(), now()->endOfWeek()]); break;
                case 'this_month': $query->whereMonth('sales.date', now()->month)->whereYear('sales.date', now()->year); break;
                case 'last_month': $query->whereMonth('sales.date', now()->subMonth()->month); break;
            }
        } else {
            if ($date = $request->input('date')) $query->whereDate('sales.date', $date);
            if ($from = $request->input('from_date')) $query->whereDate('sales.date', '>=', $from);
            if ($to = $request->input('to_date')) $query->whereDate('sales.date', '<=', $to);
        }
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'paid'           => 'required|numeric|min:0',
            'discount'       => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,visa',
        ]);
        if ($sale->customer) {
            $sale->customer->update(['name' => $request->customer_name]);
        }

        $newTotal = $sale->subtotal - $request->discount;
        $newRemaining = $newTotal - $request->paid;

        $sale->update([
            'customer_data'  => $request->customer_name,
            'paid'           => $request->paid,
            'discount'       => $request->discount,
            'total'          => $newTotal,
            'remaining'      => $newRemaining,
            'payment_method' => $request->payment_method,
        ]);
// dd($sale);
        return back()->with('success', 'تم تحديث بيانات الفاتورة بنجاح');
    }

    public function show(Sale $sale) {
        $sale->load(['customer', 'cashier', 'items.productVariant']);
        return view('sales.show', compact('sale'));
    }

    // حذف فاتورة واحدة مع إرجاع المخزن
    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                if ($item->productVariant) {
                    // تم استخدام stock_qty بناءً على تحديثك الأخير
                    $item->productVariant->increment('stock_qty', $item->qty);
                }
            }
            $sale->delete();
        });

        return back()->with('success', 'تم حذف الفاتورة وإعادة المنتجات للمخزن');
    }

    // حذف جماعي مع إرجاع المخزن
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'لم يتم تحديد فواتير'], 400);
        }

        DB::transaction(function () use ($ids) {
            $sales = Sale::whereIn('id', $ids)->with('items.productVariant')->get();
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    if ($item->productVariant) {
                        // موحد هنا أيضاً ليكون stock_qty
                        $item->productVariant->increment('stock_qty', $item->qty);
                    }
                }
                $sale->delete();
            }
        });

        return response()->json(['success' => true, 'message' => 'تم حذف المختار وإعادة المخزون']);
    }
}