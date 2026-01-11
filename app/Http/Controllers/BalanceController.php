<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Balance::with(['customer', 'invoice'])
            ->orderByDesc('payment_date');

        // 🔍 بحث برقم مرجعي أو اسم عميل
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($qq) => $qq->where('name', 'like', "%{$search}%"));
            });
        }

        // حالة الدفع
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // طرق الدفع (كاش / محفظة / InstaPay)
        $methods = $request->input('methods', []); // array
        if (!empty($methods) && is_array($methods)) {
            $query->where(function ($q) use ($methods) {
                if (in_array('cash', $methods)) {
                    $q->orWhere('cash_amount', '>', 0);
                }
                if (in_array('wallet', $methods)) {
                    $q->orWhere('wallet_amount', '>', 0);
                }
                if (in_array('instapay', $methods)) {
                    $q->orWhere('instapay_amount', '>', 0);
                }
            });
        }

        // فلتر من / إلى تاريخ
        $from = $request->input('from');
        $until = $request->input('until');

        if ($from) {
            $query->whereDate('payment_date', '>=', $from);
        }
        if ($until) {
            $query->whereDate('payment_date', '<=', $until);
        }

        // 🔢 إجماليات بناءً على نفس الفلاتر
        $totals = (clone $query)
            ->selectRaw('COALESCE(SUM(cash_amount), 0) as total_cash')
            ->selectRaw('COALESCE(SUM(wallet_amount), 0) as total_wallet')
            ->selectRaw('COALESCE(SUM(instapay_amount), 0) as total_instapay')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_all')
            ->first();

        $balances = $query->paginate(25)->withQueryString();

        return view('balances.index', [
            'balances' => $balances,
            'totals' => $totals,
            'filters' => [
                'search' => $search ?? '',
                'status' => $status ?? '',
                'methods' => $methods,
                'from' => $from,
                'until' => $until,
            ],
        ]);
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $invoices = Sale::orderByDesc('id')->get();

        return view('balances.create', compact('customers', 'invoices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'sale_id' => ['nullable', 'exists:sales,id'],
            'payment_date' => ['required', 'date'],
            'cash_amount' => ['nullable', 'numeric', 'min:0'],
            'wallet_amount' => ['nullable', 'numeric', 'min:0'],
            'instapay_amount' => ['nullable', 'numeric', 'min:0'],
            'wallet_phone' => ['nullable', 'string', 'max:50'],
            'instapay_reference' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:pending,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['cash_amount'] = $data['cash_amount'] ?? 0;
        $data['wallet_amount'] = $data['wallet_amount'] ?? 0;
        $data['instapay_amount'] = $data['instapay_amount'] ?? 0;
        $data['total_amount'] = $data['cash_amount'] + $data['wallet_amount'] + $data['instapay_amount'];

        // لو مفيش reference_number وده حقل عندك:
        if (empty($data['reference_number'] ?? null)) {
            $data['reference_number'] = 'BAL-' . now()->format('YmdHis') . '-' . rand(100, 999);
        }

        Balance::create($data);

        return redirect()
            ->route('balances.index')
            ->with('success', 'تم تسجيل حركة الرصيد بنجاح');
    }

    public function show(Balance $balance)
    {
        $balance->load(['customer', 'invoice']);
        //  dd($balance);

        return view('balances.show', compact('balance'));
    }

    public function edit(Balance $balance)
    {
        $customers = Customer::orderBy('name')->get();
        $invoices = Sale::orderByDesc('id')->get();

        return view('balances.edit', compact('balance', 'customers', 'invoices'));
    }

    public function update(Request $request, Balance $balance)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'sale_id' => ['nullable', 'exists:sales,id'],
            'payment_date' => ['required', 'date'],
            'cash_amount' => ['nullable', 'numeric', 'min:0'],
            'wallet_amount' => ['nullable', 'numeric', 'min:0'],
            'instapay_amount' => ['nullable', 'numeric', 'min:0'],
            'wallet_phone' => ['nullable', 'string', 'max:50'],
            'instapay_reference' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:pending,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['cash_amount'] = $data['cash_amount'] ?? 0;
        $data['wallet_amount'] = $data['wallet_amount'] ?? 0;
        $data['instapay_amount'] = $data['instapay_amount'] ?? 0;
        $data['total_amount'] = $data['cash_amount'] + $data['wallet_amount'] + $data['instapay_amount'];

        $balance->update($data);

        return redirect()
            ->route('balances.index')
            ->with('success', 'تم تحديث حركة الرصيد بنجاح');
    }

    public function destroy(Balance $balance)
    {
        $balance->delete();

        return redirect()
            ->route('balances.index')
            ->with('success', 'تم حذف حركة الرصيد');
    }
}
