@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('suppliers.show', $payment->supplier) }}"
                   class="text-gray-600 hover:text-gray-900 transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">تعديل الدفعة</h1>
                    <p class="mt-2 text-sm text-gray-600">للمورد: {{ $payment->supplier->name }}</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('payments.update', $payment) }}" method="POST" class="bg-white rounded-lg shadow-sm">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المبلغ (ج.م) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="amount"
                           value="{{ old('amount', $payment->amount) }}"
                           step="0.01"
                           min="0.01"
                           required
                           class="w-full px-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('amount') border-red-500 @enderror">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        التاريخ <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="date"
                           value="{{ old('date', $payment->date->format('Y-m-d')) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('date') border-red-500 @enderror">
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        طريقة الدفع <span class="text-red-500">*</span>
                    </label>
                    <select name="method"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('method') border-red-500 @enderror">
                        <option value="">اختر طريقة الدفع</option>
                        <option value="cash" {{ old('method', $payment->method) == 'cash' ? 'selected' : '' }}>نقدي</option>
                        <option value="bank_transfer" {{ old('method', $payment->method) == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="check" {{ old('method', $payment->method) == 'check' ? 'selected' : '' }}>شيك</option>
                        <option value="other" {{ old('method', $payment->method) == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                    @error('method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الفاتورة المرتبطة (اختياري)
                    </label>
                    <select name="purchase_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('purchase_id') border-red-500 @enderror">
                        <option value="">بدون ربط بفاتورة محددة</option>
                        @foreach($purchases as $purchase)
                            <option value="{{ $purchase->id }}" {{ old('purchase_id', $payment->purchase_id) == $purchase->id ? 'selected' : '' }}>
                                {{ $purchase->invoice_no }} - متبقي: {{ number_format($purchase->remaining_amount, 2) }} ج.م
                            </option>
                        @endforeach
                    </select>
                    @error('purchase_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المرجع / رقم الإيصال
                    </label>
                    <input type="text"
                           name="reference"
                           value="{{ old('reference', $payment->reference) }}"
                           placeholder="مثل: رقم الشيك، رقم التحويل، رقم الإيصال..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('reference') border-red-500 @enderror">
                    @error('reference')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ملاحظات
                    </label>
                    <textarea name="note"
                              rows="3"
                              placeholder="أي ملاحظات إضافية..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('note') border-red-500 @enderror">{{ old('note', $payment->note) }}</textarea>
                    @error('note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Footer Buttons -->
            <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-between rounded-b-lg">
                <div class="text-sm text-gray-600">
                    تم التسجيل بواسطة: {{ $payment->user?->name ?? 'غير معروف' }}
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('suppliers.show', $payment->supplier) }}"
                       class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition duration-150">
                        إلغاء
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-150">
                        حفظ التغييرات
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection