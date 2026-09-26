@extends('layout.app')

@section('title', 'Laporan Kendaraan')
@section('active_menu', 'laporan-kendaraan')
@section('breadcrumbs', 'Modul Reporting | Laporan Kendaraan')

@section('content')
<section class="hero" id="heroSection">
    <div class="hero-text">
        <span class="eyebrow">Modul Reporting</span>
        <h1 class="hero-title">Laporan Eksploitasi Kendaraan</h1>
        <p class="hero-sub">Periode: {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} s.d {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Sekarang' }}</p>
    </div>
    <div class="hero-actions">
        <a href="{{ route('admin.laporan.kendaraan.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-primary" style="background: #28a745; border-color: #28a745; margin-right: 5px;">Excel</a>
        <a href="{{ route('admin.laporan.kendaraan.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank" class="btn btn-primary" style="background: #dc3545; border-color: #dc3545; margin-right: 5px;">PDF</a>
        <button class="btn btn-primary" onclick="window.print()" style="background: #2c3e50; border-color: #2c3e50;">
            Print Laporan
        </button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head" id="filterSection">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Eksploitasi Kendaraan</h2>
        </div>
        
        <form action="{{ route('admin.laporan.kendaraan') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Cari..." style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft); width: 200px;">
            <button type="submit" class="btn btn-primary" style="padding: 5px 15px;">Filter</button>
            <a href="{{ route('admin.laporan.kendaraan') }}" class="btn btn--ghost" style="padding: 5px 15px; text-decoration: none;">Reset</a>
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
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>No. Polisi</th>
                    <th>Jenis Kendaraan</th>
                    <th style="text-align:right">Total Jarak (KM)</th>
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
                    <td><strong>{{ $k->plat_nomor }}</strong></td>
                    <td class="cell-name">{{ $k->jenis_kendaraan }}</td>
                    <td style="text-align:right; font-weight: bold;">{{ number_format($k->total_jarak, 0, ',', '.') }}</td>
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
                    <td colspan="6" style="text-align:center; padding: 30px; color: var(--t-muted);">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
            @if(count($kendaraans) > 0)
            <tfoot>
                <tr style="background: var(--bg-muted); font-weight: bold;">
                    <td colspan="2" style="text-align:right; padding: 12px;">TOTAL KESELURUHAN</td>
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

@push('styles')
<style>
    @media print {
        #heroSection, #filterSection, .sidebar, .top-nav { display: none !important; }
        #printHeader { display: block !important; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table { width: 100% !important; }
        .card { box-shadow: none !important; border: none !important; }
        @page { size: portrait; margin: 1cm; }
    }
</style>
@endpush
@endsection
