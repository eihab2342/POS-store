@extends('layouts.app')

@section('title', 'المستخدمون')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">المستخدمون (أدمن)</h1>
        <a href="{{ route('admins.create') }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
            + مستخدم جديد
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admins.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد"
            class="border rounded-lg px-3 py-2 text-sm">

        <select name="is_active" class="border rounded-lg px-3 py-2 text-sm">
            <option value="">كل الحالات</option>
            <option value="1" @selected(request('is_active') === '1')>نشط</option>
            <option value="0" @selected(request('is_active') === '0')>غير نشط</option>
        </select>

        <button class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">بحث</button>
    </form>

    <form method="POST" action="{{ route('admins.bulk-destroy') }}">
        @csrf
        @method('DELETE')

        <div class="mb-3 flex justify-between items-center">
            <div>
                <button type="submit" onclick="return confirm('هل أنت متأكد من حذف المستخدمين المحددين؟')"
                    class="px-3 py-2 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700">
                    حذف المحدد
                </button>
            </div>
            <div class="text-sm text-gray-500">
                عدد النتائج: {{ $admins->total() }}
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-center">
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th class="px-3 py-2 text-right">#</th>
                        <th class="px-3 py-2 text-right">الاسم</th>
                        <th class="px-3 py-2 text-right">البريد الإلكتروني</th>
                        <th class="px-3 py-2 text-right">الصلاحية</th>
                        <th class="px-3 py-2 text-right">الحالة</th>
                        <th class="px-3 py-2 text-right">تاريخ الإنشاء</th>
                        <th class="px-3 py-2 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr class="border-t">
                            <td class="px-3 py-2 text-center">
                                @if($admin->id !== auth()->id())
                                    <input type="checkbox" name="ids[]" value="{{ $admin->id }}" class="row-check">
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $admin->id }}</td>
                            <td class="px-3 py-2 font-medium">{{ $admin->name }}</td>
                            <td class="px-3 py-2">
                                <span class="text-gray-700">{{ $admin->email }}</span>
                            </td>
                            <td class="px-3 py-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs
                                        {{ $admin->role === 'admin' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $admin->role === 'admin' ? 'مدير' : 'مستخدم عادي' }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                @if($admin->is_active)
                                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-700">نشط</span>
                                @else
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">غير نشط</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-600">
                                {{ $admin->created_at?->format('d/m/Y h:i A') }}
                            </td>
                            <td class="px-3 py-2 space-x-1 space-x-reverse text-left">
                                <a href="{{ route('admins.edit', $admin) }}"
                                    class="inline-block px-2 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                    تعديل
                                </a>

                                @if($admin->id !== auth()->id())
                                    <form action="{{ route('admins.destroy', $admin) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')"
                                            class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                            حذف
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-gray-500">
                                لا توجد مستخدمين
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-4">
        {{ $admins->links() }}
    </div>

    @push('scripts')
        <script>
            const checkAll = document.getElementById('checkAll');
            const rowChecks = document.querySelectorAll('.row-check');
            if (checkAll) {
                checkAll.addEventListener('change', function () {
                    rowChecks.forEach(ch => ch.checked = checkAll.checked);
                });
            }
        </script>
    @endpush
@endsection