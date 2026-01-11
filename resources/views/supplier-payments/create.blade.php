@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('suppliers.show', $supplier) }}" 
                   class="text-gray-600 hover:text-gray-900 transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">تسجيل دفعة جديدة</h1>
                    <p class="mt-2 text-sm text-gray-600">للمورد: {{ $supplier->name }}</p>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 border-r-4 border-blue-500 p-4 rounded-lg mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 ml-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-900">الرصيد الحالي على المورد</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ number_format($supplier->current_balance, 2) }} ج.م</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('payments.store') }}" method="POST" class="bg-white rounded-lg shadow-sm">
            @csrf
            <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
            
            <div class="p-6 space-y-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المبلغ (ج.م) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="amount" 
                           value="{{ old('amount') }}"
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
                           value="{{ old('date', date('Y-m-d')) }}"
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
                        <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                        <option value="bank_transfer" {{ old('method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="check" {{ old('method') == 'check' ? 'selected' : '' }}>شيك</option>
                        <option value="other" {{ old('method') == 'other' ? 'selected' : '' }}>أخرى</option>
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
                            <option value="{{ $purchase->id }}" {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                {{ $purchase->invoice_no }} - متبقي: {{ number_format($purchase->remaining_amount, 2) }} ج.م
                            </option>
                        @endforeach
                    </select>
                    @error('purchase_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">يمكنك ربط الدفعة بفاتورة محددة أو تركها عامة</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المرجع / رقم الإيصال
                    </label>
                    <input type="text" 
                           name="reference" 
                           value="{{ old('reference') }}"
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
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('note') border-red-500 @enderror">{{ old('note') }}</textarea>
                    @error('note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Footer Buttons -->
            <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-end gap-3 rounded-b-lg">
                <a href="{{ route('suppliers.show', $supplier) }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition duration-150">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-150">
                    تسجيل الدفعة
                </button>
            </div>

        </form>
    </div>
</div>
@endsection