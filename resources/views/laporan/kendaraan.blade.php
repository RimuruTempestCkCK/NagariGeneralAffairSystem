@extends('layouts.adminator')

@section('title', 'Laporan Kendaraan')
@section('active_menu', 'laporan-kendaraan')
@section('breadcrumbs', 'Modul Reporting | Laporan Kendaraan')

@section('content')
<section class="card" style="min-height: 500px;">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Modul Reporting</span>
            <h2 class="card-title">Laporan Eksploitasi Kendaraan</h2>
        </div>
        
        <div class="data-toolbar">
            <form action="{{ route('laporan.kendaraan') }}" method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <div>
                    <label style="font-size:12px;">Mulai Tanggal</label><br>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" style="width:150px; padding:6px; margin:0;">
                </div>
                <div>
                    <label style="font-size:12px;">Sampai Tanggal</label><br>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" style="width:150px; padding:6px; margin:0;">
                </div>
                <div>
                    <label style="font-size:12px;">Pencarian (Plat/Jenis)</label><br>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="width:200px; padding:6px; margin:0;" placeholder="Cari...">
                </div>
                <div style="margin-top: 18px;">
                    <button type="submit" class="btn btn--primary" style="padding: 6px 12px;">Filter</button>
                    <a href="{{ route('laporan.kendaraan') }}" class="btn btn--ghost" style="padding: 6px 12px;">Reset</a>
                </div>
            </form>
            
            <div style="margin-top: 18px; margin-left: 20px;">
                <a href="{{ route('laporan.kendaraan.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn--success" style="padding: 6px 12px; margin-right: 5px; background: #28a745; border-color: #28a745; color: white; text-decoration: none;">Excel</a> <a href="{{ route('laporan.kendaraan.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank" class="btn btn--danger" style="padding: 6px 12px; margin-right: 5px; background: #dc3545; border-color: #dc3545; color: white; text-decoration: none;">PDF</a> <button class="btn btn--primary" onclick="window.print()" style="padding: 6px 12px; background: #2c3e50; border-color: #2c3e50;">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2" style="margin-right: 5px;">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg> Print Laporan
                </button>
            </div>
        </div>
    </div>
    
    <div style="display:none;" id="printHeader">
        <h2 style="text-align:center; font-family:sans-serif; margin-bottom: 5px;">LAPORAN EKSPLOITASI KENDARAAN</h2>
        <h4 style="text-align:center; font-family:sans-serif; font-weight:normal; margin-top:0;">
            Periode: {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} s.d {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Sekarang' }}
        </h4>
        <hr style="margin-bottom: 20px;">
    </div>
    
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
                <td>{{ $k->jenis_kendaraan }}</td>
                <td style="text-align:right">{{ number_format($k->total_jarak, 0, ',', '.') }}</td>
                <td style="text-align:right">{{ number_format($k->total_bbm, 0, ',', '.') }}</td>
                <td style="text-align:right">
                    {{ number_format($k->total_pemeliharaan, 0, ',', '.') }}
                    @if(count($k->breakdown) > 0)
                        <div style="font-size: 10px; color: var(--t-muted); margin-top: 5px;">
                        @foreach($k->breakdown as $jenis => $biaya)
                            {{ $jenis }}: {{ number_format($biaya, 0, ',', '.') }}<br>
                        @endforeach
                        </div>
                    @endif
                </td>
                <td style="text-align:right; font-weight:bold; color: #e74c3c;">{{ number_format($totalEksploitasi, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding: 20px;">Data tidak ditemukan.</td>
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
                <td style="text-align:right; color: #e74c3c;">Rp {{ number_format($grandBBM + $grandPerawatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</section>

@push('styles')
<style>
    @media print {
        #printHeader { display: block !important; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table { width: 100% !important; }
        @page { size: portrait; margin: 1cm; }
    }
</style>
@endpush
@endsection
