@extends('layouts.app')

@section('title', 'تقرير الأرباح')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تقرير الأرباح</h1>

        <a href="{{ route('profit.print-report', request()->query()) }}" target="_blank"
            class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 9V2h12v7M6 18h12v4H6v-4zM6 14h.01M18 14h.01M4 9h16v5H4z" />
            </svg>
            <span>طباعة تقرير الأرباح</span>
        </a>
    </div>

    {{-- فلاتر --}}
    <form method="GET" action="{{ route('profits.index') }}"
        class="mb-6 bg-white p-4 rounded-lg shadow flex flex-wrap gap-4">

        <div>
            <label class="block text-sm text-gray-600 mb-1">بحث</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                class="border-gray-300 rounded-lg w-48 text-sm" placeholder="رقم فاتورة / اسم عميل">
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">فترات جاهزة</label>
            <select name="date_filter" class="border-gray-300 rounded-lg text-sm">
                <option value="">الكل</option>
                <option value="today" @selected(($filters['date_filter'] ?? '') === 'today')>اليوم</option>
                <option value="this_week" @selected(($filters['date_filter'] ?? '') === 'this_week')>هذا الأسبوع</option>
                <option value="this_month" @selected(($filters['date_filter'] ?? '') === 'this_month')>هذا الشهر</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">تاريخ محدد</label>
            <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="border-gray-300 rounded-lg text-sm">
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">من تاريخ</label>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="border-gray-300 rounded-lg text-sm">
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">إلى تاريخ</label>
            <input type="date" name="until" value="{{ $filters['until'] ?? '' }}"
                class="border-gray-300 rounded-lg text-sm">
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                تطبيق
            </button>
            <a href="{{ route('profits.index') }}"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                تصفير
            </a>
        </div>
    </form>

    {{-- ملخص أعلى --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500 mb-1">إجمالي المبيعات</div>
            <div class="text-2xl font-bold text-blue-700">
                {{ number_format($totalRevenue, 2) }} ج.م
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500 mb-1">إجمالي الربح</div>
            <div class="text-2xl font-bold text-green-700">
                {{ number_format($totalProfit, 2) }} ج.م
            </div>
        </div>
    </div>

    {{-- جدول الفواتير --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-right text-gray-600">رقم الفاتورة</th>
                    <th class="px-4 py-2 text-right text-gray-600">التاريخ</th>
                    <th class="px-4 py-2 text-right text-gray-600">العميل</th>
                    <th class="px-4 py-2 text-right text-gray-600">الإجمالي</th>
                    <th class="px-4 py-2 text-right text-gray-600">الربح</th>
                    <th class="px-4 py-2 text-right text-gray-600">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="border-t">
                        <td class="px-4 py-2">
                            <a href="{{ route('profits.show', $sale) }}" class="text-indigo-600 hover:underline font-medium">
                                #{{ $sale->id }}
                            </a>
                        </td>
                        <td class="px-4 py-2">
                            {{ optional($sale->date)->format('d/m/Y h:i A') }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $sale->customer->name ?? 'غير محدد' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ number_format($sale->total, 2) }} ج.م
                        </td>
                        <td class="px-4 py-2 font-semibold text-green-700">
                            {{ number_format($sale->profit ?? 0, 2) }} ج.م
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('profits.show', $sale) }}" class="text-xs text-indigo-600 hover:underline">
                                عرض التفاصيل
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            لا توجد فواتير مطابقة للفلاتر الحالية.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sales->links() }}
    </div>
@endsection