<!DOCTYPE html>
<html lang="ar" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Print Barcode</title>

    <style>
        @media print {
            @page {
/*
		display: inline-block;
                page-break-before:avoid;
		page-break-after:avoid;
		page-break-inside:avoid;
*/
                /*size: 50mm 25mm;*/
		size: 38mm 25mm;
		/*size:auto;*/
                margin: 0;
            }

            body {
                margin: 0;
            }
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            direction: ltr;
            margin: 0;
            padding: 0;
        }

        .labels-wrapper {
            margin: 0;
            padding: 0;
        }

        .label {
            width: 38mm;
            height: 25mm;
            box-sizing: border-box;
            /* padding: 2mm 3.5mm 2.5mm 3.5mm; */
            padding: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: stretch;
            margin-bottom: 1px;
            overflow: hidden;
            font-size: 7.5pt;
            page-break-after: always;
        }

        .brand {
            font-size: 5.5pt;
            font-weight: 600;
            text-align: left;
            margin: 0;
            line-height: 1;
        }

        .product-name {
            font-weight: 300;
            margin: 0;
            margin-top: 0.5mm;
            font-size: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
        }

        .barcode {
            flex: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0.5mm 0;
            line-height: 3.5;
        }

        .sku-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2mm;
            font-size: 8pt;
            font-weight: 750;
            margin: 2px;
        }
    </style>
</head>

<body onload="window.print()">

    @php
        /** @var \App\Models\ProductVariant $variant */
        $qty = (int) ($variant->stock_qty ?? 1);
        if ($qty <= 0) {
            $qty = 1;
        }

        $code = $variant->barcode ?: $variant->sku;
        // dd($qty);
    @endphp

    <div class="labels-wrapper">
        @for ($i = 0; $i < $qty; $i++)
            <div class="label">
                <p class="brand">Home Wear</p>

                <p class="product-name">{{ $variant->name }}</p>

                <div class="barcode">
                    @if ($code)
                        {!! DNS1D::getBarcodeHTML($code, 'C128', 2.1, 22) !!}
                    @endif
                </div>

                <div class="sku-price">
                    <span>SKU: {{ $code }}</span>
                    <span>{{ number_format($variant->price, 2) }} EGP</span>
                </div>
            </div>
        @endfor
    </div>

</body>

</html>