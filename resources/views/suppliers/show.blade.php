@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('suppliers.index') }}"
                       class="text-gray-600 hover:text-gray-900 transition duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $supplier->name }}</h1>
                        <p class="mt-1 text-sm text-gray-600">كود: {{ $supplier->code }}</p>
                    </div>
                    @if($supplier->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">نشط</span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">غير نشط</span>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('suppliers.edit', $supplier) }}"
                       class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition duration-150">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-r-4 border-green-500 p-4 rounded-lg">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <!-- الإحصائيات -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">إجمالي المشتريات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_purchases'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">جنيه مصري</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">إجمالي المدفوعات</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_paid'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">جنيه مصري</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">الرصيد المتبقي</p>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($supplier->current_balance, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">جنيه مصري</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">عدد الفواتير</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['purchases_count'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">فاتورة</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات المورد -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات المورد</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">الشركة</p>
                    <p class="text-sm font-medium text-gray-900">{{ $supplier->company_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">الهاتف</p>
                    <p class="text-sm font-medium text-gray-900" dir="ltr">{{ $supplier->phone }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">البريد الإلكتروني</p>
                    <p class="text-sm font-medium text-gray-900" dir="ltr">{{ $supplier->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">الرقم الضريبي</p>
                    <p class="text-sm font-medium text-gray-900">{{ $supplier->tax_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">المدينة</p>
                    <p class="text-sm font-medium text-gray-900">{{ $supplier->city ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">الدولة</p>
                    <p class="text-sm font-medium text-gray-900">{{ $supplier->country ?? '-' }}</p>
                </div>
                @if($supplier->address)
                <div class="md:col-span-3">
                    <p class="text-sm text-gray-600 mb-1">العنوان</p>
                    <p class="text-sm font-medium text-gray-900">{{ $supplier->address }}</p>
                </div>
                @endif
                @if($supplier->notes)
                <div class="md:col-span-3">
                    <p class="text-sm text-gray-600 mb-1">ملاحظات</p>
                    <p class="text-sm text-gray-700">{{ $supplier->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="border-b border-green">
                <nav class="-mb-px flex gap-8">
                    <button onclick="switchTab('purchases')"
                            id="tab-purchases"
                            class="tab-button active py-4 px-1 border-b-2 font-medium text-sm transition duration-150">
                        فواتير الشراء ({{ $stats['purchases_count'] }})
                    </button>
                    <button onclick="switchTab('payments')"
                            id="tab-payments"
                            class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition duration-150">
                        المدفوعات ({{ $stats['payments_count'] }})
                    </button>
                </nav>
            </div>
        </div>

        <!-- فواتير الشراء -->
        <div id="content-purchases" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">فواتير الشراء</h3>
                    <a href="{{ route('purchases.create', $supplier) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة فاتورة جديدة
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الفاتورة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ الكلي</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المدفوع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المتبقي</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($purchases as $purchase)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $purchase->invoice_no }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $purchase->date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($purchase->total_cost, 2) }} ج.م</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ number_format($purchase->paid_amount, 2) }} ج.م</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($purchase->remaining_amount, 2) }} ج.م</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($purchase->payment_status == 'paid')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">مسددة</span>
                                        @elseif($purchase->payment_status == 'partial')
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">جزئي</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">غير مسددة</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('purchases.edit', $purchase) }}" class="text-yellow-600 hover:text-yellow-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">لا توجد فواتير</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($purchases->hasPages())
                    <div class="px-6 py-4 border-t">{{ $purchases->links() }}</div>
                @endif
            </div>
        </div>

        <!-- المدفوعات -->
        <div id="content-payments" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">المدفوعات</h3>
                    <a href="{{ route('payments.create', $supplier) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-150">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        تسجيل دفعة جديدة
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطريقة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الفاتورة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المرجع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الملاحظات</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">{{ number_format($payment->amount, 2) }} ج.م</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $payment->method_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $payment->purchase?->invoice_no ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $payment->reference ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->note ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('payments.edit', $payment) }}" class="text-yellow-600 hover:text-yellow-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">لا توجد مدفوعات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($payments->hasPages())
                    <div class="px-6 py-4 border-t">{{ $payments->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</div>

<script>
function switchTab(tab) {
    // إخفاء كل المحتوى
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // إزالة الـ active من كل الأزرار
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue', 'text-blue');
        button.classList.add('border-transparent', 'text-blue', 'hover:text-blue', 'hover:border-blue');
    });

    // إظهار المحتوى المطلوب
    document.getElementById('content-' + tab).classList.remove('hidden');

    // تفعيل الزر المطلوب
    const activeButton = document.getElementById('tab-' + tab);
    activeButton.classList.add('active', 'border-blue', 'text-blue');
    activeButton.classList.remove('border-transparent', 'text-green', 'hover:text-green', 'hover:border-blue');
}
</script>

<style>
.tab-button {
    @apply border-transparent text-green hover:text-green hover:border-green;
}
.tab-button.active {
    @apply border-blue-500 text-blue;
}
</style>
@endsection