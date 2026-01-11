@extends('layouts.app')

@section('title', 'تفاصيل المصروف')

@section('content')
<style>
.detail-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    max-width: 1000px;
    margin: 0 auto;
}

.detail-header {
    background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    padding: 1.5rem 2rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
}

.info-item {
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.info-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
    color: #1f2937;
    font-weight: 500;
}

.amount-display {
    font-size: 2rem;
    font-weight: 700;
    color: #8b5cf6;
    text-align: center;
    padding: 2rem;
    background: #f5f3ff;
    border-radius: 0.75rem;
    margin: 1rem 2rem;
}

.attachment-preview {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    border: 1px dashed #d1d5db;
}

.attachment-icon {
    width: 3rem;
    height: 3rem;
    background: #8b5cf6;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.action-buttons {
    padding: 1.5rem 2rem;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-display {
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-weight: 500;
    font-size: 0.875rem;
}
</style>

<div class="container mx-auto px-4 py-8">
    <div class="detail-card">
        <div class="detail-header">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">مصروف رقم: {{ $expense->expense_number }}</h1>
                    <p class="text-white/80 mt-1">تفاصيل المصروف</p>
                </div>
                <div class="status-display bg-white/20">
                    {{ $expense->getStatusArabic() }}
                </div>
            </div>
        </div>

        <!-- المبلغ الكبير -->
        <div class="amount-display">
            {{ number_format($expense->amount, 2) }} ج.م
        </div>

        <!-- معلومات المصروف -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">التاريخ</div>
                <div class="info-value">{{ $expense->expense_date->format('d/m/Y') }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">نوع المصروف</div>
                <div class="info-value">{{ $expense->getExpenseTypeArabic() }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">طريقة الدفع</div>
                <div class="info-value">{{ $expense->getPaymentMethodArabic() }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">رقم المرجع</div>
                <div class="info-value">{{ $expense->reference_number ?: 'لا يوجد' }}</div>
            </div>

            @if($expense->supplier)
            <div class="info-item">
                <div class="info-label">المورد</div>
                <div class="info-value">{{ $expense->supplier->name }}</div>
            </div>
            @endif

            @if($expense->employee)
            <div class="info-item">
                <div class="info-label">الموظف المسؤول</div>
                <div class="info-value">{{ $expense->employee->name }}</div>
            </div>
            @endif

            <div class="info-item">
                <div class="info-label">تم الإنشاء بواسطة</div>
                <div class="info-value">{{ $expense->creator->name }}</div>
            </div>

            @if($expense->approver)
            <div class="info-item">
                <div class="info-label">تمت الموافقة بواسطة</div>
                <div class="info-value">{{ $expense->approver->name }}</div>
            </div>
            @endif

            <div class="info-item">
                <div class="info-label">تاريخ الإنشاء</div>
                <div class="info-value">{{ $expense->created_at->format('d/m/Y h:i A') }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">آخر تحديث</div>
                <div class="info-value">{{ $expense->updated_at->format('d/m/Y h:i A') }}</div>
            </div>
        </div>

        <!-- الوصف -->
        <div class="px-6 py-4">
            <div class="info-label mb-2">وصف المصروف</div>
            <div class="info-value bg-gray-50 p-4 rounded-lg">
                {{ $expense->description }}
            </div>
        </div>

        <!-- الملاحظات -->
        @if($expense->notes)
        <div class="px-6 py-4">
            <div class="info-label mb-2">ملاحظات</div>
            <div class="info-value bg-yellow-50 p-4 rounded-lg">
                {{ $expense->notes }}
            </div>
        </div>
        @endif

        <!-- المرفق -->
        @if($expense->attachment)
        <div class="px-6 py-4">
            <div class="info-label mb-2">الملف المرفق</div>
            <div class="attachment-preview flex items-center gap-4">
                <div class="attachment-icon">
                    <i class="fas fa-paperclip"></i>
                </div>
                <div>
                    <div class="info-value">{{ basename($expense->attachment) }}</div>
                    <a href="{{ route('expenses.download', $expense) }}" 
                       class="text-sm text-purple-600 hover:text-purple-800 flex items-center gap-1 mt-1">
                        <i class="fas fa-download"></i>
                        تحميل الملف
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- أزرار الإجراءات -->
        <div class="action-buttons">
            <a href="{{ route('expenses.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                رجوع للقائمة
            </a>

            @if(auth()->user()->role == 'manager')
            <div class="flex gap-3">
                @if($expense->status == 'pending')
                <form action="{{ route('expenses.approve', $expense) }}" method="POST" onsubmit="return confirm('هل تريد الموافقة على هذا المصروف؟')">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-check ml-1"></i>
                        الموافقة
                    </button>
                </form>

                <button type="button" onclick="showRejectionForm()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-times ml-1"></i>
                    رفض
                </button>
                @endif

                @if($expense->status == 'approved')
                <form action="{{ route('expenses.markPaid', $expense) }}" method="POST" onsubmit="return confirm('هل تريد تسجيل المصروف كمصروف مدفوع؟')">
                    @csrf
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-money-bill-wave ml-1"></i>
                        تسديد المصروف
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- نموذج الرفض -->
@if($expense->status == 'pending' && auth()->user()->role == 'manager')
<div id="rejectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">رفض المصروف</h3>
            <form action="{{ route('expenses.reject', $expense) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">سبب الرفض</label>
                    <textarea name="rejection_reason" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"
                              rows="4"
                              placeholder="أدخل سبب رفض المصروف..."
                              required></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideRejectionForm()" 
                            class="px-4 py-2 text-gray-700 hover:text-gray-900">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                        تأكيد الرفض
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectionForm() {
    document.getElementById('rejectionModal').classList.remove('hidden');
    document.getElementById('rejectionModal').classList.add('flex');
}

function hideRejectionForm() {
    document.getElementById('rejectionModal').classList.remove('flex');
    document.getElementById('rejectionModal').classList.add('hidden');
}
</script>
@endif
@endsection