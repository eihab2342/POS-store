<?php 


namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    // عرض صفحة المصروفات الرئيسية
    public function index(Request $request)
    {
        $query = Expense::with(['supplier', 'employee', 'approver'])
            ->latest();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        // التصفية حسب النوع
        if ($request->filled('type')) {
            $query->where('expense_type', $request->type);
        }

        // التصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // التصفية حسب التاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->paginate(20);

        $totalExpenses = Expense::sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', today())->sum('amount');
        $pendingExpenses = Expense::pending()->sum('amount');

        return view('expenses.index', compact(
            'expenses',
            'totalExpenses',
            'todayExpenses',
            'pendingExpenses'
        ));
    }

    // عرض نموذج إنشاء مصروف جديد
    public function create()
    {
        $suppliers = Supplier::all();
        $employees = User::where('role', '!=', 'customer')->get();
        $managers = User::where('role', 'manager')->get();

        return view('expenses.create', compact('suppliers', 'employees', 'managers'));
    }

    // حفظ المصروف الجديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'reference_number' => 'nullable|string|max:100',
            'expense_type' => 'required|in:operational,administrative,marketing,maintenance,utilities,salary,purchase,other',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'employee_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf',
        ]);

        // رفع الملف إذا وجد
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('expenses', 'public');
            $validated['attachment'] = $path;
        }

        $validated['status'] = 'pending';
        $validated['created_by'] = auth('')->id();

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'تم إضافة المصروف بنجاح وتم إرساله للموافقة.');
    }

    // عرض تفاصيل مصروف
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    // تحميل المرفق
    public function downloadAttachment(Expense $expense)
    {
        if (!$expense->attachment) {
            return redirect()->back()->with('error', 'لا يوجد مرفق لهذا المصروف.');
        }

        return response()->download(Storage::disk('public')->path($expense->attachment));
    }

    // الموافقة على المصروف
    public function approve(Request $request, Expense $expense)
    {
        if ($expense->status != 'pending') {
            return redirect()->back()->with('error', 'لا يمكن الموافقة على هذا المصروف.');
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth('')->id(),
        ]);

        return redirect()->back()->with('success', 'تم الموافقة على المصروف بنجاح.');
    }

    // رفض المصروف
    public function reject(Request $request, Expense $expense)
    {
        if ($expense->status != 'pending') {
            return redirect()->back()->with('error', 'لا يمكن رفض هذا المصروف.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $expense->update([
            'status' => 'rejected',
            'notes' => $expense->notes . "\nسبب الرفض: " . $request->rejection_reason,
            'approved_by' => auth('')->id(),
        ]);

        return redirect()->back()->with('success', 'تم رفض المصروف بنجاح.');
    }

    // تعيين كمصروف مدفوع
    public function markAsPaid(Expense $expense)
    {
        if ($expense->status != 'approved') {
            return redirect()->back()->with('error', 'يجب الموافقة على المصروف أولاً قبل دفعه.');
        }

        $expense->update([
            'status' => 'paid',
        ]);

        return redirect()->back()->with('success', 'تم تسجيل المصروف كمصروف مدفوع.');
    }

    // تقرير المصروفات
    public function report(Request $request)
    {
        $query = Expense::with(['supplier', 'employee', 'approver'])
            ->where('status', 'paid'); // فقط المصروفات المدفوعة

        if ($request->filled('year')) {
            $query->whereYear('expense_date', $request->year);
        } else {
            $query->whereYear('expense_date', now()->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('expense_date', $request->month);
        }

        if ($request->filled('type')) {
            $query->where('expense_type', $request->type);
        }

        $expenses = $query->get();

        $summary = [];
        foreach ($expenses as $expense) {
            if (!isset($summary[$expense->expense_type])) {
                $summary[$expense->expense_type] = 0;
            }
            $summary[$expense->expense_type] += $expense->amount;
        }

        $years = Expense::selectRaw('YEAR(expense_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('expenses.report', compact('expenses', 'summary', 'years'));
    }

    // عرض إحصائيات سريعة في Dashboard
    public function dashboardStats()
    {
        $todayExpenses = Expense::whereDate('expense_date', today())
            ->where('status', 'paid')
            ->sum('amount');

        $monthExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->where('status', 'paid')
            ->sum('amount');

        $pendingCount = Expense::pending()->count();
        $pendingAmount = Expense::pending()->sum('amount');

        // إحصائيات حسب النوع لهذا الشهر
        $monthlyByType = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->where('status', 'paid')
            ->selectRaw('expense_type, SUM(amount) as total')
            ->groupBy('expense_type')
            ->get()
            ->pluck('total', 'expense_type');

        return [
            'today_expenses' => $todayExpenses,
            'month_expenses' => $monthExpenses,
            'pending_count' => $pendingCount,
            'pending_amount' => $pendingAmount,
            'monthly_by_type' => $monthlyByType
        ];
    }
}