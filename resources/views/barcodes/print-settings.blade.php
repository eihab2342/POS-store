<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعدادات الطباعة - {{ $variant->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            direction: rtl;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .product-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .product-info p {
            margin: 5px 0;
            color: #666;
        }

        .form-group {
            margin: 20px 0;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-print {
            background: #4CAF50;
            color: white;
        }

        .btn-print:hover {
            background: #45a049;
        }

        .btn-preview {
            background: #2196F3;
            color: white;
        }

        .btn-preview:hover {
            background: #0b7dda;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>🖨️ إعدادات الطباعة</h1>

        <div class="product-info">
            <p><strong>المنتج:</strong> {{ $variant->name }}</p>
            <p><strong>SKU:</strong> {{ $variant->sku }}</p>
            <p><strong>السعر:</strong> {{ number_format($variant->price, 2) }} جنيه</p>
            <p><strong>المخزون:</strong> {{ $variant->stock_qty }}</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">
            ❌ {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('barcodes.print-thermal', $variant) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="qty">عدد الاستيكرات للطباعة:</label>
                <input type="number" id="qty" name="qty" value="{{ $variant->stock_qty }}" min="1" max="1000" required>
            </div>

            <div class="form-group">
                <label for="printer">اختر الطابعة:</label>
                <select id="printer" name="printer" required>
                    @if(count($printers) > 0)
                    @foreach($printers as $printer)
                    <option value="{{ $printer }}" {{ str_contains($printer, 'XP-Q371U' ) ? 'selected' : '' }}>
                        {{ $printer }}
                    </option>
                    @endforeach
                    @else
                    <option value="XP-Q371U">XP-Q371U (افتراضي)</option>
                    @endif
                </select>
                <small style="color: #666; display: block; margin-top: 5px;">
                    💡 تأكد من تشغيل الطابعة وتوصيلها بالكمبيوتر
                </small>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-print">
                    🖨️ طباعة الآن
                </button>
                <button type="button" class="btn-preview"
                    onclick="window.open('{{ route('barcodes.preview', $variant) }}', '_blank')">
                    👁️ معاينة A4
                </button>
            </div>
        </form>

    </div>

</body>

</html>