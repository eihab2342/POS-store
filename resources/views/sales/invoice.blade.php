@extends('layouts.app')

@section('title', 'فاتورة مبيعات')
@section('content')
    <div class="space-y-6">
        {{-- فورم إضافة صنف عن طريق الـ SKU --}}
        <div class="bg-white rounded-xl shadow p-4 md:p-6">
            <h2 class="text-xl font-semibold mb-4">إضافة صنف بالفاتورة</h2>
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="sku" class="block mb-1 text-sm font-medium text-gray-700">كود الصنف (SKU)</label>
                    <input
                        type="text"
                        name="sku"
                        id="sku"
                        class="w-full h-11 px-3 rounded-xl border border-gray-300 bg-gray-50
                               text-gray-800 placeholder:text-gray-400
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                               transition"
                        placeholder="امسح الباركود أو اكتب كود الصنف"
                        autofocus
                    >
                    <div id="sku-error" class="mt-1 text-sm text-red-600 hidden"></div>
                </div>
                <div class="flex items-end">
                    <button
                        type="button"
                        id="add-item-btn"
                        class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-indigo-600
                               text-white font-medium shadow-sm hover:bg-indigo-700 active:scale-[0.98] transition"
                    >
                        إضافة
                    </button>
                </div>
            </div>
        </div>

        {{-- السلة --}}
        <div class="bg-white rounded-xl shadow p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">السلة الحالية</h2>
                <button
                    type="button"
                    id="reset-cart-btn"
                    class="px-4 py-2 rounded-xl border border-red-500 text-red-600 text-sm font-medium
                           bg-white hover:bg-red-50 transition hidden"
                >
                    تفريغ السلة / فاتورة جديدة
                </button>
            </div>
            <div id="cart-container">
                <p class="text-gray-500">السلة فارغة حاليًا.</p>
            </div>
        </div>

        {{-- فورم إنهاء الفاتورة (Checkout) --}}
        <div class="bg-white rounded-xl shadow p-4 md:p-6 hidden" id="checkout-section">
            <h2 class="text-xl font-semibold mb-4">بيانات الفاتورة والدفع</h2>
            <form id="checkout-form" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">رقم الهاتف (اختياري)</label>
                        <input
                            type="text"
                            name="phone"
                            id="phone"
                            class="w-full h-11 px-3 rounded-xl border border-gray-300 bg-gray-50
                                   text-gray-800 placeholder:text-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                   transition"
                            placeholder="  أسم/ هاتف  العميل"
                        >
                        <div id="phone-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">طريقة الدفع</label>
                        <select
                            name="payment_method"
                            id="payment_method"
                            class="w-full h-11 px-3 rounded-xl border border-gray-300 bg-gray-50
                                   text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                   transition"
                        >
                            <option value="cash">كاش</option>
                            <option value="wallet">محفظة</option>
                            <option value="instapay">إنستاباي</option>
                        </select>
                        <div id="payment_method-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">حالة الدفع</label>
                        <select
                            name="payment_status"
                            id="payment_status"
                            class="w-full h-11 px-3 rounded-xl border border-gray-300 bg-gray-50
                                   text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                   transition"
                        >
                            <option value="full">دفع كامل</option>
                            <option value="discount">مع خصم</option>
                            <option value="credit">دفع آجل</option>
                        </select>
                        <div id="payment_status-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">إجمالي الفاتورة بعد الخصم</label>
                        <input
                            type="text"
                            id="totalDisplay"
                            value="0.00"
                            disabled
                            class="w-full h-11 px-3 rounded-xl border border-gray-200 bg-gray-100 text-gray-800"
                        >
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">الخصم</label>
                        <input
                            type="number"
                            name="discount"
                            id="discountInput"
                            step="0.01"
                            min="0"
                            value="0"
                            class="w-full h-11 px-3 rounded-xl border border-gray-300 bg-gray-50
                                   text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                   transition"
                        >
                        <div id="discount-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">المبلغ المدفوع</label>
                        <input
                            type="number"
                            name="paid"
                            id="paidInput"
                            step="0.01"
                            min="0"
                            value="0"
                            class="w-full h-11 px-3 rounded-xl border border-gray-300 bg-gray-50
                                   text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                   transition"
                        >
                        <div id="paid-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            المتبقي
                            <span class="text-xs text-gray-400">(حسب حالة الدفع)</span>
                        </label>
                        <input
                            type="text"
                            id="remainingDisplay"
                            value="0.00"
                            disabled
                            class="w-full h-11 px-3 rounded-xl border border-gray-200 bg-gray-100 text-gray-800"
                        >
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        تأكد من البيانات قبل إنهاء الفاتورة.
                    </div>
                    <button
                        type="submit"
                        id="checkout-btn"
                        class="inline-flex items-center px-6 py-2.5 rounded-xl bg-green-600
                               text-white text-base font-semibold shadow-sm hover:bg-green-700 active:scale-[0.98] transition"
                    >
                        إنهاء الفاتورة
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// إنشاء كائن SalesInvoice لجعل الدوال متاحة عالمياً
window.SalesInvoice = (function() {
    // عناصر DOM
    let skuInput, addItemBtn, cartContainer, resetCartBtn, checkoutSection, checkoutForm;
    let discountInput, paidInput, paymentStatusSelect, totalDisplay, remainingDisplay, checkoutBtn;

    // متغيرات التطبيق
    let cart = [];
    let paidTouchedByUser = false;

    // تهيئة التطبيق
    function init() {
        // ربط عناصر DOM
        skuInput = document.getElementById('sku');
        addItemBtn = document.getElementById('add-item-btn');
        cartContainer = document.getElementById('cart-container');
        resetCartBtn = document.getElementById('reset-cart-btn');
        checkoutSection = document.getElementById('checkout-section');
        checkoutForm = document.getElementById('checkout-form');
        discountInput = document.getElementById('discountInput');
        paidInput = document.getElementById('paidInput');
        paymentStatusSelect = document.getElementById('payment_status');
        totalDisplay = document.getElementById('totalDisplay');
        remainingDisplay = document.getElementById('remainingDisplay');
        checkoutBtn = document.getElementById('checkout-btn');

        // إضافة event listeners
        addItemBtn.addEventListener('click', addItem);
        skuInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addItem();
            }
        });

        resetCartBtn.addEventListener('click', resetCart);
        checkoutForm.addEventListener('submit', checkout);

        // تحديث الإجماليات
        if (discountInput) discountInput.addEventListener('input', updateTotals);
        if (paidInput) {
            paidInput.addEventListener('input', function() {
                paidTouchedByUser = true;
                updateTotals();
            });
        }
        if (paymentStatusSelect) paymentStatusSelect.addEventListener('change', updateTotals);

        // تحميل السلة عند بدء التحميل
        loadCart();
    }

    // تحميل السلة
    async function loadCart() {
        try {
            showLoading('جاري تحميل السلة...');
            const response = await fetch('{{ route("sales.invoice.cart") }}');
            if (response.ok) {
                const data = await response.json();
                if (data.cart && data.cart.length > 0) {
                    cart = data.cart;
                    renderCart();
                    updateTotals();
                    resetCartBtn.classList.remove('hidden');
                    checkoutSection.classList.remove('hidden');
                    showToast('تم تحميل السلة', 'success');
                } else {
                    cartContainer.innerHTML = '<p class="text-gray-500">السلة فارغة حاليًا.</p>';
                    resetCartBtn.classList.add('hidden');
                    checkoutSection.classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Error loading cart:', error);
            showToast('حدث خطأ في تحميل السلة', 'error');
        } finally {
            hideLoading();
        }
    }

    // إضافة صنف
    async function addItem() {
        const sku = skuInput.value.trim();
        if (!sku) {
            showError('sku', 'يرجى إدخال كود الصنف');
            return;
        }

        clearError('sku');

        try {
            showLoading('جاري إضافة الصنف...');
            const response = await fetch('{{ route("sales.invoice.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ sku: sku })
            });

            const data = await response.json();

            if (data.ok) {
                cart = data.cart;
                renderCart();
                updateTotals();
                skuInput.value = '';
                skuInput.focus();
                resetCartBtn.classList.remove('hidden');
                checkoutSection.classList.remove('hidden');
                showToast(data.message || 'تمت إضافة الصنف بنجاح', 'success');
            } else {
                showError('sku', data.message);
                showToast(data.message, 'error', 'خطأ في الإضافة');
            }
        } catch (error) {
            console.error('Error adding item:', error);
            showError('sku', 'حدث خطأ في الإضافة');
            showToast('حدث خطأ في إضافة الصنف', 'error');
        } finally {
            hideLoading();
        }
    }

    // تحديث الكمية
    async function updateQuantity(index, newQty) {
        if (newQty < 1) {
            newQty = 1;
        }

        if (newQty > cart[index].stock_qty) {
            showToast('الكمية المطلوبة غير متوفرة في المخزون', 'error');
            return;
        }

        try {
            const response = await fetch('{{ route("sales.invoice.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    index: index,
                    qty: newQty
                })
            });

            const data = await response.json();
            if (data.ok) {
                cart = data.cart;
                renderCart();
                updateTotals();
                showToast('تم تحديث الكمية بنجاح', 'success');
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
            showToast('حدث خطأ في تحديث الكمية', 'error');
        }
    }

    // حذف صنف
    async function removeItem(index) {
        try {
            const confirmed = await showConfirm('هل تريد حذف هذا الصنف من الفاتورة؟', 'نعم، احذف', 'إلغاء');
            if (!confirmed) return;

            showLoading('جاري حذف الصنف...');
            const response = await fetch('{{ route("sales.invoice.remove") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ index: index })
            });

            const data = await response.json();
            if (data.ok) {
                cart = data.cart;
                if (cart.length === 0) {
                    cartContainer.innerHTML = '<p class="text-gray-500">السلة فارغة حاليًا.</p>';
                    resetCartBtn.classList.add('hidden');
                    checkoutSection.classList.add('hidden');
                } else {
                    renderCart();
                    updateTotals();
                }
                showToast('تم حذف الصنف بنجاح', 'success');
            }
        } catch (error) {
            console.error('Error removing item:', error);
            showToast('حدث خطأ في حذف الصنف', 'error');
        } finally {
            hideLoading();
        }
    }

    // تفريغ السلة
    async function resetCart() {
        try {
            const confirmed = await showConfirm('هل أنت متأكد من تفريغ السلة بالكامل؟', 'نعم، فرّغ السلة', 'إلغاء');
            if (!confirmed) return;

            showLoading('جاري تفريغ السلة...');
            const response = await fetch('{{ route("sales.invoice.reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();
            if (data.ok) {
                cart = [];
                cartContainer.innerHTML = '<p class="text-gray-500">السلة فارغة حاليًا.</p>';
                resetCartBtn.classList.add('hidden');
                checkoutSection.classList.add('hidden');
                updateTotals();
                showToast(data.message || 'تم تفريغ السلة بنجاح', 'success');
            }
        } catch (error) {
            console.error('Error resetting cart:', error);
            showToast('حدث خطأ في تفريغ السلة', 'error');
        } finally {
            hideLoading();
        }
    }

    // إنهاء الفاتورة
    async function checkout(e) {
        e.preventDefault();

        if (cart.length === 0) {
            showToast('السلة فارغة، أضف أصناف أولاً', 'warning');
            return;
        }

        // التحقق من المخزون قبل المتابعة
        for (const item of cart) {
            if (item.qty > item.stock_qty) {
                showToast(`الكمية المطلوبة للصنف ${item.name} غير متوفرة في المخزون`, 'error');
                return;
            }
        }

        // تأكيد إنهاء الفاتورة
        const confirmed = await showConfirm(
            'هل تريد إنهاء الفاتورة وتأكيد البيع؟',
            'نعم، أنهي الفاتورة',
            'إلغاء'
        );

        if (!confirmed) return;

        // تجميع بيانات الفاتورة
        const cartData = cart.map(item => ({
            variant_id: item.variant_id,
            qty: item.qty
        }));

        const formData = {
            phone: document.getElementById('phone').value,
            payment_method: document.getElementById('payment_method').value,
            payment_status: paymentStatusSelect.value,
            discount: discountInput.value,
            paid: paidInput.value,
            cart: cartData
        };

        // إظهار مؤشر التحميل
        const originalBtnText = checkoutBtn.innerHTML;
        checkoutBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            جاري المعالجة...
        `;
        checkoutBtn.disabled = true;

        try {
            const response = await fetch('{{ route("sales.invoice.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.ok) {
                // إظهار إشعار النجاح
                await Swal.fire({
                    title: 'تم بنجاح!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'موافق',
                    customClass: {
                        popup: 'rounded-xl',
                        title: 'font-medium'
                    }
                });

                // إعادة تعيين كل شيء
                cart = [];
                cartContainer.innerHTML = '<p class="text-gray-500">السلة فارغة حاليًا.</p>';
                resetCartBtn.classList.add('hidden');
                checkoutSection.classList.add('hidden');
                checkoutForm.reset();
                discountInput.value = 0;
                paidInput.value = 0;
                updateTotals();

                // فتح الإيصال إذا كان موجودًا
                if (data.receipt_url) {
                    window.open(data.receipt_url, '_blank');
                }
            } else {
                // عرض الأخطاء
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showError(field, data.errors[field][0]);
                    });
                    showToast('يوجد أخطاء في البيانات المدخلة', 'error');
                } else {
                    showToast(data.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error during checkout:', error);
            showToast('حدث خطأ أثناء إنهاء الفاتورة', 'error');
        } finally {
            checkoutBtn.innerHTML = originalBtnText;
            checkoutBtn.disabled = false;
        }
    }

    // عرض السلة
    function renderCart() {
        if (cart.length === 0) {
            cartContainer.innerHTML = '<p class="text-gray-500">السلة فارغة حاليًا.</p>';
            return;
        }

        let html = `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">كود الصنف</th>
                            <th class="px-4 py-3">اسم الصنف</th>
                            <th class="px-4 py-3">المتاح بالمخزون</th>
                            <th class="px-4 py-3">الكمية</th>
                            <th class="px-4 py-3">السعر</th>
                            <th class="px-4 py-3">الإجمالي</th>
                            <th class="px-4 py-3">حذف</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
        `;

        cart.forEach((item, index) => {
            const rowTotal = item.price * item.qty;
            html += `
                <tr>
                    <td class="px-4 py-3 text-gray-700">${index + 1}</td>
                    <td class="px-4 py-3 font-mono text-gray-800">${item.sku}</td>
                    <td class="px-4 py-3 text-gray-800">${item.name}</td>
                    <td class="px-4 py-3 text-gray-600">${item.stock_qty}</td>
                    <td class="px-4 py-3">
                        <input
                            type="number"
                            class="w-24 h-10 px-2 rounded-lg border border-gray-300 bg-gray-50
                                   text-center text-gray-800 qty-input
                                   focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500
                                   transition"
                            value="${item.qty}"
                            min="1"
                            max="${item.stock_qty}"
                            onchange="window.SalesInvoice.updateQuantity(${index}, this.value)"
                        >
                    </td>
                    <td class="px-4 py-3 text-gray-800">${item.price.toFixed(2)}</td>
                    <td class="px-4 py-3 text-gray-800 row-total-${index}">${rowTotal.toFixed(2)}</td>
                    <td class="px-4 py-3">
                        <button
                            type="button"
                            onclick="window.SalesInvoice.removeItem(${index})"
                            class="text-red-600 hover:text-red-800 text-sm font-medium"
                        >
                            حذف
                        </button>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-xl px-4 py-3">
                    <p class="text-xs text-gray-500">الإجمالي قبل الخصم</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">
                        <span id="subtotalDisplay">0.00</span>
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl px-4 py-3">
                    <p class="text-xs text-gray-500">قيمة الخصم</p>
                    <p class="mt-1 text-lg font-semibold text-amber-600">
                        <span id="discountSummary">0.00</span>
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl px-4 py-3">
                    <p class="text-xs text-gray-500">الإجمالي بعد الخصم</p>
                    <p class="mt-1 text-lg font-semibold text-emerald-700">
                        <span id="totalAfterDiscountDisplay">0.00</span>
                    </p>
                </div>
            </div>
        `;

        cartContainer.innerHTML = html;
    }

    // تحديث الإجماليات
    function updateTotals() {
        let subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        let discount = parseFloat(discountInput?.value) || 0;
        let total = Math.max(0, subtotal - discount);
        let paymentStatus = paymentStatusSelect?.value || 'full';

        // تحديث paid إذا لم يكن المستخدم لمسه
        if (paymentStatus !== 'credit' && !paidTouchedByUser && paidInput) {
            paidInput.value = total.toFixed(2);
        }

        let paid = parseFloat(paidInput?.value) || 0;
        let remaining = 0;

        if (paymentStatus === 'credit') {
            remaining = Math.max(total - paid, 0);
        } else {
            remaining = Math.max(paid - total, 0);
        }

        // تحديث العرض
        const subtotalEl = document.getElementById('subtotalDisplay');
        const discountSummaryEl = document.getElementById('discountSummary');
        const totalAfterDiscountEl = document.getElementById('totalAfterDiscountDisplay');

        if (subtotalEl) subtotalEl.textContent = subtotal.toFixed(2);
        if (discountSummaryEl) discountSummaryEl.textContent = discount.toFixed(2);
        if (totalAfterDiscountEl) totalAfterDiscountEl.textContent = total.toFixed(2);

        if (totalDisplay) totalDisplay.value = total.toFixed(2);
        if (remainingDisplay) remainingDisplay.value = remaining.toFixed(2);

        // تحديث إجمالي كل صف
        cart.forEach((item, index) => {
            const rowTotal = item.price * item.qty;
            const rowTotalEl = document.querySelector(`.row-total-${index}`);
            if (rowTotalEl) {
                rowTotalEl.textContent = rowTotal.toFixed(2);
            }
        });
    }

    // عرض خطأ
    function showError(field, message) {
        const errorEl = document.getElementById(`${field}-error`);
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        }
    }

    // مسح خطأ
    function clearError(field) {
        const errorEl = document.getElementById(`${field}-error`);
        if (errorEl) {
            errorEl.textContent = '';
            errorEl.classList.add('hidden');
        }
    }

    // دوال الإشعارات (استخدام الدوال العالمية من layout)
    function showToast(message, type = 'info', title = null) {
        if (window.showToast) {
            window.showToast(message, type, title);
        } else {
            // fallback إذا لم تكن الدوال متاحة
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-start',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            let icon = 'info';
            if (type === 'success') icon = 'success';
            if (type === 'error') icon = 'error';
            if (type === 'warning') icon = 'warning';

            Toast.fire({ icon, title: title || message });
        }
    }

    async function showConfirm(message, confirmButtonText = 'نعم', cancelButtonText = 'لا') {
        if (window.showConfirm) {
            return await window.showConfirm(message, confirmButtonText, cancelButtonText);
        } else {
            const result = await Swal.fire({
                title: 'تأكيد',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
                reverseButtons: true
            });
            return result.isConfirmed;
        }
    }

    function showLoading(message = 'جاري المعالجة...') {
        if (window.showLoading) {
            window.showLoading(message);
        } else {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });
        }
    }

    function hideLoading() {
        if (window.hideLoading) {
            window.hideLoading();
        } else {
            Swal.close();
        }
    }

    // جعل الدوال متاحة للاستخدام من الـ HTML
    return {
        init,
        addItem,
        updateQuantity,
        removeItem,
        resetCart,
        checkout,
        loadCart,
        updateTotals,
        showError,
        clearError
    };
})();

// تهيئة التطبيق عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    window.SalesInvoice.init();
});
</script>
@endpush