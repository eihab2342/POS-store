@extends('layouts.app')

@section('title', 'فاتورة #' . $sale->id)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            تفاصيل الفاتورة #{{ $sale->id }}
        </h1>

        <a href="{{ route('profits.index') }}" class="text-sm text-gray-600 hover:text-gray-800">
            ← رجوع لتقرير الأرباح
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold mb-4">بيانات أساسية</h2>

            <div class="space-y-2 text-sm">
                <div>
                    <span class="text-gray-500">رقم الفاتورة:</span>
                    <span class="font-semibold">#{{ $sale->id }}</span>
                </div>
                <div>
                    <span class="text-gray-500">التاريخ:</span>
                    <span>{{ optional($sale->date)->format('d/m/Y h:i A') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">العميل:</span>
                    <span>{{ $sale->customer->phone ?? 'غير محدد' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">الكاشير:</span>
                    <span>{{ $sale->cashier->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold mb-4">الملخص المالي</h2>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">الإجمالي:</span>
                    <span class="font-semibold">{{ number_format($sale->total, 2) }} ج.م</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">الربح:</span>
                    <span class="font-semibold text-green-700">{{ number_format($sale->profit ?? 0, 2) }} ج.م</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">الخصم:</span>
                    <span>{{ number_format($sale->discount ?? 0, 2) }} ج.م</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">المدفوع:</span>
                    <span>{{ number_format($sale->paid ?? 0, 2) }} ج.م</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">المتبقي:</span>
                    <span>{{ number_format($sale->remaining ?? 0, 2) }} ج.م</span>
                </div>
            </div>
        </div>
    </div>

    {{-- تفاصيل الأصناف --}}
    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="font-semibold mb-4">المنتجات في الفاتورة</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-right text-gray-600">المنتج</th>
                        <th class="px-4 py-2 text-right text-gray-600">الباركود</th>
                        <th class="px-4 py-2 text-right text-gray-600">اللون</th>
                        <th class="px-4 py-2 text-right text-gray-600">المقاس</th>
                        <th class="px-4 py-2 text-right text-gray-600">الكمية</th>
                        <th class="px-4 py-2 text-right text-gray-600">سعر البيع</th>
                        <th class="px-4 py-2 text-right text-gray-600">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sale->items as $item)
                        @php $pv = $item->productVariant; @endphp
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $pv->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $pv->sku ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $pv->color ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $pv->size ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $item->qty }}</td>
                            <td class="px-4 py-2">{{ number_format($item->price, 2) }} ج.م</td>
                            <td class="px-4 py-2 font-semibold">
                                {{ number_format($item->qty * $item->price, 2) }} ج.م
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                                لا توجد أصناف مسجلة لهذه الفاتورة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection