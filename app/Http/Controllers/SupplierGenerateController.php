<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SupplierGenerateController extends Controller
{
    private $cacheDuration = 60 * 24;

    /**
     * عرض كل الموردين
     */
    public function index(Request $request)
    {
        $query = Supplier::query()->with('purchases', 'payments');
	//dd($query);
        // بحث
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // فلتر الموردين اللي عليهم فلوس
        /*if ($request->has('has_debt') && $request->has_debt) {
            $query->whereRaw('total_debt > total_paid');
        }*/
	
        $suppliers = $query->orderByDesc('id')->paginate(25)->withQueryString();
        //dd($suppliers);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * عرض فورم إضافة مورد
     */
    public function create()
    {
        return view('suppliers.form');
    }

    /**
     * حفظ مورد جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:suppliers,phone',
            'email' => 'nullable|email|unique:suppliers,email',
            'address' => 'nullable|string|max:500',
            'opening_balance' => 'numeric',
            'notes' => 'nullable|string',
        ], [
            'name.required' => 'اسم المورد مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مسجل من قبل',
        ]);
	$validated['current_balance'] = $validated['opening_balance'];
	$validated['code'] = rand(000,999);
        $supplier = Supplier::create($validated);

        Cache::forget('suppliers:all');
        Cache::forget('suppliers:options');

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    /**
     * عرض تفاصيل المورد
     */
    public function show(Supplier $supplier)
    {
        $supplier->load(['purchases' => function ($q) {
            $q->orderByDesc('created_at');
        }, 'payments' => function ($q) {
            $q->orderByDesc('payment_date');
        }]);
        //dd($supplier);
        // احسب الديون
        $totalDebt = $supplier->opening_balance ?? 0;
        $totalPaid = $supplier->payments->sum('amount') ?? 0;
        $remainingBalance = $totalDebt - $totalPaid;
        $openingBalance = $supplier->opening_balance ?? 0;
        $current_balance = $supplier->current_balance ?? 0;
	//dd($totalDebt,$totalPaid,$remainingBalance,$openingBalance ,$current_balance  );
        return view('suppliers.show', compact('supplier', 'totalDebt', 'totalPaid', 'remainingBalance', 'openingBalance', 'current_balance'));
    }
  
    /**
     * عرض فورم التعديل
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', compact('supplier'));
    }

    /**
     * تحديث المورد
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:suppliers,phone,' . $supplier->id,
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        Cache::forget('suppliers:all');
        Cache::forget('suppliers:options');

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'تم تحديث المورد بنجاح');
    }

    /**
     * حذف المورد
     */
    public function destroy(Supplier $supplier)
    {
        try {
            // تأكد إنه مفيش ديون عليه
            $remaining = $supplier->total_debt - $supplier->total_paid;
            if ($remaining > 0) {
                return back()->with('error', 'لا يمكن حذف المورد لأن عليك ديون له: ' . number_format($remaining, 2) . ' جنيه');
            }

            $supplier->delete();
            Cache::forget('suppliers:all');
            Cache::forget('suppliers:options');

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'تم حذف المورد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء الحذف');
        }
    }

    /**
     * تقرير الديون
     */
    public function debtsReport()
    {
        $suppliers = Supplier::whereRaw('total_debt > total_paid')
            ->orderByRaw('(total_debt - total_paid) DESC')
            ->get()
            ->map(function ($supplier) {
                $supplier->remaining = $supplier->total_debt - $supplier->total_paid;
                return $supplier;
            });

        $totalDebt = $suppliers->sum('total_debt');
        $totalPaid = $suppliers->sum('total_paid');
        $totalRemaining = $suppliers->sum('remaining');

        return view('suppliers.debts-report', compact('suppliers', 'totalDebt', 'totalPaid', 'totalRemaining'));
    }
}