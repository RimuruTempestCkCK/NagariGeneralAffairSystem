@extends('layout.app')

@section('title', 'Laporan Kendaraan')
@section('active_menu', 'laporan-kendaraan')
@section('breadcrumbs', 'Modul Reporting | Laporan Kendaraan')

@section('content')
<style>
    .vehicle-report-table {
        min-width: 1270px;
        table-layout: fixed;
    }
    .vehicle-report-table th,
    .vehicle-report-table td {
        padding: 16px 14px;
        vertical-align: top;
    }
    .vehicle-report-table th:nth-child(4),
    .vehicle-report-table td:nth-child(4) {
        padding-right: 24px;
        white-space: nowrap;
    }
    .vehicle-report-table th:nth-child(5),
    .vehicle-report-table td:nth-child(5) {
        padding-left: 24px;
        border-left: 1px solid var(--border-soft);
    }
    .vehicle-report-table .vehicle-proof-list {
        display: grid;
        gap: 10px;
    }
    .vehicle-report-table .vehicle-proof-trip {
        display: grid;
        gap: 6px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-soft);
    }
    .vehicle-report-table .vehicle-proof-trip:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }
    .vehicle-report-table .vehicle-proof-date {
        color: var(--t-muted);
        font-size: 11px;
        white-space: nowrap;
    }
    .vehicle-report-table .vehicle-proof-photos {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .vehicle-report-table .vehicle-proof-link {
        display: grid;
        justify-items: center;
        gap: 4px;
        min-width: 66px;
        font-size: 11px;
        text-decoration: none;
    }
    .vehicle-report-table .vehicle-proof-link img {
        display: block;
        width: 68px;
        height: 52px;
        object-fit: cover;
        border-radius: 6px;
    }
</style>

<section class="hero" id="heroSection">
    <div class="hero-text">
        <span class="eyebrow">Modul Reporting</span>
        <h1 class="hero-title">Laporan Eksploitasi Kendaraan</h1>
        <p class="hero-sub">Periode: {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} s.d {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Sekarang' }}</p>
    </div>
    <div class="hero-actions">
        <a href="{{ route('admin.laporan.kendaraan.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn--success">Excel</a>
        <a href="{{ route('admin.laporan.kendaraan.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank" class="btn btn--danger">PDF</a>
    </div>
</section>

<section class="card col-12">
    <div class="card-head" id="filterSection">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Eksploitasi Kendaraan</h2>
        </div>
        
        <form action="{{ route('admin.laporan.kendaraan') }}" method="GET" class="filter-bar" style="display: flex; gap: 10px; align-items: center;">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="input">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="input">
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Cari..." style="width: 200px;">
            <select name="kode_cabang" class="input">
                <option value="">Semua Cabang</option>
                @foreach($branches as $kode => $nama)
                <option value="{{ $kode }}" {{ request('kode_cabang') === $kode ? 'selected' : '' }}>{{ $kode }} - {{ $nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn--primary btn--filter">Filter</button>
            <a href="{{ route('admin.laporan.kendaraan') }}" class="btn btn--ghost btn--reset">Reset</a>
        </form>
    </div>
    
    <div style="display:none;" id="printHeader">
        <h2 style="text-align:center; font-family:sans-serif; margin-bottom: 5px;">LAPORAN EKSPLOITASI KENDARAAN</h2>
        <h4 style="text-align:center; font-family:sans-serif; font-weight:normal; margin-top:0;">
            Periode: {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} s.d {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Sekarang' }}
        </h4>
        <hr style="margin-bottom: 20px;">
    </div>
    
    <div class="table-scroll">
        <table class="table vehicle-report-table" style="width: 100%; border-collapse: collapse;">
            <colgroup>
                <col style="width: 130px;">
                <col style="width: 130px;">
                <col style="width: 150px;">
                <col style="width: 120px;">
                <col style="width: 270px;">
                <col style="width: 140px;">
                <col style="width: 160px;">
                <col style="width: 170px;">
            </colgroup>
            <thead>
                <tr>
                    <th>Cabang</th>
                    <th>No. Polisi</th>
                    <th>Jenis Kendaraan</th>
                    <th style="text-align:right">Total Jarak (KM)</th>
                    <th>Bukti Kilometer</th>
                    <th style="text-align:right">Biaya BBM (Rp)</th>
                    <th style="text-align:right">Biaya Perawatan (Rp)</th>
                    <th style="text-align:right">Total Eksploitasi (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $grandJarak = 0; 
                    $grandBBM = 0;
                    $grandPerawatan = 0;
                @endphp
                @forelse($kendaraans as $k)
                @php
                    $grandJarak += $k->total_jarak;
                    $grandBBM += $k->total_bbm;
                    $grandPerawatan += $k->total_pemeliharaan;
                    $totalEksploitasi = $k->total_bbm + $k->total_pemeliharaan;
                @endphp
                <tr>
                    <td>{{ $branches[$k->kode_cabang] ?? 'Belum ditentukan' }}</td>
                    <td><strong>{{ $k->nomor_kendaraan }}</strong></td>
                    <td class="cell-name">{{ $k->jenis_kendaraan }}</td>
                    <td style="text-align:right; font-weight: bold;">{{ number_format($k->total_jarak, 0, ',', '.') }}</td>
                    <td>
                        <div class="vehicle-proof-list">
                        @forelse($k->perjalanans as $perjalanan)
                            @if($perjalanan->foto_km_awal || $perjalanan->foto_km_akhir)
                            <div class="vehicle-proof-trip">
                                <span class="vehicle-proof-date">{{ date('d/m/Y', strtotime($perjalanan->tanggal)) }}</span>
                                <div class="vehicle-proof-photos">
                                @if($perjalanan->foto_km_awal)
                                <a href="{{ asset('storage/' . $perjalanan->foto_km_awal) }}" target="_blank" rel="noopener" class="vehicle-proof-link">
                                    <img src="{{ asset('storage/' . $perjalanan->foto_km_awal) }}" alt="Kilometer berangkat {{ $k->nomor_kendaraan }}" loading="lazy">
                                    <span>Berangkat</span>
                                </a>
                                @endif
                                @if($perjalanan->foto_km_akhir)
                                <a href="{{ asset('storage/' . $perjalanan->foto_km_akhir) }}" target="_blank" rel="noopener" class="vehicle-proof-link">
                                    <img src="{{ asset('storage/' . $perjalanan->foto_km_akhir) }}" alt="Kilometer sampai {{ $k->nomor_kendaraan }}" loading="lazy">
                                    <span>Sampai</span>
                                </a>
                                @endif
                                </div>
                            </div>
                            @endif
                        @empty
                            <span style="color: var(--t-muted);">-</span>
                        @endforelse
                        </div>
                        @if($k->perjalanans->isNotEmpty() && $k->perjalanans->every(fn ($perjalanan) => !$perjalanan->foto_km_awal && !$perjalanan->foto_km_akhir))
                            <span style="color: var(--t-muted);">Belum ada foto</span>
                        @endif
                    </td>
                    <td style="text-align:right">{{ number_format($k->total_bbm, 0, ',', '.') }}</td>
                    <td style="text-align:right">
                        {{ number_format($k->total_pemeliharaan, 0, ',', '.') }}
                        @if(count($k->breakdown) > 0)
                            <div style="font-size: 11px; color: var(--t-muted); margin-top: 5px;">
                            @foreach($k->breakdown as $jenis => $biaya)
                                {{ $jenis }}: {{ number_format($biaya, 0, ',', '.') }}<br>
                            @endforeach
                            </div>
                        @endif
                    </td>
                    <td style="text-align:right; font-weight:bold; color: var(--danger);">{{ number_format($totalEksploitasi, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding: 30px; color: var(--t-muted);">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
            @if(count($kendaraans) > 0)
            <tfoot>
                <tr style="background: var(--bg-muted); font-weight: bold;">
                    <td colspan="4" style="text-align:right; padding: 12px;">TOTAL KESELURUHAN</td>
                    <td style="text-align:right">{{ number_format($grandJarak, 0, ',', '.') }}</td>
                    <td style="text-align:right">{{ number_format($grandBBM, 0, ',', '.') }}</td>
                    <td style="text-align:right">{{ number_format($grandPerawatan, 0, ',', '.') }}</td>
                    <td style="text-align:right; color: var(--danger);">Rp {{ number_format($grandBBM + $grandPerawatan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</section>

@endsection
