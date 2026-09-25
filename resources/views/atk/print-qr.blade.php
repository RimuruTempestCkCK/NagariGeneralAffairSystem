<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code - {{ $atk->kode_atk }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-nagari.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .action-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            background-color: #1d4ed8;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-back {
            background-color: #4b5563;
        }
        .qr-card {
            background: #ffffff;
            border: 2px dashed #9ca3af;
            border-radius: 12px;
            width: 320px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .logo-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .logo-header img {
            height: 28px;
            width: auto;
        }
        .logo-header span {
            font-size: 13px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.5px;
        }
        .qr-wrapper {
            margin: 14px auto;
            display: flex;
            justify-content: center;
        }
        .kode-badge {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-size: 16px;
            font-weight: 800;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .item-name {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .item-category {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 8px;
        }
        .footer-text {
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: transparent;
                padding: 0;
                margin: 0;
            }
            .action-bar {
                display: none !important;
            }
            .qr-card {
                border: 1px solid #000;
                box-shadow: none;
                margin: 0 auto;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="action-bar">
        <button class="btn btn-back" onclick="window.close()">Tutup</button>
        <button class="btn" onclick="window.print()">Cetak Label QR (Print)</button>
    </div>

    <div class="qr-card">
        <div class="logo-header">
            <img src="{{ asset('images/bank-nagari-logo.svg') }}" alt="Bank Nagari">
            <span>DIVISI UMUM (GAS)</span>
        </div>
        
        <div class="kode-badge">{{ $atk->kode_atk }}</div>
        
        <div class="qr-wrapper">
            <img src="data:image/svg+xml;base64,{{ $qrImage }}" alt="QR Code {{ $atk->kode_atk }}" style="width: 170px; height: 170px;">
        </div>

        <div class="item-name">{{ $atk->nama_atk }}</div>
        <div class="item-category">Jenis: {{ $atk->jenis_atk }} | Satuan: {{ $atk->satuan }}</div>

        <div class="footer-text">
            Sistem Inventaris ATK &bull; Bank Nagari
        </div>
    </div>
</body>
</html>
