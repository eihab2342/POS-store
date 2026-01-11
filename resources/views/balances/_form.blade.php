@csrf

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    {{-- العميل --}}
    {{-- <div>
        <label class="block text-sm text-gray-600 mb-1">العميل <span class="text-red-500">*</span></label>
        <select name="customer_id" class="w-full border-gray-300 rounded-lg">
            <option value="">اختر عميل</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $balance->customer_id ?? null) == $customer->id)>
                    {{ $customer->name }}
                </option>
            @endforeach
        </select>
        @error('customer_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div> --}}

    {{-- الفاتورة --}}
    {{-- <div>
        <label class="block text-sm text-gray-600 mb-1">الفاتورة</label>
        <select name="sale_id" class="w-full border-gray-300 rounded-lg">
            <option value="">بدون</option>
            @foreach($invoices as $invoice)
                <option value="{{ $invoice->id }}" @selected(old('sale_id', $balance->sale_id ?? null) == $invoice->id)>
                    #{{ $invoice->id }} - {{ number_format($invoice->total, 2) }} ج.م
                </option>
            @endforeach
        </select>
        @error('sale_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div> --}}

    {{-- تاريخ الدفع --}}
    <div>
        <label class="block text-sm text-gray-600 mb-1">تاريخ الدفع</label>
        <input type="datetime-local" name="payment_date"
            value="{{ old('payment_date', isset($balance) && $balance->payment_date ? $balance->payment_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
            class="w-full border-gray-300 rounded-lg">
        @error('payment_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- طرق الدفع --}}
<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
    <h3 class="font-semibold mb-3 text-gray-800">طرق الدفع</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm text-gray-600 mb-1">💵 كاش</label>
            <input type="number" step="0.01" min="0" name="cash_amount"
                value="{{ old('cash_amount', $balance->cash_amount ?? 0) }}" class="w-full border-gray-300 rounded-lg">
            @error('cash_amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">📱 محفظة موبايل</label>
            <input type="number" step="0.01" min="0" name="wallet_amount"
                value="{{ old('wallet_amount', $balance->wallet_amount ?? 0) }}"
                class="w-full border-gray-300 rounded-lg">
            @error('wallet_amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">💳 InstaPay</label>
            <input type="number" step="0.01" min="0" name="instapay_amount"
                value="{{ old('instapay_amount', $balance->instapay_amount ?? 0) }}"
                class="w-full border-gray-300 rounded-lg">
            @error('instapay_amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <label class="block text-sm text-gray-600 mb-1">رقم المحفظة</label>
            <input type="text" name="wallet_phone" value="{{ old('wallet_phone', $balance->wallet_phone ?? '') }}"
                class="w-full border-gray-300 rounded-lg" placeholder="01xxxxxxxxx">
            @error('wallet_phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">رقم عملية InstaPay</label>
            <input type="text" name="instapay_reference"
                value="{{ old('instapay_reference', $balance->instapay_reference ?? '') }}"
                class="w-full border-gray-300 rounded-lg" placeholder="REF-xxxxx">
            @error('instapay_reference') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- الحالة والإجمالي والملاحظات --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div>
        <label class="block text-sm text-gray-600 mb-1">الحالة</label>
        <select name="status" class="w-full border-gray-300 rounded-lg">
            @php $statusOld = old('status', $balance->status ?? 'completed'); @endphp
            <option value="pending" @selected($statusOld === 'pending')>معلق</option>
            <option value="completed" @selected($statusOld === 'completed')>مكتمل</option>
            <option value="cancelled" @selected($statusOld === 'cancelled')>ملغي</option>
        </select>
        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm text-gray-600 mb-1">إجمالي المبلغ (يتم حسابه تلقائيًا)</label>
        <input type="text" readonly value="{{ number_format(($balance->total_amount ?? 0), 2) }} ج.م"
            class="w-full border-gray-300 rounded-lg bg-gray-100 font-bold">
    </div>
</div>

<div class="mb-6">
    <label class="block text-sm text-gray-600 mb-1">ملاحظات</label>
    <textarea name="notes" rows="3"
        class="w-full border-gray-300 rounded-lg">{{ old('notes', $balance->notes ?? '') }}</textarea>
    @error('notes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="flex items-center gap-3">
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
        حفظ
    </button>

    <a href="{{ route('balances.index') }}" class="text-sm text-gray-600 hover:text-gray-800">
        إلغاء
    </a>
</div>