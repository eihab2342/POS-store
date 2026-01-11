<form action="{{ isset($variant) ? route('variants.update', $variant) : route('variants.store') }}" method="POST"
    class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow" dir="rtl">
    @isset($variant) @method('PUT') @endisset
    @csrf

    {{-- العنوان + زر طباعة الاستيكر في حالة التعديل --}}
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ isset($variant) ? 'تعديل الصنف' : 'إضافة صنف جديد' }}
        </h2>

        @isset($variant)
            <a href="{{ route('variants.print.labels', $variant->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 
                          bg-green-600 text-white font-semibold text-sm 
                          rounded-lg shadow-md hover:bg-purple-700 
                          transition-all duration-200">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 9V4h12v5M6 18h12v2H6v-2Zm0-7h12v5H6v-5Z" />
                </svg>

                طباعة استيكر
            </a>
        @endisset
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
    <label class="block text-gray-700 font-medium mb-2">SKU</label>
    <input type="text" name="sku" value="{{ old('sku', $sku ?? '') }}" readonly
        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
        placeholder="يتم توليده تلقائيًا">
    @error('sku') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
</div>


        <div>
            <label class="block text-gray-700 font-medium mb-2">اسم المنتج</label>
            <input type="text" name="name" value="{{ old('name', $variant->name ?? '') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="تيشرت قطن أسود">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">اللون</label>
            <input type="text" name="color" value="{{ old('color', $variant->color ?? '') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">المقاس</label>
            <input type="text" name="size" value="{{ old('size', $variant->size ?? '') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="M">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">تكلفة الشراء <span class="text-red-600">*</span></label>
            <input type="number" step="0.01" name="cost" value="{{ old('cost', $variant->cost ?? '') }}" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">سعر البيع <span class="text-red-600">*</span></label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $variant->price ?? '') }}" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">الكمية في المخزون</label>
            <input type="number" name="stock_qty" value="{{ old('stock_qty', $variant->stock_qty ?? 0) }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">حد إعادة الطلب</label>
            <input type="number" name="reorder_level" value="{{ old('reorder_level', $variant->reorder_level ?? 0) }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-4">
        <a href="{{ route('variants.index') }}"
            class="px-8 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            إلغاء
        </a>
        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition font-medium">
            {{ isset($variant) ? 'حفظ التعديلات' : 'إضافة الصنف' }}
        </button>
    </div>
</form>