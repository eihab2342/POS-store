<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $query = Credit::query()
            ->with(['customer', 'sale'])
            ->whereHas('sale', function ($q) {
                $q->where('status', 'open');
            })
            ->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('sale_id', $search)
                    ->orWhereHas('customer', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // فلتر عميل معيّن
        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // فلتر تاريخ (من/إلى) على created_at
        $from = $request->input('from_date');
        $to = $request->input('to_date');

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $credits = $query->paginate(25)->withQueryString();

        return view('credits.index', [
            'credits' => $credits,
            'filters' => [
                'search' => $search ?? '',
                'customer_id' => $customerId ?? '',
                'from_date' => $from,
                'to_date' => $to,
            ],
        ]);
    }

    public function show(Credit $credit)
    {
        $credit->load(['customer', 'sale.items.productVariant', 'sale.cashier']);

        return view('credits.show', compact('credit'));
    }

    public function pay(Request $request, Credit $credit)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $amount = (float) $data['amount'];

        if ($amount > $credit->remaining) {
            return back()->withErrors([
                'amount' => 'المبلغ المدفوع أكبر من المتبقي على الأجل.',
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            $newRemaining = $credit->remaining - $amount;

            // ✅ تحديث الأجل
            $credit->update([
                'remaining' => max($newRemaining, 0),
            ]);

            // ✅ تحديث الفاتورة المرتبطة لو موجودة
            if ($credit->sale) {
                $sale = $credit->sale;

                $sale->update([
                    'paid' => ($sale->paid ?? 0) + $amount,
                    'remaining' => max(($sale->remaining ?? 0) - $amount, 0),
                ]);
            }

            // ✅ لو عندك موديل CreditPayment زي اللي في Filament
            if (class_exists(Credit::class)) {
                Credit::create([
                    'credit_id' => $credit->id,
                    'amount' => $amount,
                    'notes' => $data['notes'] ?? null,
                    'paid_by' => auth('')->id(),
                    'paid_at' => now(),
                ]);
            }

            // لو اتسدد بالكامل نحذفه من الأجلات
            if ($newRemaining <= 0) {
                $credit->delete();
                DB::commit();

                return redirect()
                    ->route('credits.index')
                    ->with('success', 'تم تسديد الأجل بالكامل وإزالته من القائمة.');
            }

            DB::commit();

            return redirect()
                ->route('credits.show', $credit)
                ->with('success', "تم تسجيل دفع {$amount} ج.م، المتبقي الآن: ".number_format($newRemaining, 2).' ج.م');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['amount' => 'حدث خطأ أثناء التسديد: '.$e->getMessage()])
                ->withInput();
        }
    }
}
