@extends('layout.app')

@section('title', 'Laporan Kepemilikan Aset')
@section('active_menu', 'laporan-aset')
@section('breadcrumbs', 'Modul Reporting | Laporan Aset')

@section('content')
<section class="hero" id="heroSection">
    <div class="hero-text">
        <span class="eyebrow">Modul Reporting</span>
        <h1 class="hero-title">Laporan Aset</h1>
        <p class="hero-sub">Daftar Kepemilikan Aset (Kondisi Saat Ini: {{ \Carbon\Carbon::now()->format('d F Y') }})</p>
    </div>
    <div class="hero-actions">
        <a href="{{ route('laporan.aset.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-primary" style="background: #28a745; border-color: #28a745; margin-right: 5px;">Excel</a>
        <a href="{{ route('laporan.aset.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank" class="btn btn-primary" style="background: #dc3545; border-color: #dc3545; margin-right: 5px;">PDF</a>
        <button class="btn btn-primary" onclick="window.print()" style="background: #2c3e50; border-color: #2c3e50;">
            Print Laporan
        </button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head" id="filterSection">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Kepemilikan Aset</h2>
        </div>
        
        <form action="{{ route('laporan.aset') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Cari aset..." style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft); width: 200px;">
            <select name="status" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Status</option>
                <option value="Aman" {{ request('status') == 'Aman' ? 'selected' : '' }}>Aman</option>
                <option value="Akan Jatuh Tempo" {{ request('status') == 'Akan Jatuh Tempo' ? 'selected' : '' }}>Akan Jatuh Tempo</option>
                <option value="Sudah Jatuh Tempo" {{ request('status') == 'Sudah Jatuh Tempo' ? 'selected' : '' }}>Sudah Jatuh Tempo</option>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 5px 15px;">Filter</button>
            <a href="{{ route('laporan.aset') }}" class="btn btn--ghost" style="padding: 5px 15px; text-decoration: none;">Reset</a>
        </form>
    </div>
    
    <div style="display:none;" id="printHeader">
        <h2 style="text-align:center; font-family:sans-serif; margin-bottom: 5px;">DAFTAR KEPEMILIKAN ASET</h2>
        <h4 style="text-align:center; font-family:sans-serif; font-weight:normal; margin-top:0;">
            Kondisi Saat Ini ({{ \Carbon\Carbon::now()->format('d F Y') }})
        </h4>
        <hr style="margin-bottom: 20px;">
    </div>
    
    <div class="table-scroll">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>No. Sertifikat</th>
                    <th>Cabang / Lokasi</th>
                    <th>Pemilik</th>
                    <th style="text-align:right">Luas Tanah (m&sup2;)</th>
                    <th>Masa Berlaku</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $totalLuas = 0; @endphp
                @forelse($asets as $aset)
                @php $totalLuas += $aset->luas_tanah; @endphp
                <tr>
                    <td><strong>{{ $aset->nomor_sertifikat }}</strong></td>
                    <td class="cell-name">
                        {{ $aset->kode_cabang }}
                        <div style="font-size: 11px; color: var(--t-muted);">{{ Str::limit($aset->lokasi, 40) }}</div>
                    </td>
                    <td>{{ $aset->nama_pemilik }}</td>
                    <td style="text-align:right">{{ number_format($aset->luas_tanah, 2, ',', '.') }}</td>
                    <td>{{ $aset->jatuh_tempo_sertifikat ? date('d M Y', strtotime($aset->jatuh_tempo_sertifikat)) : '-' }}</td>
                    <td>
                        @php
                            $statusClass = '';
                            if($aset->status_sertifikat === 'Aman' || $aset->status_sertifikat === 'Tidak Ada Jatuh Tempo') $statusClass = 't-active';
                            elseif($aset->status_sertifikat === 'Akan Jatuh Tempo') $statusClass = 't-used';
                            elseif($aset->status_sertifikat === 'Sudah Jatuh Tempo') $statusClass = 't-unavail';
                        @endphp
                        <span class="tag {{ $statusClass }}">{{ $aset->status_sertifikat }}</span>
                    </td>
                    <td style="font-size: 11px;">{{ $aset->keterangan ?: '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 30px; color: var(--t-muted);">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
            @if(count($asets) > 0)
            <tfoot>
                <tr style="background: var(--bg-muted); font-weight: bold;">
                    <td colspan="3" style="text-align:right; padding: 12px;">TOTAL KESELURUHAN LUAS TANAH</td>
                    <td style="text-align:right; color: var(--accent);">{{ number_format($totalLuas, 2, ',', '.') }} m&sup2;</td>
                    <td colspan="3"></td>
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
        @page { size: landscape; margin: 1cm; }
    }
</style>
@endpush
@endsection
