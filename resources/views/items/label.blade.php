<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Barang</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: white;
            color: #111827;
        }
        .label {
            width: 160px;
            padding: 10px 10px 8px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            text-align: center;
            page-break-inside: avoid;
            display: inline-block;
            margin: 6px;
            vertical-align: top;
        }
        .sku {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 6px;
            margin-bottom: 8px;
        }
        .name {
            font-size: 11px;
            font-weight: 600;
            line-height: 1.4;
            min-height: 28px;
            overflow: hidden;
        }
        .qr {
            width: 64px;
            height: 64px;
            margin: 8px auto;
            display: block;
        }
        .barcode {
            width: 120px;
            height: 42px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    @php $labelItems = $items ?? [ ['sku' => $item->sku, 'name' => $item->name, 'barcodeImg' => $barcodeUrl ?? null, 'qrImg' => $qrUrl ?? null] ]; @endphp

    @foreach($labelItems as $labelItem)
        <div class="label">
            @if(!empty($labelItem['barcodeImg']))
                <img class="barcode" src="{{ $labelItem['barcodeImg'] }}" alt="Barcode {{ $labelItem['sku'] }}" />
            @endif
            <div class="sku">{{ $labelItem['sku'] }}</div>
            <div class="name">{{ $labelItem['name'] }}</div>
            @if(!empty($labelItem['qrImg']))
                <img class="qr" src="{{ $labelItem['qrImg'] }}" alt="QR {{ $labelItem['sku'] }}" />
            @endif
        </div>
    @endforeach
</body>
</html>
