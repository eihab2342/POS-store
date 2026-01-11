@extends('layouts.app')

@section('title', 'تفاصيل الأجل #' . $credit->id)

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            الأجل رقم #{{ $credit->id }}
        </h1>
        <p class="text-gray-500 mt-1">
            فاتورة رقم:
            @if($credit->sale_id)
                <a href="{{ route('sales.show', $credit->sale_id) }}" class="text-indigo-600 hover:underline">
                    #{{ $credit->sale_id }}
                </a>
            @else
                -
            @endif
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-lg mb-3">بيانات الأجل</h2>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">المتبقي:</span>
                    <span class="font-bold text-red-600">
                        {{ number_format($credit->remaining, 2) }} ج.م
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">تاريخ الأجل:</span>
                    <span>{{ optional($credit->date)->format('Y-m-d') ?? '-' }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">تاريخ الإنشاء:</span>
                    <span>{{ optional($credit->created_at)->format('Y-m-d H:i') }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">آخر تحديث:</span>
                    <span>{{ optional($credit->updated_at)->format('Y-m-d H:i') }}</span>
                </div>

                <div class="mt-3">
                    <span class="text-gray-600 block mb-1">الوصف:</span>
                    <p class="text-gray-800 text-sm">
                        {{ $credit->description ?: 'لا يوجد' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-lg mb-3">بيانات العميل</h2>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">الاسم:</span>
                    <span>{{ $credit->customer->name ?? 'غير محدد' }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">الهاتف:</span>
                    <span>{{ $credit->customer->phone ?? '-' }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">البريد:</span>
                    <span>{{ $credit->customer->email ?? '-' }}</span>
                </div>

                <div class="mt-3">
                    <span class="text-gray-600 block mb-1">العنوان:</span>
                    <p class="text-gray-800 text-sm">
                        {{ $credit->customer->address ?? 'غير محدد' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ملخص الفاتورة لو موجودة --}}
    @if($credit->sale)
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <h2 class="font-semibold text-lg mb-3">ملخص الفاتورة</h2>

            <div class="grid md:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-gray-600">المجموع الفرعي</div>
                    <div>{{ number_format($credit->sale->subtotal, 2) }} ج.م</div>
                </div>
                <div>
                    <div class="text-gray-600">الخصم</div>
                    <div>{{ number_format($credit->sale->discount, 2) }} ج.م</div>
                </div>
                <div>
                    <div class="text-gray-600">الإجمالي</div>
                    <div class="font-bold text-green-700">
                        {{ number_format($credit->sale->total, 2) }} ج.م
                    </div>
                </div>
                <div>
                    <div class="text-gray-600">المدفوع</div>
                    <div>{{ number_format($credit->sale->paid, 2) }} ج.م</div>
                </div>
                <div>
                    <div class="text-gray-600">المتبقي على الفاتورة</div>
                    <div class="text-red-600 font-semibold">
                        {{ number_format($credit->remaining, 2) }} ج.م
                    </div>
                </div>
                <div>
                    <div class="text-gray-600">طريقة الدفع</div>
                    <div>{{ $credit->sale->payment_method }}</div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('receipt.show', $credit->sale->id) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    طباعة الفاتورة
                </a>
            </div>
        </div>
    @endif

    {{-- فورم السداد --}}
    {{-- فورم السداد --}}
@if($credit->remaining > 0)
    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="font-semibold text-lg mb-3">تسجيل سداد</h2>

        <form method="POST" action="{{ route('credits.pay', $credit) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 mb-1">المبلغ المدفوع</label>
                <input
                    type="number"
                    name="amount"
                    step="0.01"
                    min="0.01"
                    max="{{ $credit->remaining }}"
                    value="{{ old('amount') }}"
                    placeholder="اكتب مبلغ السداد"
                    class="border-gray-300 rounded-lg w-64">

                <p class="text-xs text-gray-500 mt-1">
                    الحد الأقصى: {{ number_format($credit->remaining, 2) }} ج.م
                </p>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">ملاحظات (اختياري)</label>
                <textarea
                    name="notes"
                    rows="3"
                    class="border-gray-300 rounded-lg w-full"
                    placeholder="أي ملاحظات عن عملية السداد">{{ old('notes') }}</textarea>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                تسجيل السداد
            </button>
        </form>
    </div>

    @else
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded">
            تم تسديد هذا الأجل بالكامل.
        </div>
    @endif
@endsection
