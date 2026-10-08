<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Daftar ATK</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px; color: #111827; font-family: Arial, sans-serif; }
        .toolbar { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 24px; }
        .toolbar button { border: 0; border-radius: 6px; padding: 10px 16px; background: #2563eb; color: #fff; cursor: pointer; font-size: 14px; }
        .toolbar button.secondary { background: #6b7280; }
        h1 { margin: 0 0 6px; font-size: 24px; }
        .subtitle { margin: 0; color: #4b5563; font-size: 13px; }
        .report-meta { display: flex; justify-content: space-between; gap: 16px; margin: 20px 0 12px; color: #374151; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #9ca3af; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .number { text-align: right; white-space: nowrap; }
        .empty { padding: 24px; text-align: center; color: #6b7280; }
        @media print {
            body { padding: 0; }
            .toolbar { display: none; }
            th { background: #e5e7eb !important; print-color-adjust: exact; }
            @page { size: landscape; margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" class="secondary" onclick="window.close()">Tutup</button>
        <button type="button" onclick="window.print()">Cetak Daftar ATK</button>
    </div>

    <header>
        <h1>Daftar Inventaris Alat Tulis Kantor (ATK)</h1>
        <p class="subtitle">Nagari General Affair System</p>
    </header>

    <div class="report-meta">
        <span>
            Filter:
            {{ request('search') ?: 'Semua kode/nama' }}
            | Jenis: {{ request('jenis_atk') ?: 'Semua jenis' }}
            | Status: {{ request('status') ?: 'Semua status' }}
        </span>
        <span>Tanggal cetak: {{ now()->format('d-m-Y H:i') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode ATK</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th class="number">Stok</th>
                <th>Satuan</th>
                <th class="number">Harga Satuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($atks as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->kode_atk }}</td>
                    <td>{{ $item->nama_atk }}</td>
                    <td>{{ $item->jenis_atk }}</td>
                    <td class="number">{{ number_format($item->jumlah) }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td class="number">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">Tidak ada data ATK yang sesuai dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="subtitle">Jumlah data: {{ $atks->count() }} item</p>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
