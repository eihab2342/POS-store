@extends('layouts.app')

@section('title', 'تعديل كميات المخزون')

@section('content')
    <div class="p-6">
        <div id="toast-container" class="fixed top-5 left-5 z-50"></div>

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                🔄 تعديل كميات المخزون (Bulk Update)
            </h1>
            <a href="{{ route('variants.index') }}"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                ← العودة للأصناف
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-xl p-6">
            <form action="{{ route('variants.bulk.update') }}" method="POST" id="bulk-update-form">
                @csrf
                <div class="overflow-y-auto max-h-[65vh] rounded-lg border">
                    <table class="min-w-full text-right">
                        <thead class="bg-gray-100 text-gray-700 text-sm border-b sticky top-0">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">اسم المنتج</th>
                                <th class="px-4 py-3">الكمية الحالية</th>
                                <th class="px-4 py-3">الكمية الجديدة</th>
                                <th class="px-4 py-3 text-center">إجراءات</th>
                            </tr>
                        </thead>

                        <tbody class="text-sm">
                            @foreach($variants as $v)
                                <tr class="border-b hover:bg-gray-50 transition" id="row-{{ $v->id }}">
                                    <td class="px-4 py-3 font-semibold text-gray-700">{{ $v->id }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-700">{{ $v->sku }}</td>
                                    <td class="px-4 py-3 text-gray-800">{{ $v->name }}</td>
                                    <td class="px-4 py-3 text-blue-700 font-bold current-qty">{{ $v->stock_qty }}</td>
                                    <td class="px-4 py-3 w-40">
                                        <input type="number" 
                                               name="variants[{{ $v->id }}]"
                                               data-id="{{ $v->id }}"
                                               class="qty-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="تحديث">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('variants.print.labels', $v->id) }}" 
                                           target="_blank"
                                           class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 hover:bg-blue-100 text-blue-600 rounded-md transition border border-blue-200">
                                            🖨️ طباعة
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 sticky bottom-4 flex justify-center">
                    <button type="submit"
                        class="px-10 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg transition">
                        💾 حفظ الكل يدوياً
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                const variantId = this.getAttribute('data-id');
                const newQty = this.value;
                const row = document.getElementById('row-' + variantId);

                if (newQty === '') return;

                // تغيير شكل الحقل ليوضح أنه جاري الحفظ
                this.classList.add('bg-yellow-50', 'border-yellow-400');

                fetch("{{ route('variants.bulk.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        variants: { [variantId]: newQty },
                        is_ajax: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // تحديث النص في الخانة الحالية
                        row.querySelector('.current-qty').innerText = newQty;
                        // تمييز الحقل باللون الأخضر لحظياً
                        this.classList.remove('bg-yellow-50', 'border-yellow-400');
                        this.classList.add('bg-green-50', 'border-green-400');
                        setTimeout(() => this.classList.remove('bg-green-50', 'border-green-400'), 2000);
                        showToast('✅ تم التحديث بنجاح');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('❌ حدث خطأ أثناء الحفظ', 'bg-red-500');
                });
            });
        });

        function showToast(message, bgColor = 'bg-green-500') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-xl mb-3 transition-opacity duration-500`;
            toast.innerText = message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }
    </script>
@endsection