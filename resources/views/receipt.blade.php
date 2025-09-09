<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>فاتورة بيع</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-black py-4">
    <div class="max-w-sm mx-auto bg-white border border-gray-300 rounded-lg p-4 text-xs font-mono">

        <!-- الهيدر -->
        <div class="text-center border-b border-gray-400 pb-2 mb-2">
            <h1 class="text-lg font-bold">YAZAN</h1>
            <h2 class="text-lg font-bold">فاتورة بيع</h2>
            <p class="mt-1">
                رقم الفاتورة: <span class="font-semibold">{{ $sale->id }}</span><br>
                التاريخ: <span class="font-semibold">{{ $sale->date }}</span>
            </p>
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
                @foreach($sale->items as $item)
                <tr class="border-b border-dashed border-gray-300">
                    <td class="p-1 text-right">
                        {{ $item->variant->product->name }}
                        @if($item->variant->size) - {{ $item->variant->size }}@endif
                        @if($item->variant->color) {{ $item->variant->color }}@endif
                    </td>
                    <td class="p-1 text-center">{{ number_format($item->price, 2) }}</td>
                    <td class="p-1 text-center">{{ $item->qty }}</td>
                    <td class="p-1 text-center font-semibold">{{ number_format($item->price * $item->qty, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right mt-2 border-t border-gray-400 pt-2">
            <p class="text-sm font-bold">
                هاتف العميل: {{ $sale->customer->phone ?? '---' }}
            </p>
        </div>

        <!-- الإجمالي -->
        <div class="text-right mt-2 border-t border-gray-400 pt-2">
            <p class="text-sm font-bold">
                الإجمالي: {{ number_format($sale->total, 2) }} ج.م
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