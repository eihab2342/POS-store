@extends('layouts.app')
@section('title', 'إضافة مرتجع')

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow">
        @if ($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
                <div class="font-bold mb-2">فيه أخطاء:</div>
                <ul class="list-disc pr-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1 class="text-2xl font-bold mb-6 text-gray-800">🧾 إضافة مرتجع جديد</h1>

        {{-- حقل رقم الفاتورة --}}
        <div class="mb-6">
            <label class="block text-sm mb-2 text-gray-600">رقم الفاتورة</label>
            <input type="number" id="invoiceInput" placeholder="اكتب رقم الفاتورة واضغط Enter"
                class="border-gray-300 rounded-lg w-full focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
            <p id="invoiceError" class="text-red-600 text-sm mt-1 hidden"></p>
        </div>

        {{-- تفاصيل الفاتورة --}}
        <div id="saleDetails" class="hidden">
            {{-- بطاقة بيانات الفاتورة --}}
            <div class="bg-gray-50 border rounded-lg p-4 mb-4 flex flex-wrap justify-between gap-4">
                <div class="flex-1 min-w-[150px] bg-white border rounded-lg p-3 shadow">
                    <h3 class="text-gray-500 text-sm mb-1">العميل</h3>
                    <p id="customerName" class="font-semibold text-gray-800"></p>
                </div>

                <div class="flex-1 min-w-[150px] bg-white border rounded-lg p-3 shadow">
                    <h3 class="text-gray-500 text-sm mb-1">إجمالي الفاتورة</h3>
                    <p id="saleTotal" class="font-semibold text-indigo-600"></p>
                </div>

                <div class="flex-1 min-w-[150px] bg-white border rounded-lg p-3 shadow">
                    <h3 class="text-gray-500 text-sm mb-1">المدفوع</h3>
                    <p id="paidAmount" class="font-semibold text-green-600"></p>
                </div>

                <div class="flex-1 min-w-[150px] bg-white border rounded-lg p-3 shadow">
                    <h3 class="text-gray-500 text-sm mb-1">المتبقي</h3>
                    <p id="remainingAmount" class="font-semibold text-red-600"></p>
                </div>
            </div>

            {{-- تنبيه الحد الأقصى --}}
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-blue-700 font-medium">
                        الحد الأقصى المسموح: <span id="maxRefundAmount" class="font-bold"></span> ج.م
                    </span>
                </div>
                <p class="text-blue-600 text-sm mt-1">
                    يمكنك إدخال أي كمية، ولكن الإجمالي المحسوب للعرض لن يتجاوز هذا المبلغ.
                </p>
            </div>

            {{-- جدول المنتجات --}}
            <form id="returnForm" method="POST" action="{{ route('returns.store') }}">
                @csrf
                <input type="hidden" name="sale_id" id="saleId">

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border mb-4">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-right">المنتج</th>
                                <th class="px-4 py-2 text-right">السعر</th>
                                <th class="px-4 py-2 text-right">الكمية المباعة</th>
                                <th class="px-4 py-2 text-right">الكمية المرتجعة</th>
                                <th class="px-4 py-2 text-right">المجموع المرتجع</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTable"></tbody>
                    </table>
                </div>

                {{-- طريقة استرداد المبلغ --}}
                <div class="mb-4">
                    <label class="block text-sm mb-2 text-gray-600">طريقة استرداد المبلغ</label>
                    <select name="refund_method" id="refundMethod"
                        class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                        <option value="cash">نقداً</option>
                        <option value="wallet">محفظة</option>
                        <option value="credit">رصيد للعميل</option>
                    </select>
                </div>

                {{-- سبب المرتجع --}}
                <div class="mb-4">
                    <label class="block text-sm mb-2 text-gray-600">سبب المرتجع (اختياري)</label>
                    <textarea name="reason" rows="2"
                        class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
                        placeholder="أدخل سبب المرتجع..."></textarea>
                </div>

                <div class="mt-6 p-4 bg-gray-50 border rounded-lg">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <div class="text-lg font-semibold">
                                الإجمالي المرتجع المعروض: <span id="totalRefund" class="text-green-600">0.00</span>
                            </div>
                            <div id="refundStatus" class="text-sm font-normal mt-1"></div>
                        </div>

                        <div class="text-right">
                            <div class="mb-2 text-sm text-gray-600">
                                الإجمالي الفعلي المدخل: <span id="actualTotal" class="font-semibold">0.00</span> ج.م
                            </div>
                            <button type="submit" id="submitBtn"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                                💾 حفظ المرتجع
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let maxRefund = 0;
        let currentSaleData = null;

        document.getElementById('invoiceInput').addEventListener('keypress', async function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const invoiceId = this.value.trim();
                if (!invoiceId) return;

                const errorEl = document.getElementById('invoiceError');
                const detailsEl = document.getElementById('saleDetails');
                const itemsTable = document.getElementById('itemsTable');

                itemsTable.innerHTML = '';
                errorEl.classList.add('hidden');

                try {
                    const res = await fetch(`/returns/sale-details/${invoiceId}`);
                    if (!res.ok) throw new Error('الفاتورة غير موجودة');
                    const data = await res.json();

                    currentSaleData = data;
                    maxRefund = parseFloat(data.paid) || 0;

                    document.getElementById('saleId').value = data.id;
                    document.getElementById('customerName').textContent = data.customer;
                    document.getElementById('saleTotal').textContent = formatCurrency(data.total);
                    document.getElementById('paidAmount').textContent = formatCurrency(data.paid);
                    document.getElementById('remainingAmount').textContent = formatCurrency(data.remaining);
                    document.getElementById('maxRefundAmount').textContent = formatCurrency(maxRefund);

                    // تعبئة جدول المنتجات
                    data.items.forEach((item, index) => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';
                        row.innerHTML = `
                            <td class="px-4 py-3 border">
                                <div class="font-medium">${item.variant_name}</div>
                                <input type="hidden" name="items[${index}][product_variant_id]" value="${item.variant_id}">
                            </td>
                            <td class="px-4 py-3 border text-gray-700 text-center">
                                ${formatCurrency(item.price)}
                            </td>
                            <td class="px-4 py-3 border text-center">
                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                    ${item.qty}
                                </span>
                            </td>
                            <td class="px-4 py-3 border">
                                <div class="flex items-center gap-2 justify-center">
                                    <button type="button" class="decrement-btn bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded-full flex items-center justify-center" data-index="${index}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <input type="number" name="items[${index}][returned_qty]"
                                        id="qty-${index}"
                                        min="0" max="${item.qty}" step="1" value="0"
                                        class="qty-input border-gray-300 rounded-lg w-16 text-center bg-yellow-50 font-semibold"
                                        data-price="${item.price}" data-index="${index}"
                                        onchange="calculateTotals()">
                                    <button type="button" class="increment-btn bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded-full flex items-center justify-center" data-index="${index}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3 border text-green-600 font-semibold text-center refund-cell" id="refund-${index}">
                                ${formatCurrency(0)}
                            </td>
                        `;
                        itemsTable.appendChild(row);
                    });

                    detailsEl.classList.remove('hidden');
                    calculateTotals();

                    // إضافة event listeners للأزرار
                    attachQuantityButtons();

                } catch (err) {
                    errorEl.textContent = err.message;
                    errorEl.classList.remove('hidden');
                    detailsEl.classList.add('hidden');
                }
            }
        });

        function attachQuantityButtons() {
            document.querySelectorAll('.increment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = this.dataset.index;
                    const input = document.getElementById(`qty-${index}`);
                    if (parseInt(input.value) < parseInt(input.max)) {
                        input.value = parseInt(input.value) + 1;
                        calculateTotals();
                    }
                });
            });

            document.querySelectorAll('.decrement-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = this.dataset.index;
                    const input = document.getElementById(`qty-${index}`);
                    if (parseInt(input.value) > 0) {
                        input.value = parseInt(input.value) - 1;
                        calculateTotals();
                    }
                });
            });
        }

        function calculateTotals() {
            let actualTotal = 0; // الإجمالي الفعلي المدخل
            let displayTotal = 0; // الإجمالي المعروض (لا يتجاوز paid)

            document.querySelectorAll('.qty-input').forEach(input => {
                const price = parseFloat(input.dataset.price);
                const qty = parseInt(input.value) || 0;
                const refund = price * qty;

                const index = input.dataset.index;

                // تحديث خلية المجموع في الجدول
                document.getElementById(`refund-${index}`).textContent = formatCurrency(refund);

                actualTotal += refund;
            });

            // حساب الإجمالي المعروض (لا يتجاوز paid)
            displayTotal = Math.min(actualTotal, maxRefund);

            // تحديث العرض
            document.getElementById('totalRefund').textContent = formatCurrency(displayTotal);
            document.getElementById('actualTotal').textContent = formatCurrency(actualTotal);

            // تحديث حالة الزر والرسالة
            const refundStatusEl = document.getElementById('refundStatus');
            const submitBtn = document.getElementById('submitBtn');

            if (actualTotal === 0) {
                refundStatusEl.innerHTML = `<span class="text-gray-500">⚪ لم يتم تحديد مرتجع</span>`;
                submitBtn.disabled = false;
            } else if (actualTotal <= maxRefund) {
                if (actualTotal === maxRefund) {
                    refundStatusEl.innerHTML = `<span class="text-green-600">✅ وصلت للحد الأقصى المسموح</span>`;
                } else {
                    const remaining = maxRefund - actualTotal;
                    refundStatusEl.innerHTML = `<span class="text-blue-600">📊 يمكنك إضافة ${formatCurrency(remaining)} أخرى</span>`;
                }
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                // إذا تجاوز الحد
                const exceedAmount = actualTotal - maxRefund;
                refundStatusEl.innerHTML = `
                    <span class="text-red-600">
                        ❌ تجاوز الحد المسموح بمقدار ${formatCurrency(exceedAmount)}
                    </span>
                `;
                //submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            // إذا كانت طريقة الاسترداد "رصيد للعميل"، نسمح بأي مبلغ
            const refundMethod = document.getElementById('refundMethod').value;
            if (refundMethod === 'credit') {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                if (actualTotal > maxRefund) {
                    refundStatusEl.innerHTML = `
                        <span class="text-green-600">
                            ✅ مسموح (طريقة الاسترداد: رصيد للعميل)
                        </span>
                    `;
                }
            }
        }

        function formatCurrency(amount) {
            const num = parseFloat(amount) || 0;
            return num.toLocaleString('ar-EG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' ج.م';
        }

        // عند تغيير طريقة الاسترداد
        document.getElementById('refundMethod').addEventListener('change', function() {
            calculateTotals();
        });

        // منع الإرسال إذا كان الإجمالي الفعلي يتجاوز المدفوع
        document.getElementById('returnForm').addEventListener('submit', function(e) {
            const refundMethod = document.getElementById('refundMethod').value;
            const actualTotal = parseFloat(document.getElementById('actualTotal').textContent) || 0;

            // إذا كانت طريقة نقداً أو محفظة وتجاوزت المدفوع
            if (refundMethod !== 'credit' && actualTotal > maxRefund) {
                e.preventDefault();
                alert(`لا يمكن إرجاع مبلغ ${formatCurrency(actualTotal)} لأن العميل دفع ${formatCurrency(maxRefund)} فقط.\n\nغير الكميات أو اختر "رصيد للعميل".`);
                return false;
            }

            return true;
        });
    </script>
@endsection