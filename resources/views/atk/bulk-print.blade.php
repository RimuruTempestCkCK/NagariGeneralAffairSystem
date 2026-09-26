<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bulk Print QR ATK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .print-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            width: 100%;
        }
        .label-box {
            border: 1px dashed #ccc;
            padding: 10px;
            text-align: center;
            break-inside: avoid;
            page-break-inside: avoid;
            box-sizing: border-box;
        }
        .qr-wrapper {
            margin-bottom: 8px;
        }
        .qr-wrapper svg {
            width: 100px;
            height: 100px;
        }
        .label-code {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 3px;
        }
        .label-name {
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .no-print {
            margin-bottom: 20px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .label-box { border: 1px solid #000; }
            @page { margin: 1cm; size: A4 portrait; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print">
        <button onclick="window.print()" style="padding:10px 20px; background:#007bff; color:#fff; border:none; cursor:pointer;">Print Sekarang</button>
        <button onclick="window.close()" style="padding:10px 20px; background:#6c757d; color:#fff; border:none; cursor:pointer;">Tutup</button>
    </div>

    <div class="print-container">
        @foreach($atks as $atk)
            <div class="label-box">
                <div class="qr-wrapper">
                    {!! QrCode::format('svg')->size(100)->generate($atk->kode_atk) !!}
                </div>
                <div class="label-code">{{ $atk->kode_atk }}</div>
                <div class="label-name">{{ $atk->nama_atk }}</div>
            </div>
        @endforeach
    </div>
</body>
</html>
