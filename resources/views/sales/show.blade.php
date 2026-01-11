@extends('layouts.app')

@section('title', 'فاتورة #' . $sale->id)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            فاتورة #{{ $sale->id }}
        </h1>

        <a href="{{ route('sales.index') }}" class="text-sm text-gray-600 hover:text-gray-800">
            ← رجوع لقائمة المبيعات
        </a>
    </div>

    {{-- معلومات عامة --}}
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="text-sm text-gray-500">التاريخ</div>
                <div class="font-medium">
                    {{ optional($sale->date)->format('Y-m-d H:i') }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">الكاشير</div>
                <div class="font-medium">
                    {{ $sale->cashier->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">طريقة الدفع</div>
                <div class="font-medium">
                    {{ $sale->payment_method ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">العميل</div>
                <div class="font-medium">
                    {{ $sale->customer->name ?? 'عميل نقدي' }}
                </div>
            </div>
        </div>
    </div>

    {{-- المنتجات --}}
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <h2 class="text-lg font-semibold mb-4">المنتجات المباعة</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-right text-gray-600">اسم المنتج</th>
                        <th class="px-4 py-2 text-right text-gray-600">الباركود</th>
                        <th class="px-4 py-2 text-right text-gray-600">المقاس</th>
                        <th class="px-4 py-2 text-right text-gray-600">اللون</th>
                        <th class="px-4 py-2 text-right text-gray-600">الكمية</th>
                        <th class="px-4 py-2 text-right text-gray-600">السعر</th>
                        <th class="px-4 py-2 text-right text-gray-600">الإجمالي الفرعي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                        @php
                            $pv = $item->productVariant;
                        @endphp
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                {{ $pv->name ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $pv->sku ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $pv->size ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $pv->color ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $item->qty }}
                            </td>
                            <td class="px-4 py-2">
                                {{ number_format($item->price, 2) }} ج.م
                            </td>
                            <td class="px-4 py-2">
                                {{ number_format($item->qty * $item->price, 2) }} ج.م
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- الملخص المالي --}}
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-4">الملخص المالي</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="text-sm text-gray-500">المجموع الفرعي</div>
                <div class="font-medium">
                    {{ number_format($sale->subtotal ?? 0, 2) }} ج.م
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">الخصم</div>
                <div class="font-medium">
                    {{ number_format($sale->discount ?? 0, 2) }} ج.م
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">الضريبة</div>
                <div class="font-medium">
                    {{ number_format($sale->tax ?? 0, 2) }} ج.م
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">الإجمالي النهائي</div>
                <div class="font-bold text-lg">
                    {{ number_format($sale->total ?? 0, 2) }} ج.م
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">المدفوع</div>
                <div class="font-medium">
                    {{ number_format($sale->paid ?? 0, 2) }} ج.م
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">المتبقي</div>
                <div class="font-bold">
                    {{ number_format($sale->remaining ?? 0, 2) }} ج.م
                </div>
            </div>
        </div>

        @if(!empty($sale->notes))
            <div class="mt-4">
                <div class="text-sm text-gray-500 mb-1">ملاحظات</div>
                <div class="border border-gray-200 rounded-lg p-3 text-sm text-gray-700">
                    {{ $sale->notes }}
                </div>
            </div>
        @endif
    </div>
@endsection