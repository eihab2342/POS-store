{{-- ============================================================ --}}
{{-- resources/views/suppliers/form.blade.php - فورم المورد --}}
{{-- ============================================================ --}}

@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8" dir="rtl">
        <div class="max-w-2xl mx-auto">

            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                {{ isset($supplier) ? 'تعديل المورد' : 'إضافة مورد جديد' }}
            </h1>

            <form action="{{ isset($supplier) ? route('suppliers.update', $supplier) : route('suppliers.store') }}"
                method="POST" class="bg-white rounded-lg shadow-lg p-6">
                @csrf
                @if(isset($supplier))
                    @method('PUT')
                @endif

                <div class="space-y-4">
                    {{-- اسم المورد --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            اسم المورد <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- الهاتف --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            رقم الهاتف <span class="text-red-600">*</span>
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- البريد --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            البريد الإلكتروني
                        </label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- العنوان --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">العنوان</label>
                        <textarea name="address" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('address', $supplier->address ?? '') }}</textarea>
                        @error('address')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- الرصيد الافتتاحي (فقط عند الإضافة) --}}
                    @if(!isset($supplier))
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                الرصيد الافتتاحي (لو كان عليك فلوس من قبل)
                            </label>
                            <input type="number" name="opening_balance" value="{{ old('opening_balance', 0) }}" step="0.01"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('opening_balance')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- ملاحظات --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes', $supplier->notes ?? '') }}</textarea>
                        @error('notes')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- الأزرار --}}
                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('suppliers.index') }}"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                        إلغاء
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                        {{ isset($supplier) ? 'حفظ التعديلات' : 'إضافة المورد' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection