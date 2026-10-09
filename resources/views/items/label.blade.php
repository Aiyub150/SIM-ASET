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
            padding: 10px 8px 8px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            text-align: center;
            page-break-inside: avoid;
            display: inline-block;
            margin: 6px;
            vertical-align: top;
        }
        .sku {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 6px;
            margin-bottom: 4px;
            color: #111827;
        }
        .name {
            font-size: 10.5px;
            font-weight: 600;
            line-height: 1.3;
            min-height: 26px;
            max-height: 38px;
            overflow: hidden;
            color: #374151;
        }
        .qr {
            width: 90px;
            height: 90px;
            margin: 4px auto 6px;
            display: block;
        }
        .barcode {
            width: 130px;
            height: 42px;
            display: block;
            margin: 0 auto 6px;
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
