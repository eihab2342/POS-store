@extends('layouts.app')

@section('title', 'الرصيد والمدفوعات')

@section('content')
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">الرصيد والمدفوعات</h1>

            <a href="{{ route('balances.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                + إضافة حركة رصيد
            </a>
        </div>

        {{-- فلاتر --}}
        <form method="GET" action="{{ route('balances.index') }}"
            class="mb-6 bg-white p-4 rounded-lg shadow flex flex-wrap gap-4">

            <div>
                <label class="block text-sm text-gray-600 mb-1">بحث</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="border-gray-300 rounded-lg w-48"
                    placeholder="رقم مرجعي / اسم عميل">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">الحالة</label>
                <select name="status" class="border-gray-300 rounded-lg">
                    <option value="">الكل</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>معلق</option>
                    <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>مكتمل</option>
                    <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>ملغي</option>
                </select>
            </div>

            <div>
                <span class="block text-sm text-gray-600 mb-1">طرق الدفع</span>
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-1 text-sm">
                        <input type="checkbox" name="methods[]" value="cash" @checked(in_array('cash', $filters['methods'] ?? []))>
                        <span>كاش</span>
                    </label>
                    <label class="flex items-center gap-1 text-sm">
                        <input type="checkbox" name="methods[]" value="wallet" @checked(in_array('wallet', $filters['methods'] ?? []))>
                        <span>محفظة</span>
                    </label>
                    <label class="flex items-center gap-1 text-sm">
                        <input type="checkbox" name="methods[]" value="instapay" @checked(in_array('instapay', $filters['methods'] ?? []))>
                        <span>InstaPay</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">من تاريخ</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">إلى تاريخ</label>
                <input type="date" name="until" value="{{ $filters['until'] ?? '' }}" class="border-gray-300 rounded-lg">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                    تطبيق الفلاتر
                </button>
                <a href="{{ route('balances.index') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                    تصفير
                </a>
            </div>
        </form>
    {{-- ملخص الرصيد --}}
    @if(isset($totals))
        <div class="mb-6 grid gap-4 md:grid-cols-4">
            <div class="bg-white shadow rounded-lg p-4 border-l-4 border-green-500">
                <div class="text-xs text-gray-500 mb-1">إجمالي الكاش</div>
                <div class="text-lg font-bold text-gray-800">
                    {{ number_format($totals->total_cash, 2) }} ج.م
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-4 border-l-4 border-yellow-500">
                <div class="text-xs text-gray-500 mb-1">إجمالي المحفظة</div>
                <div class="text-lg font-bold text-gray-800">
                    {{ number_format($totals->total_wallet, 2) }} ج.م
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-4 border-l-4 border-blue-500">
                <div class="text-xs text-gray-500 mb-1">إجمالي InstaPay</div>
                <div class="text-lg font-bold text-gray-800">
                    {{ number_format($totals->total_instapay, 2) }} ج.م
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-4 border-l-4 border-indigo-500">
                <div class="text-xs text-gray-500 mb-1">إجمالي كل المدفوعات</div>
                <div class="text-lg font-bold text-gray-800">
                    {{ number_format($totals->total_all, 2) }} ج.م
                </div>
            </div>
        </div>
    @endif

        {{-- جدول --}}
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-right text-gray-600">رقم مرجعي</th>
                        <th class="px-4 py-2 text-right text-gray-600">العميل</th>
                        <th class="px-4 py-2 text-right text-gray-600">الفاتورة</th>
                        <th class="px-4 py-2 text-right text-gray-600">كاش</th>
                        <th class="px-4 py-2 text-right text-gray-600">محفظة</th>
                        <th class="px-4 py-2 text-right text-gray-600">InstaPay</th>
                        <th class="px-4 py-2 text-right text-gray-600">الإجمالي</th>
                        <th class="px-4 py-2 text-right text-gray-600">الحالة</th>
                        <th class="px-4 py-2 text-right text-gray-600">تاريخ الدفع</th>
                        <th class="px-4 py-2 text-right text-gray-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($balances as $balance)
                        <tr class="border-t">
                            <td class="px-4 py-2 font-medium">
                                <a href="{{ route('balances.show', $balance) }}" class="text-indigo-600 hover:underline">
                                    {{ $balance->reference_number }}
                                </a>
                            </td>
                            <td class="px-4 py-2">
                                {{ $balance->customer->name ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $balance->invoice->invoice_number ?? $balance->sale_id ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ number_format($balance->cash_amount, 2) }} ج.م
                            </td>
                            <td class="px-4 py-2">
                                {{ number_format($balance->wallet_amount, 2) }} ج.م
                            </td>
                            <td class="px-4 py-2">
                                {{ number_format($balance->instapay_amount, 2) }} ج.م
                            </td>
                            <td class="px-4 py-2 font-bold">
                                {{ number_format($balance->total_amount, 2) }} ج.م
                            </td>
                            <td class="px-4 py-2">
                                @php
        $status = $balance->status;
        $label = match ($status) {
            'pending' => 'معلق',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $status,
        };
        $color = match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs {{ $color }}">{{ $label }}</span>
                            </td>
                            <td class="px-4 py-2">
                                {{ optional($balance->payment_date)->format('d/m/Y h:i A') }}
                            </td>
                            <td class="px-4 py-2 space-x-2 space-x-reverse">
                                <a href="{{ route('balances.show', $balance) }}"
                                    class="text-indigo-600 text-xs hover:underline">عرض</a>
                                <a href="{{ route('balances.edit', $balance) }}"
                                    class="text-blue-600 text-xs hover:underline">تعديل</a>
                                <form action="{{ route('balances.destroy', $balance) }}" method="POST" class="inline"
                                    onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 text-xs hover:underline">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-6 text-center text-gray-500">
                                لا توجد حركات رصيد حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $balances->links() }}
        </div>
@endsection