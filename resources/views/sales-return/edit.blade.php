@extends('layouts.app')
@section('title', 'تعديل المرتجع')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <div class="font-bold mb-2">فيه أخطاء:</div>
            <ul class="list-disc pr-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">تعديل المرتجع</h1>
            <p class="text-gray-500 mt-1">
                الفاتورة رقم
                <span class="text-indigo-600 font-bold">#{{ $saleReturn->sale_id }}</span>
            </p>
        </div>

        <a href="{{ route('returns.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:text-indigo-700 hover:border-indigo-200 transition">
            <span>الرجوع</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="h-2 bg-indigo-600"></div>

        <form action="{{ route('returns.update', $saleReturn->id) }}" method="POST" class="p-8 space-y-6" id="returnEditForm">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5 flex items-start gap-4">
                <div class="bg-white border border-indigo-100 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider">المنتج</div>
                    <div class="text-lg font-extrabold text-gray-900 mt-1">
                        {{ $saleReturn->variant->variant_name ?? $saleReturn->variant->name ?? 'منتج غير محدد' }}
                    </div>
                    <div class="text-sm text-indigo-800/70 mt-1">
                        أقصى كمية مسموح بها: <span class="font-bold">{{ $maxQty }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- الكمية --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">الكمية المرتجعة</label>

                    <div class="flex items-center gap-2">
                        <button type="button" id="decBtn"
                                class="w-12 h-12 rounded-xl border border-gray-200 bg-gray-50 font-extrabold text-gray-700 hover:bg-gray-100">
                            −
                        </button>

                        <input
                            type="number"
                            name="returned_qty"
                            id="returnedQty"
                            value="{{ old('returned_qty', $saleReturn->returned_qty) }}"
                            min="0"
                            max="{{ $maxQty }}"
                            step="1"
                            class="flex-1 h-12 px-4 rounded-xl border border-gray-200 bg-white text-center text-lg font-extrabold
                                   focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-60"
                            required
                        />

                        <button type="button" id="incBtn"
                                class="w-12 h-12 rounded-xl border border-gray-200 bg-gray-50 font-extrabold text-gray-700 hover:bg-gray-100">
                            +
                        </button>
                    </div>

                    <p class="text-xs text-gray-500">
                        لن يتم قبول رقم أكبر من <b>{{ $maxQty }}</b>
                    </p>
                </div>

                {{-- السبب --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">سبب الإرجاع</label>
                    <textarea
                        name="reason"
                        rows="3"
                        class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white
                               focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-60"
                        placeholder="مثال: تلف في المنتج"
                    >{{ old('reason', $saleReturn->reason) }}</textarea>
                </div>
            </div>

            <div class="pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('returns.index') }}"
                   class="px-6 py-3 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50">
                    إلغاء
                </a>

                <button type="submit"
                        class="px-8 py-3 rounded-xl bg-blue-600 text-white font-extrabold hover:bg-blue-700 shadow-lg shadow-blue-200 active:scale-[0.99] transition">
                    تحديث المرتجع
                </button>

                <button type="reset"
                        class="px-8 py-3 rounded-xl bg-green-600 text-white font-extrabold hover:bg-green-700 shadow-lg shadow-green-200 active:scale-[0.99] transition">
                    إعادة تعيين
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const input = document.getElementById('returnedQty');
    const max = parseInt(input.getAttribute('max') || '0', 10);
    const min = parseInt(input.getAttribute('min') || '0', 10);

    function clamp() {
        let v = parseInt(input.value || '0', 10);
        if (isNaN(v)) v = 0;
        if (v > max) v = max;
        if (v < min) v = min;
        input.value = v;
    }

    document.getElementById('incBtn').addEventListener('click', () => {
        clamp();
        if (parseInt(input.value, 10) < max) input.value = parseInt(input.value, 10) + 1;
    });

    document.getElementById('decBtn').addEventListener('click', () => {
        clamp();
        if (parseInt(input.value, 10) > min) input.value = parseInt(input.value, 10) - 1;
    });

    input.addEventListener('input', clamp);
    clamp();
})();
</script>
@endsection
