<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $sale->id }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: #ffffff;
            font-size: 12px;
            direction: rtl;
        }

        .receipt {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 10px;
            line-height: 1.4;
        }

        /* Utilities */
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }

        .bold  { font-weight: bold; }
        .large { font-size: 17px; }
        .xs    { font-size: 10px; }
        .sm    { font-size: 11px; }
        .md    { font-size: 12px; }
        .lg    { font-size: 14px; }

        .warning { color: #c62828; font-weight: bold; font-size: 18px; }
        .success { color: #2e7d32; font-weight: bold; font-size: 16px; }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 13px;
            margin: 2px 0;
        }

        /* Info rows */
        .info-block {
            font-size: 11.5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 13px;
            font-weight: 600;
        }

        .info-label {
            font-weight: bold;
        }

        /* Divider */
        .divider {
            border-top: 1px dashed #000;
            margin: 9px 0;
        }

        /* Items table (grid style) */
        .items-header,
        .item-row {
            display: grid;
            grid-template-columns: 3.2fr 1fr 1fr 2fr;
            column-gap: 4px;
            align-items: center;
        }

        .items-header {
            font-weight: bold;
            border-bottom: 1.5px solid #000;
            padding: 6px 0 4px;
            font-size: 11.5px;
        }

        .item-row {
            padding: 5px 0;
            font-size: 11px;
            border-bottom: 1px dotted #aaa;
        }

        .item-name {
            font-size: 10.2px;
        }

        /* Totals */
        .totals {
            margin-top: 10px;
            border-top: 2px solid #000;
            padding-top: 8px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 13px;
        }

        .total-row.final {
            font-size: 18px;
            font-weight: bold;
            border-top: 3px double #000;
            padding-top: 9px;
            margin-top: 10px;
        }

        /* Payment box */
        .payment-box {
            background: #f5f5f5;
            border: 2px solid #000;
            padding: 10px 8px;
            margin: 14px 0;
            text-align: center;
            border-radius: 4px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 13px;
        }

        /* Credit highlight */
        .credit-box {
            text-align: center;
            padding: 10px;
            background: #ffebee;
            border: 3px double #c62828;
            font-weight: bold;
            font-size: 17px;
            color: #c62828;
            margin: 10px 0;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 11px;
        }

        .footer-separator {
            margin-top: 8px;
            margin-bottom: 4px;
        }

        /* طباعة مثالية */
        @media print {
            body,
            .receipt {
                margin: 0 !important;
                padding: 8px 10px !important;
                width: 80mm;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="receipt">

        {{-- Header --}}
        <div class="header">
            <h1>Home Wear</h1>
            <p>سلكا • المنصورة • الدقهلية</p>
            <p>تليفون: 01022789042</p>
        </div>

        {{-- Invoice Info --}}
        <div class="info-block">
            <div class="info-row">
                <span class="info-label">الفاتورة:</span>
                <span class="bold large">#{{ $sale->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">التاريخ:</span>
                <span>{{ $sale->date->format('d/m/Y h:i A') }}</span>
            </div>
            {{-- <div class="info-row">
                <span class="info-label">الكاشير:</span>
                <span>{{ $sale->cashier?->name ?? '---' }}</span>
            </div> --}}
            <div class="info-row">
                <span class="info-label">العميل:</span>
                <span>{{ $sale->customer_data ?? $sale->customer?->phone ?? 'عميل نقدي' }}</span>
            </div>
        </div>

        <div class="divider"></div>

        {{-- Items --}}
        <div class="items-header">
            <div>الصنف</div>
            <div class="text-center">كمية</div>
            <div class="text-right">السعر</div>
            <div class="text-right">الإجمالي</div>
        </div>

        @foreach ($sale->items as $item)
            <div class="item-row">
                <div class="item-name">
                    {{ Str::limit($item->productVariant?->name ?? 'محذوف', 22) . $item->productVariant?->sku  }}
                </div>
                <div class="text-center">{{ $item->qty }}</div>
                <div class="text-right">{{ number_format($item->price, 1) }}</div>
                <div class="text-right bold">{{ number_format($item->qty * $item->price, 1) }}</div>
            </div>
        @endforeach

        <div class="divider"></div>

        {{-- Totals --}}
        <div class="totals">
            <div class="total-row">
                <span>الإجمالي قبل الخصم:</span>
                <span>{{ number_format($sale->subtotal, 1) }} ج.م</span>
            </div>

            @if ($sale->discount > 0)
                <div class="total-row">
                    <span>الخصم:</span>
                    <span class="bold">-{{ number_format($sale->discount, 1) }} ج.م</span>
                </div>
            @endif

            <div class="total-row final">
                <span>الصافي بعد الخصم:</span>
                <span class="large">{{ number_format($sale->total, 1) }} ج.م</span>
            </div>
        </div>

        {{-- Payment Box --}}
        @php
            $diff = $sale->paid - $sale->total;
        @endphp

        <div class="payment-box">
            <div class="payment-row">
                <span class="bold">طريقة الدفع:</span>
                <span class="bold large">
                    @switch($sale->payment_method)
                        @case('cash') كاش @break
                        @case('wallet') محفظة @break
                        @case('instapay') إنستاباي @break
                        @default {{ $sale->payment_method }}
                    @endswitch
                </span>
            </div>

            <div class="payment-row">
                <span class="bold">نوع الفاتورة:</span>
                <span class="bold lg">
                    @switch($sale->sale_type)
                        @case('full') دفع كامل @break
                        @case('discount') مع خصم @break
                        @case('credit') آجل @break
                        @default {{ $sale->sale_type }}
                    @endswitch
                </span>
            </div>

            <div class="payment-row">
                <span class="bold">المدفوع:</span>
                <span class="large">{{ number_format($sale->paid, 1) }} ج.م</span>
            </div>

            @if ($diff < -0.01)
                <div class="mt-2 warning">
                    دين على العميل: {{ number_format(abs($diff), 1) }} ج.م
                </div>
            @elseif ($diff > 0.01)
                <div class="mt-2 success">
                    الباقي للعميل: {{ number_format($diff, 1) }} ج.م
                </div>
            @else
                <div class="mt-2">
                    تم الدفع كاملاً
                </div>
            @endif
        </div>

        {{-- Credit Highlight --}}
        @if ($sale->sale_type === 'credit' || $diff < -0.01)
            <div class="credit-box">
                فاتورة آجل • المتبقي على العميل:
                {{ number_format(abs($diff), 1) }} ج.م
            </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p class="bold">شكراً لزيارتكم</p>
            <p>نتمنى لكم يوماً سعيداً</p>

            <div class="footer-separator">
                <p>==============================</p>
            </div>

            <p class="xs">طبعت في: {{ now()->format('d/m/Y h:i A') }}</p>
        </div>

    </div>
</body>
</html>
