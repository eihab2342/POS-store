<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف الجرد</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            padding: 20px;
            background: #fff;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-btn {
            background: #10b981;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }

        .print-btn:hover {
            background: #059669;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #000;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .header p {
            font-size: 14px;
            color: #6b7280;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background: #1f2937;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            border: 1px solid #000;
        }

        td {
            padding: 15px 8px;
            border: 1px solid #d1d5db;
            text-align: center;
            font-size: 13px;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .row-num {
            font-weight: bold;
            color: #6b7280;
            background: #f3f4f6;
        }

        .sku {
            font-weight: bold;
            color: #2563eb;
            font-family: 'Courier New', monospace;
        }

        .product-name {
            text-align: right;
            padding-right: 12px;
        }

        .product-details {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .stock-qty {
            font-weight: bold;
            font-size: 15px;
            color: #059669;
        }

        .actual-qty {
            background: #fef3c7 !important;
            border: 2px solid #f59e0b !important;
            min-height: 40px;
            font-weight: bold;
        }

        .notes-col {
            background: #f0fdf4 !important;
        }

        .signature-section {
            display: flex;
            justify-content: space-around;
            margin-top: 60px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 30%;
        }

        .signature-line {
            border-top: 2px solid #000;
            margin-top: 60px;
            padding-top: 10px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #d1d5db;
            color: #6b7280;
            font-size: 12px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            .signature-section {
                page-break-before: avoid;
            }
        }

        @page {
            size: A4;
            margin: 15mm;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">
            🖨️ طباعة كشف الجرد
        </button>
    </div>

    <div class="header">
        <h1>📋 كشف جرد المخزون</h1>
        <p><strong>التاريخ:</strong> {{ $date }} | <strong>الوقت:</strong> {{ $time }}</p>
        <p><strong>عدد الأصناف:</strong> {{ $products->count() }} صنف</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 12%">SKU</th>
                <th style="width: 33%">اسم المنتج</th>
                <th style="width: 12%">الكمية بالنظام</th>
                <th style="width: 18%">الكمية الفعلية</th>
                <th style="width: 20%">ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
                <tr>
                    <td class="row-num">{{ $index + 1 }}</td>
                    <td class="sku">{{ $product->sku }}</td>
                    <td class="product-name">
                        <strong>{{ $product->name }}</strong>
                        @if($product->size || $product->color)
                            <div class="product-details">
                                @if($product->size)
                                    <span>مقاس: {{ $product->size }}</span>
                                @endif
                                @if($product->color)
                                    <span>{{ $product->size ? ' • ' : '' }}لون: {{ $product->color }}</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="stock-qty">{{ $product->stock_qty }}</td>
                    <td class="actual-qty">&nbsp;</td>
                    <td class="notes-col">&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">المدير</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">المحاسب</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">أمين المخزن</div>
        </div>
    </div>

    <div class="footer">
        <p>تم الإنشاء بواسطة نظام إدارة المخزون - {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <script>
        // لو عايز الطباعة تبدأ تلقائياً، شيل الكومنت من السطر التالي:
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>





@php 

public function getPlayersByTeamId(string $teamId = null, int $page = 1): array
	{
		$params = [
			'page'        => $page,
			'retire_time' => 0,
		];

		if ($teamId) {
			$params['team_id'] = $teamId;
		}
		$response = $this->request('football/player/with_stat/list', $params);
		//dd($response);
		
		if (!isset($response['code']) || $response['code'] !== 0) {
			Log::channel('custom_log')->error("API Error", ['response' => $response]);
			return [];
		}
		if (!isset($response['results']) || empty($response['results'])) {
			Log::channel('custom_log')->info("No players fouund", ['params' => $params]);
			return [];
		}
		$activePlayers = array_filter($response['results'], function ($player) {
			return !empty($player['team_id']) && trim($player['team_id']) !== ''; //
		});
		Log::channel('custom_log')->info("Players fetched", [
			'page' => $page,
			'total' => count($response['results']),
			'active_with_team' => count($activePlayers)
		]);

		return array_values($activePlayers);
	}