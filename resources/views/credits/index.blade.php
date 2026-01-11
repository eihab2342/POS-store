@extends('layouts.app')

@section('title', 'الأجلات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">الأجلات</h1>
</div>

{{-- فلاتر --}}
<form method="GET" action="{{ route('credits.index') }}"
      class="mb-6 bg-white p-4 rounded-lg shadow flex flex-wrap gap-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">بحث</label>
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
               class="border-gray-300 rounded-lg w-52 focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="رقم الأجل / الفاتورة / اسم العميل / الهاتف">
    </div>

    <div>
        <label class="block text-sm text-gray-600 mb-1">من تاريخ</label>
        <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}"
               class="border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div>
        <label class="block text-sm text-gray-600 mb-1">إلى تاريخ</label>
        <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}"
               class="border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="flex items-end gap-2">
        <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
            تطبيق
        </button>

        <a href="{{ route('credits.index') }}"
           class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
            تصفير
        </a>
    </div>
</form>

{{-- الجدول --}}
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-2 text-right">رقم الأجل</th>
            <th class="px-4 py-2 text-right">رقم الفاتورة</th>
            <th class="px-4 py-2 text-right">العميل</th>
            <th class="px-4 py-2 text-right">الهاتف</th>
            <th class="px-4 py-2 text-right">إجمالي</th>
            <th class="px-4 py-2 text-right">المدفوع</th>
            <th class="px-4 py-2 text-right">المتبقي</th>
            <th class="px-4 py-2 text-right">التاريخ</th>
            <th class="px-4 py-2 text-right">إجراءات</th>
        </tr>
        </thead>

        <tbody>
                    @php
            // dd($credits)
        @endphp

        @forelse($credits as $credit)
            <tr class="border-t hover:bg-gray-50 transition">
                <td class="px-4 py-2">#{{ $credit->id }}</td>

                <td class="px-4 py-2">
                    @if($credit->sale_id)
                        <a href="{{ route('sales.show', $credit->sale_id) }}"
                           class="text-indigo-600 hover:underline">
                            #{{ $credit->sale_id }}
                        </a>
                    @else -
                    @endif
                </td>

                <td class="px-4 py-2">{{ $credit->customer->name ?? '-' }}</td>
                <td class="px-4 py-2">{{ $credit->customer->phone ?? '-' }}</td>
                <td class="px-4 py-2">{{ number_format($credit->sale->total ?? 0,2) }}</td>
                <td class="px-4 py-2">{{ number_format($credit->sale->paid ?? 0,2) }}</td>

                <td class="px-4 py-2 text-red-600 font-semibold">
                    {{ number_format($credit->remaining,2) }}
                </td>

                <td class="px-4 py-2">
                    {{ optional($credit->date)->format('Y-m-d') ?? '-' }}
                </td>

                <td class="px-4 py-2 flex gap-3">
                    <button onclick="openPayModal({{ $credit->id }}, {{ $credit->remaining }})"
                            class="text-green-600 hover:text-green-800 transition text-sm font-medium">
                        💵 سداد
                    </button>

                    <a href="{{ route('credits.show', $credit) }}"
                       class="text-indigo-600 hover:underline text-sm">
                        تفاصيل
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                    لا توجد أجلات
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $credits->links() }}
</div>

{{-- ================= MODAL ================= --}}
<div id="payModal" class="fixed inset-0 z-50 hidden">
    <div id="payOverlay" onclick="closePayModal()"
         class="absolute inset-0 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>

    <div class="relative flex items-center justify-center min-h-screen px-4">
        <div id="payBox"
             class="bg-white w-full max-w-md rounded-2xl shadow-2xl
                    scale-95 opacity-0 transition-all duration-300">

            <div class="border-b px-5 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">تسجيل سداد</h3>
                <button onclick="closePayModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <form method="POST" id="payForm" class="px-5 py-4 space-y-4">
                @csrf

                <div>
                    <label class="text-sm text-gray-600 mb-1 block">المبلغ المسدد</label>
                    <div class="relative">
                        <input type="number" name="amount" id="payAmount"
                               step="0.01" min="0.01" required
                               class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 pr-10">
                        <span class="absolute left-3 top-2.5 text-gray-500 text-sm">ج.م</span>
                    </div>
                </div>

                <p class="text-sm text-gray-600">
                    <span>المتبقي الحالي:</span>
                    <span id="modalRemaining" class="font-semibold text-red-600"></span> ج.م
                </p>

                <p class="text-sm text-gray-700">
                    <span>المتبقي بعد السداد:</span>
                    <span id="remainingAfter" class="font-semibold text-green-600">-</span> ج.م
                </p>

                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="closePayModal()"
                            class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">
                        إلغاء
                    </button>

                    <button type="submit"
                            class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                        💾 تسجيل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentRemaining = 0;

    function openPayModal(id, remaining) {
        currentRemaining = remaining;
        const modal = document.getElementById('payModal');
        const overlay = document.getElementById('payOverlay');
        const box = document.getElementById('payBox');
        const form = document.getElementById('payForm');
        const amountInput = document.getElementById('payAmount');

        modal.classList.remove('hidden');
        form.action = `/credits/${id}/pay`;
        document.getElementById('modalRemaining').textContent = remaining.toFixed(2);
        document.getElementById('remainingAfter').textContent = remaining.toFixed(2);
        amountInput.value = '';

        setTimeout(() => {
            overlay.classList.add('opacity-100');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);

        amountInput.focus();
    }

    function closePayModal() {
        const modal = document.getElementById('payModal');
        const overlay = document.getElementById('payOverlay');
        const box = document.getElementById('payBox');

        overlay.classList.remove('opacity-100');
        box.classList.remove('scale-100', 'opacity-100');

        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    document.getElementById('payAmount').addEventListener('input', function () {
        const value = parseFloat(this.value) || 0;
        const remainingAfter = Math.max(currentRemaining - value, 0);
        document.getElementById('remainingAfter').textContent = remainingAfter.toFixed(2);
    });
</script>
@endsection
