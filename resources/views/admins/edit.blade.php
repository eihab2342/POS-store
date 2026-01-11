@extends('layouts.app')

@section('title', 'تعديل مستخدم')

@section('content')
    <h1 class="text-2xl font-bold mb-4">تعديل مستخدم</h1>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admins.update', $admin) }}" method="POST"
        class="bg-white rounded-xl shadow p-6 max-w-xl space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">الاسم</label>
            <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">كلمة المرور الجديدة (اختياري)</label>
            <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 text-sm" minlength="8">
            <p class="text-xs text-gray-500 mt-1">اتركها فارغة إذا لا تريد تغيير كلمة المرور</p>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
            <label for="is_active" class="text-sm">الحساب نشط</label>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admins.index') }}" class="px-4 py-2 text-sm border rounded-lg">
                إلغاء
            </a>
            <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                حفظ التغييرات
            </button>
        </div>
    </form>
@endsection