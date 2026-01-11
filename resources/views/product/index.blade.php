@extends('layouts.app')

@section('title', 'المنتجات والأصناف')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">إدارة الأصناف</h1>

            <div class="flex gap-3">

                <!-- زر تعديل كميات كل المنتجات -->
                <a href="{{ route('variants.bulk.edit') }}"
                    class="bg-green-600 hover:bg-green-800 text-white px-6 py-3 rounded-lg">
                    تعديل الكميات (جرد كامل)
                </a>

                <!-- زر إضافة صنف -->
                <a href="{{ route('variants.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                    + إضافة صنف جديد
                </a>

            </div>
        </div>

        <!-- Search & Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap items-center gap-4">

            <!-- Live Search Input -->
            <input type="text" id="live-search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو SKU..."
                class="flex-1 px-4 py-2 border rounded-lg">

            <!-- زر مسح -->
            <button id="clear-search" class="text-red-600 hover:underline">مسح</button>

            <!-- Low Stock Filter -->
            <button id="filter-low-stock" class="px-6 py-2 rounded-lg 
                                              {{ request('low_stock') ? 'bg-red-600 text-white' : 'bg-gray-200' }}">
                مخزون منخفض
            </button>
        </div>

        <!-- Bulk Form -->
        <form id="bulk-form" method="POST" action="{{ route('variants.print.inventory') }}">
            @csrf

            <!-- Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right"><input type="checkbox" id="select-all"></th>
                            <th class="px-6 py-3 text-right">SKU</th>
                            <th class="px-6 py-3 text-right">المنتج</th>
                            <th class="px-6 py-3 text-right">المقاس</th>
                            <!-- <th class="px-6 py-3 text-right">اللون</th> -->
		            <th class="px-6 py-3 text-right">جملة</th>
                            <th class="px-6 py-3 text-right">السعر</th>
                            <th class="px-6 py-3 text-right">مكسب</th>
                            <th class="px-6 py-3 text-right">المخزون</th>
                            <th class="px-6 py-3 text-center">إجراءات</th>
                        </tr>
                    </thead>

                    <tbody id="variants-body">
                        @include('product.partials.rows', ['variants' => $variants])
                    </tbody>
                </table>
            </div>

            <!-- Footer Actions -->
            <div class="mt-4 flex justify-between items-center">
                <button type="submit" id="print-inventory-btn"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg disabled:opacity-50" disabled>
                    طباعة كشف جرد المختارين
                </button>

                <div id="pagination">
                    {{ $variants->appends(request()->query())->links() }}
                </div>
            </div>
        </form>


        <!-- Hidden delete forms -->
        @foreach($variants as $variant)
            <form id="delete-variant-{{ $variant->id }}" action="{{ route('variants.destroy', $variant) }}" method="POST"
                style="display:none">
                @csrf @method('DELETE')
            </form>
        @endforeach

    </div>


    <!-- Live Search Script -->
    <script>
        let timeout = null;
        let lowStock = {{ request('low_stock') ? 1 : 0 }};

        const searchInput = document.getElementById('live-search');
        const tableBody = document.getElementById('variants-body');
        const pagination = document.getElementById('pagination');

        // ========== LIVE SEARCH ==========
        searchInput.addEventListener('keyup', function () {
            clearTimeout(timeout);

            timeout = setTimeout(() => {
                fetchResults(this.value, lowStock);
            }, 250);
        });

        // ========== CLEAR SEARCH ==========
        document.getElementById('clear-search').onclick = function () {
            searchInput.value = "";
            fetchResults("", lowStock);
        };

        // ========== FILTER LOW STOCK ==========
        document.getElementById('filter-low-stock').onclick = function () {
            lowStock = lowStock ? 0 : 1;
            fetchResults(searchInput.value, lowStock);
        };

        // Fetch function
        function fetchResults(search = "", low_stock = 0) {

            fetch(`/variants/live-search?search=${search}&low_stock=${low_stock}`)
                .then(res => res.text())
                .then(html => {
                    tableBody.innerHTML = html;

                    updateCheckboxEvents();
                    pagination.innerHTML = "";
                });
        }


        // ========== Checkboxes Handling ==========
        const printBtn = document.getElementById('print-inventory-btn');

        function updateCheckboxEvents() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', togglePrintButton);
            });
        }

        document.getElementById('select-all').addEventListener('change', function (e) {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = e.target.checked;
            });
            togglePrintButton();
        });

        function togglePrintButton() {
            const checked = document.querySelectorAll('.row-checkbox:checked').length;
            printBtn.disabled = checked === 0;
        }


        // ========== Bulk Print Behavior ==========
        document.getElementById('bulk-form').addEventListener('submit', function (e) {
            if (e.submitter && e.submitter.getAttribute('form')?.startsWith('delete-variant-')) {
                this.removeAttribute('target');
                return;
            }
            this.target = '_blank';
        });

        updateCheckboxEvents();
    </script>

@endsection