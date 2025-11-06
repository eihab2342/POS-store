<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>فاتورة بيع</title>
    <style>
        /* خلي الجسم عادي */
        body {
            margin: 0;
            background: #fff;
            font-family: sans-serif;
            font-size: 12px;
        }

        /* ده عرض الطابعة بس، مفيش ارتفاع */
        .receipt {
            width: 78mm;
            /* أو 80mm حسب الطابعة */
            margin: 0 auto;
            padding: 8px 6px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 2px 0;
        }

        tr.item-row td {
            border-bottom: 1px dashed #ddd;
        }

        /* الجزء المهم بقا 👇 */
        @media print {
            @page {
                /* ما تقولش للطابعة طولك كذا */
                size: auto;
                margin: 2mm;
            }

            body {
                margin: 0;
            }

            .receipt {
                /* مفيش height هنا */
                width: 78mm;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <h3 style="text-align:center; margin:0 0 4px;">YAZAN</h3>
        <p style="margin:0 0 4px; text-align:center;">فاتورة بيع</p>
        <p style="margin:0 0 4px;">
            رقم الفاتورة: <strong>{{ $sale->id }}</strong><br>
            التاريخ: <strong>{{ $sale->created_at->format('Y-m-d H:i') }}</strong>
        </p>

        <table>
            <thead>
                <tr>
                    <th style="text-align:right;">الصنف</th>
                    <th style="text-align:center;">سعر</th>
                    <th style="text-align:center;">كمية</th>
                    <th style="text-align:center;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr class="item-row">
                        <td style="text-align:right;">
                            {{ $item->variant->product->name ?? $item->name ?? 'صنف' }}
                        </td>
                        <td style="text-align:center;">{{ number_format($item->price, 2) }}</td>
                        <td style="text-align:center;">{{ $item->qty }}</td>
                        <td style="text-align:center;">{{ number_format($item->price * $item->qty, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="text-align:right; margin:6px 0 0;">
            الإجمالي: <strong>{{ number_format($sale->total, 2) }}</strong>
        </p>
        <p style="text-align:center; margin:6px 0 0; font-size:10px;">شكراً لتعاملكم</p>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>

</html>