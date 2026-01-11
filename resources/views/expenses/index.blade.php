@extends('layouts.app')

@section('title', 'إدارة المصروفات')

@section('content')
<style>
:root {
    --primary: #3b82f6;
    --primary-dark: #2563eb;
    --primary-light: #dbeafe;
    --secondary: #10b981;
    --secondary-dark: #059669;
    --secondary-light: #d1fae5;
    --danger: #ef4444;
    --danger-dark: #dc2626;
    --danger-light: #fee2e2;
    --warning: #f59e0b;
    --warning-dark: #d97706;
    --warning-light: #fef3c7;
    --info: #8b5cf6;
    --info-dark: #7c3aed;
    --info-light: #ede9fe;
    --success: #22c55e;
    --success-dark: #16a34a;
    --success-light: #dcfce7;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
}

.expense-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
}

.expense-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-pending {
    background: var(--warning-light);
    color: var(--warning-dark);
}

.status-approved {
    background: var(--success-light);
    color: var(--success-dark);
}

.status-rejected {
    background: var(--danger-light);
    color: var(--danger-dark);
}

.status-paid {
    background: var(--primary-light);
    color: var(--primary-dark);
}

.type-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
    background: var(--gray-100);
    color: var(--gray-600);
}

.action-btn {
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-view {
    background: var(--gray-500);
    color: white;
}

.btn-approve {
    background: var(--success);
    color: white;
}

.btn-reject {
    background: var(--danger);
    color: white;
}

.btn-paid {
    background: var(--info);
    color: white;
}

.action-btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.stats-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid var(--gray-200);
}

.stats-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stats-icon-today {
    background: var(--primary-light);
    color: var(--primary);
}

.stats-icon-month {
    background: var(--secondary-light);
    color: var(--secondary);
}

.stats-icon-pending {
    background: var(--warning-light);
    color: var(--warning);
}

.stats-icon-total {
    background: var(--info-light);
    color: var(--info);
}

.bg-green-600 {
    background: var(--success);
}

.hover\:bg-green-700:hover {
    background: var(--success-dark);
}

.bg-purple-600 {
    background: var(--info);
}

.hover\:bg-purple-700:hover {
    background: var(--info-dark);
}

.bg-gray-200 {
    background: var(--gray-200);
}

.hover\:bg-gray-300:hover {
    background: var(--gray-300);
}

.text-gray-800 {
    color: var(--gray-800);
}

.text-gray-600 {
    color: var(--gray-600);
}

.text-gray-500 {
    color: var(--gray-500);
}

.text-gray-700 {
    color: var(--gray-700);
}

.text-gray-900 {
    color: var(--gray-900);
}

.text-white {
    color: white;
}

.text-purple-600 {
    color: var(--info);
}

.hover\:text-purple-800:hover {
    color: var(--info-dark);
}

.border-gray-100 {
    border-color: var(--gray-100);
}

.border-gray-200 {
    border-color: var(--gray-200);
}

.border-gray-300 {
    border-color: var(--gray-300);
}

.bg-gray-50 {
    background: var(--gray-50);
}

.hover\:bg-gray-50:hover {
    background: var(--gray-50);
}

.bg-white {
    background: white;
}

.focus\:ring-purple-500:focus {
    --tw-ring-color: var(--info);
}

.rounded-lg {
    border-radius: 0.5rem;
}

.rounded-xl {
    border-radius: 0.75rem;
}

.font-medium {
    font-weight: 500;
}

.font-semibold {
    font-weight: 600;
}

.font-bold {
    font-weight: 700;
}

.text-3xl {
    font-size: 1.875rem;
    line-height: 2.25rem;
}

.text-2xl {
    font-size: 1.5rem;
    line-height: 2rem;
}

.text-lg {
    font-size: 1.125rem;
    line-height: 1.75rem;
}

.text-sm {
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.text-xs {
    font-size: 0.75rem;
    line-height: 1rem;
}

.text-4xl {
    font-size: 2.25rem;
    line-height: 2.5rem;
}

.uppercase {
    text-transform: uppercase;
}

.tracking-wider {
    letter-spacing: 0.05em;
}

.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
}

.py-2 {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}

