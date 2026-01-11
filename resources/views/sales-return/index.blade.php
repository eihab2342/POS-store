@extends('layouts.app')
@section('title', 'إدارة المرتجعات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">قائمة المرتجعات</h1>
    <a href="{{ route('returns.create') }}"
       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">+ إضافة مرتجع</a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white shadow rounded-lg overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-2 text-right">#</th>
            <th class="px-4 py-2 text-right">رقم الفاتورة</th>
            <th class="px-4 py-2 text-right">المنتج</th>
            <th class="px-4 py-2 text-right">الكمية المرتجعة</th>
            <th class="px-4 py-2 text-right">السبب</th>
            <th class="px-4 py-2 text-right">المستخدم</th>
            <th class="px-4 py-2 text-right">تاريخ الإضافة</th>
            <th class="px-4 py-2 text-right">إجراءات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($returns as $return)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2">{{ $return->id }}</td>
                <td class="px-4 py-2">#{{ $return->sale_id }}</td>
                <td class="px-4 py-2">{{ $return->variant->name ?? '-' }}</td>
                <td class="px-4 py-2">{{ $return->returned_qty }}</td>
                <td class="px-4 py-2">{{ $return->reason ?? '-' }}</td>
                <td class="px-4 py-2">{{ $return->user->name ?? '-' }}</td>
                <td class="px-4 py-2">{{ $return->created_at->format('Y-m-d') }}</td>
                <td class="px-4 py-2 flex gap-2">
                    <a href="{{ route('returns.show', $return) }}" class="text-indigo-600 hover:underline">عرض</a>
                    <a href="{{ route('returns.edit', $return) }}" class="text-green-600 hover:underline">تعديل</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-gray-500 py-4">لا توجد مرتجعات</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $returns->links() }}
</div>
@endsection
