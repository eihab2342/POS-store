<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        // ابدأ بـ Builder وليس Collection
        $query = Sale::query()->with('customer', 'items.productVariant') // ضف العلاقة مع المنتج
            ->orderByDesc('date');

        // طبّق الفلاتر على الـ Query قبل التنفيذ
        $this->applyFilters($query, $request);

        // نجيب النتائج بعد الفلترة
        $sales = $query->paginate(25)->withQueryString();

        // استخدم نفس الـ Query في الإجماليات (بدون paginate)
        $aggregateQuery = clone $query;
        $totalRevenue = (clone $aggregateQuery)->sum('total');

        // حساب إجمالي الربح
        $totalProfit = $sales->sum(function ($sale) {
            return $sale->items->sum(function ($item) {
                // احسب الربح لكل منتج (سعر البيع - سعر الشراء)
                $cost = $item->productVariant->cost ?? 0; // افترض أن cost هو سعر الشراء
                $price = $item->price ?? 0; // سعر البيع
                $qty = $item->qty ?? 0; // الكمية المباعة

                // اجمع الربح بناءً على الكمية
                return ($price - $cost) * $qty;
            });
        });

        $filters = [
            'search' => $request->input('search', ''),
            'date_filter' => $request->input('date_filter', ''),
            'date' => $request->input('date', ''),
            'from' => $request->input('from', ''),
            'until' => $request->input('until', ''),
        ];

        return view('profits.index', compact('sales', 'totalRevenue', 'totalProfit', 'filters'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.productVariant', 'cashier']);

        return view('profits.show', compact('sale'));
    }

    public function printReport(Request $request)
    {
        // 1) نبني الكويري الأساسي + العلاقات اللي هتحتاجها
        $query = Sale::query()
            ->with([
                'customer',
                'items.productVariant', // مهم عشان نجيب الـ cost من الـ ProductVariant
            ])
            ->orderByDesc('date');

        // 2) نطبّق الفلاتر اللي عندك (اليوم، الشهر، رينج تواريخ... إلخ)
        $query = $this->applyFilters($query, $request);

        // 3) نجيب كل الفواتير بعد الفلترة
        $sales = $query->get();

        // 4) إجمالي المبيعات (الإيراد)
        $totalRevenue = $sales->sum('total');

        // 5) نحسب الربح لكل فاتورة + الإجمالي
        $totalProfit = 0;

        foreach ($sales as $sale) {

            // ربح الفاتورة الواحدة
            $saleProfit = 0;

            foreach ($sale->items as $item) {
                $variant = $item->productVariant;

                // سعر الشراء (cost) من جدول المنتجات
                $cost = $variant?->cost ?? 0;

                // سعر البيع المخزن في الفاتورة
                $price = $item->price ?? 0;

                $qty = $item->qty ?? 0;

                // ربح هذا السطر = (سعر البيع - سعر الشراء) * الكمية
                $saleProfit += ($price - $cost) * $qty;
            }

            // نخلي الربح متخزن على الأوبجيكت لو حابب تستخدمه في الـ view
            $sale->calculated_profit = $saleProfit;

            // نضيفه على إجمالي الربح
            $totalProfit += $saleProfit;
        }

        // 6) نص يوضح الفلترة المستخدمة (اليوم / من كذا لكذا...)
        $periodLabel = $this->periodLabelFromRequest($request);

        // 7) نبعته للـ view
        return view('profits.report', compact(
            'sales',
            'totalRevenue',
            'totalProfit',
            'periodLabel'
        ));
    }

    /**
     * نفس منطق الفلاتر اللي كان في ProfitResource (اليوم/الأسبوع/الشهر + تاريخ محدد + من/إلى)
     */
    private function applyFilters(Builder $query, Request $request): Builder
    {
        // 🔍 بحث بـ رقم فاتورة أو اسم عميل
        if ($search = $request->input('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('customer', fn(Builder $qq) =>
                        $qq->where('name', 'like', "%{$search}%"));
            });
        }

        // فلتر فترات جاهزة
        if ($value = $request->input('date_filter')) {
            $query = match ($value) {
                'today' => $query->whereDate('date', now()->toDateString()),
                'this_week' => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month),
                default => $query,
            };
        }

        // تاريخ محدد
        if ($specific = $request->input('date')) {
            $query->whereDate('date', $specific);
        }

        // مدى تواريخ
        if ($from = $request->input('from')) {
            $query->whereDate('date', '>=', $from);
        }
        if ($until = $request->input('until')) {
            $query->whereDate('date', '<=', $until);
        }

        return $query;
    }

    private function periodLabelFromRequest(Request $request): string
    {
        if ($df = $request->input('date_filter')) {
            return match ($df) {
                'today' => 'تقرير اليوم',
                'this_week' => 'تقرير هذا الأسبوع',
                'this_month' => 'تقرير هذا الشهر',
                default => '',
            };
        }

        $parts = [];

        if ($d = $request->input('date')) {
            $parts[] = 'التاريخ: ' . \Carbon\Carbon::parse($d)->format('d/m/Y');
        }

        if ($f = $request->input('from')) {
            $parts[] = 'من: ' . \Carbon\Carbon::parse($f)->format('d/m/Y');
        }

        if ($u = $request->input('until')) {
            $parts[] = 'إلى: ' . \Carbon\Carbon::parse($u)->format('d/m/Y');
        }

        return implode(' - ', $parts) ?: 'كل الفواتير';
    }


}
