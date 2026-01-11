<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الأرباح</title>
    <style>
        @media print {
            @page { size: A4; margin: 10mm; }
            body { margin: 0; }
        }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #fff;
            color: #111827;
        }
        h1 { font-size: 20px; margin-bottom: 5px; }
        h2 { font-size: 16px; margin: 15px 0 5px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #e5e7eb; padding: 4px 6px; }
        th { background: #f3f4f6; }
        .text-right { text-align: right; }
        .summary { margin-top: 10px; font-size: 13px; }
        .muted { color: #6b7280; font-size: 11px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    </style>
</head>
<body onload="window.print()">

<div class="header">
    <div>
        <h1>تقرير الأرباح</h1>
        <div class="muted">{{ $periodLabel }}</div>
        <div class="muted">تم الإنشاء في: {{ now()->format('d/m/Y h:i A') }}</div>
    </div>
</div>

<div class="summary">
    <div>إجمالي المبيعات: <strong>{{ number_format($totalRevenue, 2) }} ج.م</strong></div>
    <div>إجمالي الربح: <strong>{{ number_format($totalProfit, 2) }} ج.م</strong></div>
</div>

<h2>تفاصيل الفواتير</h2>

<table>
    <thead>
    <tr>
        <th class="text-right">#</th>
        <th class="text-right">التاريخ</th>
        <th class="text-right">العميل</th>
        <th class="text-right">الإجمالي</th>
        <th class="text-right">الربح</th>
    </tr>
    </thead>
    <tbody>
    @forelse($sales as $sale)
        <tr>
            <td class="text-right">#{{ $sale->id }}</td>
            <td class="text-right">{{ optional($sale->date)->format('d/m/Y h:i') }}</td>
            <td class="text-right">{{ $sale->customer->name ?? 'غير محدد' }}</td>
            <td class="text-right">{{ number_format($sale->total, 2) }} ج.م</td>
            <td class="text-right">{{ number_format($sale->profit ?? 0, 2) }} ج.م</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-right">لا توجد فواتير في هذه الفترة.</td>
        </tr>
    @endforelse
    </tbody>
</table>

</body>
</html>
