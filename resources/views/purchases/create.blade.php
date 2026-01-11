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
                    <h1 class="text-3xl font-bold text-gray-900">إضافة فاتورة شراء جديدة</h1>
                    <p class="mt-2 text-sm text-gray-600">للمورد: {{ $supplier->name }}</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('purchases.store') }}" method="POST" class="bg-white rounded-lg shadow-sm">
            @csrf
            <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

            <div class="p-6 space-y-6">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        رقم الفاتورة <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="invoice_no"
                           value="{{ old('invoice_no') }}"
                           required
                           placeholder="مثل: INV-2024-001"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('invoice_no') border-red-500 @enderror">
                    @error('invoice_no')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تاريخ الفاتورة <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="date"
                           value="{{ old('date', date('Y-m-d')) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('date') border-red-500 @enderror">
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المبلغ الإجمالي (ج.م) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="total_cost"
                           value="{{ old('total_cost') }}"
                           step="0.01"
                           min="0.01"
                           required
                           class="w-full px-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('total_cost') border-red-500 @enderror">
                    @error('total_cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">إجمالي قيمة الفاتورة (سيُضاف إلى رصيد المورد)</p>
                </div>

            </div>

            <!-- Footer Buttons -->
            <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-end gap-3 rounded-b-lg">
                <a href="{{ route('suppliers.show', $supplier) }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition duration-150">
                    إلغاء
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150">
                    حفظ الفاتورة
                </button>
            </div>

        </form>
    </div>
</div>
@endsection