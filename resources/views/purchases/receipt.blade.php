<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>فاتورة شراء من مورد</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-black py-4">
    <div class="max-w-sm mx-auto bg-white border border-gray-300 rounded-lg p-4 text-xs font-mono">

        <!-- الهيدر -->
        <div class="text-center border-b border-gray-400 pb-2 mb-2">
            <h1 class="text-lg font-bold">YAZAN</h1>
            <h1 class="text-lg font-bold">فاتورة شراء من مورد</h1>
            <p>رقم: <b>{{ $purchase->id }}</b>
                @if($purchase->invoice_no)
                | رقم مورد: <b>{{ $purchase->invoice_no }}</b>
                @endif
            </p>
            <p>التاريخ: <b>{{ $purchase->date }}</b></p>
            <p>المورد: <b>{{ $purchase->supplier?->name }}</b></p>
        </div>

        <!-- جدول المنتجات -->
        <table class="w-full border-collapse text-xs">
            <thead>
                <tr class="border-b border-gray-400">
                    <th class="p-1 text-right">الصنف</th>
                    <th class="p-1 text-center">السعر</th>
                    <th class="p-1 text-center">الكمية</th>
                    <th class="p-1 text-center">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $it)
                <tr class="border-b border-dashed border-gray-300">
                    <td class="p-1 text-right">
                        {{ $it->variant?->product?->name }}
                        @if($it->variant?->size) - {{ $it->variant->size }}@endif
                        @if($it->variant?->color) {{ $it->variant->color }}@endif
                        @if($it->variant?->sku) ({{ $it->variant->sku }}) @endif
                    </td>
                    <td class="p-1 text-center">{{ $it->qty }}</td>
                    <td class="p-1 text-center">{{ number_format($it->cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- الإجمالي -->
        <div class="text-right mt-2 border-t border-gray-400 pt-2">
            <p class="text-sm font-bold">
            <p class="text-base font-bold">إجمالي الفاتورة: {{ number_format($purchase->total_cost, 2) }} ج.م</p>
            </p>
        </div>

        <!-- الفوتر -->
        <div class="text-center mt-4 border-t border-gray-300 pt-2 text-[10px]">
            <p>شكراً لتسوقكم من متجرنا</p>
            <p>📞 00212594901 | 📍 المنصورة - مصر</p>
        </div>
    </div>

    <script>
    window.print();
    </script>
</body>

</html>