{{-- resources/views/pdf/labels.blade.php --}}
<!doctype html>
<html dir="rtl">

<head>
    <meta charset="utf-8">
    <style>
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8mm
        }

        .label {
            border: 1px dashed #aaa;
            padding: 4mm;
            font-size: 12px;
            text-align: center
        }

        .name {
            font-weight: bold;
            margin-bottom: 2mm
        }

        .sku {
            font-size: 10px;
            margin-top: 1mm
        }
    </style>
</head>

<body>
    <div class="grid">
        @foreach($barcodes as $b)
            <div class="label">
                <div class="name">{{ $pv->name }} @if($pv->color)- {{ $pv->color }}@endif @if($pv->size)-
                {{ $pv->size }}@endif</div>
                @if($b->type === 'qr')
                    {!! QrCode::size(100)->generate($b->code) !!}
                @else
                    {!! \Milon\Barcode\DNS1D::getBarcodeHTML($b->code, $b->type === 'ean13' ? 'EAN13' : 'C128', 2, 60) !!}
                @endif
                <div class="sku">{{ $b->code }}</div>
                <div class="sku">{{ $pv->sku }}</div>
                <div class="sku">{{ number_format($pv->price, 2) }} ج.م</div>
            </div>
        @endforeach
    </div>
</body>

</html>