.px-6 {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.py-3 {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}

.px-8 {
    padding-left: 2rem;
    padding-right: 2rem;
}

.py-4 {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.p-6 {
    padding: 1.5rem;
}

.py-8 {
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.mb-1 {
    margin-bottom: 0.25rem;
}

.mb-2 {
    margin-bottom: 0.5rem;
}

.mb-3 {
    margin-bottom: 0.75rem;
}

.mb-4 {
    margin-bottom: 1rem;
}

.mb-6 {
    margin-bottom: 1.5rem;
}

.mb-8 {
    margin-bottom: 2rem;
}

.mt-1 {
    margin-top: 0.25rem;
}

.mt-2 {
    margin-top: 0.5rem;
}

.mt-3 {
    margin-top: 0.75rem;
}

.mt-4 {
    margin-top: 1rem;
}

.mr-1 {
    margin-right: 0.25rem;
}

.mr-2 {
    margin-right: 0.5rem;
}

.ml-1 {
    margin-left: 0.25rem;
}

.ml-2 {
    margin-left: 0.5rem;
}

.gap-2 {
    gap: 0.5rem;
}

.gap-3 {
    gap: 0.75rem;
}

.gap-4 {
    gap: 1rem;
}

.gap-6 {
    gap: 1.5rem;
}

.w-full {
    width: 100%;
}

.hover\:bg-gray-100:hover {
    background: var(--gray-100);
}

.bg-gray-100 {
    background: var(--gray-100);
}

.outline-none {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

.focus\:outline-none:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

.focus\:ring-2:focus {
    --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
    box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}

.grid {
    display: grid;
}

.grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

.md\:grid-cols-2 {
    @media (min-width: 768px) {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.md\:grid-cols-4 {
    @media (min-width: 768px) {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

.lg\:grid-cols-4 {
    @media (min-width: 1024px) {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

.flex {
    display: flex;
}

.items-center {
    align-items: center;
}

.justify-center {
    justify-content: center;
}

.justify-between {
    justify-content: space-between;
}

.justify-end {
    justify-content: flex-end;
}

.flex-wrap {
    flex-wrap: wrap;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.inline-flex {
    display: inline-flex;
}

.relative {
    position: relative;
}

.overflow-hidden {
    overflow: hidden;
}

.overflow-x-auto {
    overflow-x: auto;
}

.whitespace-nowrap {
    white-space: nowrap;
}

.divide-y > :not([hidden]) ~ :not([hidden]) {
    --tw-divide-y-reverse: 0;
    border-top-width: calc(1px * calc(1 - var(--tw-divide-y-reverse)));
    border-bottom-width: calc(1px * var(--tw-divide-y-reverse));
}

.divide-gray-200 > :not([hidden]) ~ :not([hidden]) {
    --tw-divide-opacity: 1;
    border-color: rgb(229 231 235 / var(--tw-divide-opacity));
}

.border-t {
    border-top-width: 1px;
}

.inline {
    display: inline;
}

.inline-block {
    display: inline-block;
}

.block {
    display: block;
}

.container {
    width: 100%;
    margin-right: auto;
    margin-left: auto;
    padding-right: 1rem;
    padding-left: 1rem;
}

@media (min-width: 640px) {
    .container {
        max-width: 640px;
    }
}

@media (min-width: 768px) {
    .container {
        max-width: 768px;
    }
}

@media (min-width: 1024px) {
    .container {
        max-width: 1024px;
    }
}

@media (min-width: 1280px) {
    .container {
        max-width: 1280px;
    }
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.duration-200 {
    transition-duration: 200ms;
}

.duration-300 {
    transition-duration: 300ms;
}

.opacity-90 {
    opacity: 0.9;
}
</style>

<div class="container mx-auto px-4 py-8">
    <!-- العنوان والإحصائيات -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold" style="color: var(--gray-800);">إدارة المصروفات</h1>
                <p class="mt-2" style="color: var(--gray-600);">سجل وتتبع جميع مصروفات المنشأة</p>
            </div>
            <a href="{{ route('expenses.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                <span>إضافة مصروف جديد</span>
            </a>
        </div>

        <!-- بطاقات الإحصائيات -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stats-card">
                <div class="stats-icon stats-icon-today">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2" style="color: var(--gray-800);">{{ number_format($todayExpenses, 2) }} ج.م</h3>
                <p style="color: var(--gray-600);">مصروفات اليوم</p>
            </div>

            <div class="stats-card">
                <div class="stats-icon stats-icon-month">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2" style="color: var(--gray-800);">{{ number_format($monthExpenses ?? 0, 2) }} ج.م</h3>
                <p style="color: var(--gray-600);">مصروفات الشهر</p>
            </div>

            <div class="stats-card">
                <div class="stats-icon stats-icon-pending">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2" style="color: var(--gray-800);">{{ number_format($pendingExpenses, 2) }} ج.م</h3>
                <p style="color: var(--gray-600);">قيد الانتظار</p>
            </div>

            <div class="stats-card">
                <div class="stats-icon stats-icon-total">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2" style="color: var(--gray-800);">{{ number_format($totalExpenses, 2) }} ج.م</h3>
                <p style="color: var(--gray-600);">إجمالي المصروفات</p>
            </div>
        </div>
    </div>

    <!-- فلترة البحث -->
    <div class="expense-card p-6 mb-6">
        <form action="{{ route('expenses.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--gray-700);">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="رقم المصروف أو الوصف..." 
                       class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                       style="border-color: var(--gray-300);">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--gray-700);">نوع المصروف</label>
                <select name="type" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        style="border-color: var(--gray-300);">
                    <option value="">الكل</option>
                    @foreach(App\Models\Expense::getExpenseTypes() as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--gray-700);">الحالة</label>
                <select name="status" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        style="border-color: var(--gray-300);">
                    <option value="">الكل</option>
                    @foreach(App\Models\Expense::getStatuses() as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 flex-1">
                    <i class="fas fa-search ml-2"></i>
                    <span>بحث</span>
                </button>
                <a href="{{ route('expenses.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- جدول المصروفات -->
    <div class="expense-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: var(--gray-500);">رقم المصروف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: var(--gray-500);">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: var(--gray-500);">الوصف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: var(--gray-500);">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: var(--gray-500);">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: var(--gray-500);">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: var(--gray-500);">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium" style="color: var(--gray-900);">{{ $expense->expense_number }}</div>
                            <div class="text-sm" style="color: var(--gray-500);">{{ $expense->payment_method }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm" style="color: var(--gray-900);">{{ $expense->expense_date->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium" style="color: var(--gray-900);">{{ Str::limit($expense->description, 50) }}</div>
                            @if($expense->supplier)
                                <div class="text-sm" style="color: var(--gray-500);">المورد: {{ $expense->supplier->name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold" style="color: var(--gray-900);">{{ number_format($expense->amount, 2) }} ج.م</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="type-badge">{{ $expense->getExpenseTypeArabic() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'status-pending',
                                    'approved' => 'status-approved',
                                    'rejected' => 'status-rejected',
                                    'paid' => 'status-paid'
                                ];
                            @endphp
                            <span class="status-badge {{ $statusColors[$expense->status] }}">
                                {{ $expense->getStatusArabic() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <a href="{{ route('expenses.show', $expense) }}" 
                                   class="action-btn btn-view">
                                    <i class="fas fa-eye ml-1"></i>
                                    عرض
                                </a>
                                
                                @if(auth()->user()->role == 'manager')
                                    @if($expense->status == 'pending')
                                    <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="action-btn btn-approve">
                                            <i class="fas fa-check ml-1"></i>
                                            موافقة
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($expense->status == 'approved')
                                    <form action="{{ route('expenses.markPaid', $expense) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="action-btn btn-paid">
                                            <i class="fas fa-money-bill-wave ml-1"></i>
                                            دفع
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center">
                            <div style="color: var(--gray-500);">
                                <i class="fas fa-receipt text-4xl mb-3"></i>
                                <p class="text-lg">لا توجد مصروفات مسجلة</p>
                                <p class="text-sm mt-2">ابدأ بإضافة أول مصروف للمنشأة</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- الترقيم -->
        @if($expenses->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $expenses->withQueryString()->links() }}
        </div>
        @endif
    </div>

    <!-- رابط التقرير -->
    <div class="mt-6 text-center">
        {{-- <a href="{{ route('expenses.report') }}"
           class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-800 font-medium">
            <i class="fas fa-chart-bar"></i>
            <span>عرض تقرير المصروفات الشامل</span>
        </a> --}}
    </div>
</div>
@endsection