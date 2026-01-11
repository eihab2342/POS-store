<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال دفع #{{ $payment->id }}</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body onload="window.print()" class="bg-white text-gray-900">

    <div class="w-[80mm] mx-auto p-4 text-sm leading-relaxed">

        <!-- Header -->
        <div class="text-center border-b border-dashed pb-3 mb-3">
            <h1 class="text-xl font-bold">إيصال دفع</h1>
            <p class="text-gray-600">{{ config('app.name', 'Home Wear') }}</p>
            <p class="text-gray-600 text-xs">تاريخ الطباعة: {{ now()->format('d/m/Y h:i A') }}</p>
        </div>

        <!-- Invoice Info -->
        <div class="space-y-1 mb-3">
            <div class="flex justify-between">
                <span class="font-semibold">رقم الإيصال:</span>
                <span>#{{ $payment->id }}</span>
            </div>

            <div class="flex justify-between">
                <span class="font-semibold">التاريخ:</span>
                <span>{{ $payment->date->format('d/m/Y') }}</span>
            </div>

            <div class="flex justify-between">
                <span class="font-semibold">المورد:</span>
                <span>{{ $payment->supplier->name }}</span>
            </div>

            <div class="flex justify-between">
                <span class="font-semibold">رقم الهاتف:</span>
                <span>{{ $payment->supplier->phone }}</span>
            </div>
        </div>

        <hr class="border-dashed my-3">

        <!-- Payment Details -->
        <div class="space-y-2">
            <h2 class="text-lg font-bold mb-1">تفاصيل الدفع</h2>

            <div class="flex justify-between">
                <span class="font-semibold">طريقة الدفع:</span>
                <span>{{ $payment->method_name }}</span>
            </div>

            @if($payment->purchase)
                <div class="flex justify-between">
                    <span class="font-semibold">فاتورة الشراء:</span>
                    <span>#{{ $payment->purchase->id }}</span>
                </div>
            @endif

            <div class="flex justify-between text-lg font-bold text-green-700 mt-3">
                <span>المبلغ المدفوع:</span>
                <span>{{ number_format($payment->amount, 2) }} ج.م</span>
            </div>

            @if($payment->note)
                <div class="mt-2">
                    <p class="font-semibold">ملاحظة:</p>
                    <p class="text-gray-700">{{ $payment->note }}</p>
                </div>
            @endif
        </div>

        <hr class="border-dashed my-3">

        <!-- Summary -->
        <div class="space-y-1">
            <div class="flex justify-between">
                <span class="font-semibold">الرصيد السابق:</span>
                <span>{{ number_format($previous_balance, 2) }} ج.م</span>
            </div>

            <div class="flex justify-between">
                <span class="font-semibold">الرصيد الحالي:</span>
                <span>{{ number_format($payment->supplier->current_balance, 2) }} ج.م</span>
            </div>
        </div>

        <hr class="border-dashed my-3">

        <!-- Footer -->
        <div class="text-center mt-3">
            <p class="font-semibold text-gray-700">شكراً لتعاملكم معنا</p>
        </div>

    </div>

</body>

</html>