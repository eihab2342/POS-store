{{-- ============================================================ --}}
{{-- resources/views/supplier-transactions/debt-form.blade.php --}}
{{-- ============================================================ --}}

@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8" dir="rtl">
        <div class="max-w-2xl mx-auto">

            <h1 class="text-3xl font-bold text-gray-800 mb-6">تسجيل دين جديد</h1>

            <form action="{{ route('supplier-payments.store') }}" method="POST"
                class="bg-white rounded-lg shadow-lg p-6">
                @csrf

                <div class="space-y-4">
                    {{-- المورد --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            المورد <span class="text-red-600">*</span>
                        </label>
                        <select name="supplier_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">اختر المورد</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ (old('supplier_id') == $s->id || ($selectedSupplier && $selectedSupplier->id == $s->id)) ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- المبلغ --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            المبلغ (جنيه) <span class="text-red-600">*</span>
                        </label>
                        <input type="number" name="amount" value="{{ old('amount') }}" required step="0.01" min="0.01"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('amount')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- التاريخ --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            التاريخ <span class="text-red-600">*</span>
                        </label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('transaction_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- الوصف --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            الوصف <span class="text-red-600">*</span>
                        </label>
                        <textarea name="description" rows="3" required
                            placeholder="مثال: بضاعة شهر يناير، أو فاتورة رقم 123"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- الأزرار --}}
                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ $selectedSupplier ? route('suppliers.show', $selectedSupplier) : route('suppliers.index') }}"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                        إلغاء
                    </a>
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                        تسجيل الدين
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection