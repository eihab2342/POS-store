@extends('layouts.app')

@section('title', 'سجل المبيعات')

@section('content')
<div class="p-6 font-sans" x-data="bulkDeleteHandler()">
    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📊 سجل المبيعات</h1>
        
        <button 
            @click="deleteSelected" 
            x-show="selectedIds.length > 0"
            style="display: none;"
            class="bg-red-600 text-white px-6 py-2 rounded-xl font-bold shadow-lg hover:bg-red-700 transition-all animate-modal-pop"
        >
            🗑️ حذف المحدّد (<span x-text="selectedIds.length"></span>) وإرجاع المخزون
        </button>
    </div>

    {{-- الإحصائيات --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-5 rounded-xl shadow-sm border-r-4 border-indigo-500">
            <p class="text-sm text-gray-500">إجمالي المبيعات</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalRevenue, 2) }} ج.م</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border-r-4 border-green-500">
            <p class="text-sm text-gray-500">إجمالي الأرباح</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($totalProfit, 2) }} ج.م</p>
        </div>
    </div>

    {{-- الفلاتر --}}
    <form method="GET" action="{{ route('sales.index') }}" class="mb-6 bg-white p-4 rounded-xl shadow-sm flex flex-wrap gap-4 items-end border border-gray-100">
        <div>
            <label class="block text-xs text-gray-500 mb-1">بحث برقم الفاتورة</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="border-gray-200 rounded-lg w-32 text-sm focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">فترة سريعة</label>
            <select name="period" class="border-gray-200 rounded-lg text-sm">
                <option value="">-- الكل --</option>
                <option value="today" @selected(($filters['period'] ?? '') === 'today')>اليوم</option>
                <option value="yesterday" @selected(($filters['period'] ?? '') === 'yesterday')>أمس</option>
                <option value="this_week" @selected(($filters['period'] ?? '') === 'this_week')>هذا الأسبوع</option>
                <option value="this_month" @selected(($filters['period'] ?? '') === 'this_month')>هذا الشهر</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">طريقة الدفع</label>
            <select name="payment_method" class="border-gray-200 rounded-lg text-sm">
                <option value="">-- الكل --</option>
                <option value="cash" @selected(($filters['payment_method'] ?? '') === 'cash')>نقدي</option>
                <option value="visa" @selected(($filters['payment_method'] ?? '') === 'visa')>فيزا</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm">تطبيق</button>
            <a href="{{ route('sales.index') }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600">تصفير</a>
        </div>
    </form>

    {{-- الجدول --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <table class="min-w-full text-right">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-4 text-center"><input type="checkbox" @change="toggleAll"></th>
                    <th class="px-6 py-4 text-gray-600 font-semibold text-sm">الفاتورة</th>
                    <th class="px-6 py-4 text-gray-600 font-semibold text-sm">التاريخ</th>
                    <th class="px-6 py-4 text-gray-600 font-semibold text-sm">العميل</th>
                    <th class="px-6 py-4 text-gray-600 font-semibold text-sm text-center">الإجمالي</th>
                    <th class="px-6 py-4 text-gray-600 font-semibold text-sm text-center">الربح</th>
                    <th class="px-6 py-4 text-center text-gray-600 font-semibold text-sm">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($sales as $sale)
                <tr class="hover:bg-gray-50 transition" :class="selectedIds.includes({{ $sale->id }}) ? 'bg-indigo-50' : ''">
                    <td class="px-4 py-4 text-center">
                        <input type="checkbox" value="{{ $sale->id }}" x-model.number="selectedIds">
                    </td>
                    <td class="px-6 py-4 font-bold text-indigo-600">#{{ $sale->id }}</td>
                    <td class="px-6 py-4 font-bold text-indigo-600">{{ $sale->created_at }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $sale->customer_data ?? $sale->customer->name ?? 'عميل نقدي' }}</td>
                    <td class="px-6 py-4 font-semibold text-center">{{ number_format($sale->total, 2) }}</td>
                    <td class="px-6 py-4 font-semibold text-center text-green-600">{{ number_format($sale->calculated_profit, 2) }}</td>
                    <td class="px-3 py-4">
                        <div class="flex justify-center gap-2">
                            @if($sale->status !== 'closed')
                                <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-indigo-600 font-bold text-sm">عرض</a>
                                <button onclick="openEditModal({{ json_encode($sale) }})" class="text-amber-500 hover:text-amber-700 font-bold text-sm">تعديل</button>
                                <a href="{{ route('receipt.show', $sale) }}" class="text-green-600 hover:text-indigo-600 font-bold text-sm">طباعة</a>
                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('حذف الفاتورة وإرجاع المخزن؟')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-600 font-bold text-sm">حذف</button>
                                </form>
                            @else
                                <span class="text-green-500 font-semibold">مغلقة</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sales->links() }}</div>
</div>

{{-- المودال النحيف --}}
<div id="editModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-xl" onclick="closeModal()"></div>
    <div class="relative bg-white rounded-[2rem] shadow-2xl max-w-md w-full overflow-hidden animate-modal-pop">
        <div class="h-2 bg-indigo-600"></div>
        <div class="p-8">
            <h3 class="text-xl font-black mb-6">تعديل الفاتورة</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-black text-indigo-600 uppercase mb-1">اسم العميل</label>
                    <input type="text" name="customer_name" id="modal_customer_name" class="w-full bg-gray-50 border rounded-xl p-3 font-bold text-sm outline-none focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">الإجمالي الفرعي</label>
                        <input type="text" id="modal_subtotal" readonly class="w-full bg-gray-100 border rounded-xl p-3 font-bold text-gray-400 cursor-not-allowed text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-red-500 uppercase mb-1">الخصم</label>
                        <input type="number" step="0.01" name="discount" id="modal_discount" class="w-full bg-gray-50 border rounded-xl p-3 font-bold text-red-600 text-sm outline-none focus:border-red-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-green-600 uppercase mb-1">المدفوع</label>
                        <input type="number" step="0.01" name="paid" id="modal_paid" class="w-full bg-gray-50 border rounded-xl p-3 font-bold text-green-600 text-sm outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-indigo-600 uppercase mb-1">طريقة الدفع</label>
                        <select name="payment_method" id="modal_payment_method" class="w-full bg-gray-50 border rounded-xl p-3 font-bold text-sm outline-none focus:border-indigo-500">
                            <option value="cash">نقدي</option>
                            <option value="visa">فيزا</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-[2] bg-blue text-black py-3.5 rounded-xl font-black">حفظ</button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-green text-black py-3.5 rounded-xl font-bold">تراجع</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function bulkDeleteHandler() {
        return {
            selectedIds: [],
            toggleAll(e) {
                this.selectedIds = e.target.checked ? @json($sales->pluck('id')) : [];
            },
            deleteSelected() {
                if (confirm(`حذف ${this.selectedIds.length} فواتير وإعادة المخزون؟`)) {
                    fetch("{{ route('sales.bulk-delete') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ ids: this.selectedIds })
                    }).then(res => res.json()).then(data => data.success && window.location.reload());
                }
            }
        }
    }
    function openEditModal(sale) {
        document.getElementById('editForm').action = `/sales/${sale.id}`;
        document.getElementById('modal_customer_name').value = sale.customer_name || '';
        document.getElementById('modal_subtotal').value = sale.subtotal;
        document.getElementById('modal_discount').value = sale.discount;
        document.getElementById('modal_paid').value = sale.paid;
        document.getElementById('modal_payment_method').value = sale.payment_method;
        document.getElementById('editModal').classList.replace('hidden', 'flex');
    }
    function closeModal() { document.getElementById('editModal').classList.replace('flex', 'hidden'); }
</script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
    .font-sans { font-family: 'Cairo', sans-serif !important; }
    @keyframes modal-pop { 0% { transform: scale(0.95); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
    .animate-modal-pop { animation: modal-pop 0.2s ease-out forwards; }
</style>
@endsection
