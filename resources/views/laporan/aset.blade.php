@extends('layouts.adminator')

@section('title', 'Laporan Kepemilikan Aset')
@section('active_menu', 'laporan-aset')
@section('breadcrumbs', 'Modul Reporting | Laporan Aset')

@section('content')
<section class="card" style="min-height: 500px;">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Modul Reporting</span>
            <h2 class="card-title">Daftar Kepemilikan Aset</h2>
        </div>
        
        <div class="data-toolbar">
            <form action="{{ route('laporan.aset') }}" method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <div>
                    <label style="font-size:12px;">Pencarian (Sertifikat/Cabang/Lokasi)</label><br>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="width:250px; padding:6px; margin:0;" placeholder="Cari aset...">
                </div>
                <div>
                    <label style="font-size:12px;">Status Sertifikat</label><br>
                    <select name="status" class="form-control" style="width:150px; padding:6px; margin:0;">
                        <option value="">Semua Status</option>
                        <option value="Aman" {{ request('status') == 'Aman' ? 'selected' : '' }}>Aman</option>
                        <option value="Akan Jatuh Tempo" {{ request('status') == 'Akan Jatuh Tempo' ? 'selected' : '' }}>Akan Jatuh Tempo</option>
                        <option value="Sudah Jatuh Tempo" {{ request('status') == 'Sudah Jatuh Tempo' ? 'selected' : '' }}>Sudah Jatuh Tempo</option>
                    </select>
                </div>
                <div style="margin-top: 18px;">
                    <button type="submit" class="btn btn--primary" style="padding: 6px 12px;">Filter</button>
                    <a href="{{ route('laporan.aset') }}" class="btn btn--ghost" style="padding: 6px 12px;">Reset</a>
                </div>
            </form>
            
            <div style="margin-top: 18px; margin-left: 20px;">
                <button class="btn btn--primary" onclick="window.print()" style="padding: 6px 12px; background: #2c3e50; border-color: #2c3e50;">
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
        <h2 style="text-align:center; font-family:sans-serif; margin-bottom: 5px;">DAFTAR KEPEMILIKAN ASET</h2>
        <h4 style="text-align:center; font-family:sans-serif; font-weight:normal; margin-top:0;">
            Kondisi Saat Ini ({{ \Carbon\Carbon::now()->format('d F Y') }})
        </h4>
        <hr style="margin-bottom: 20px;">
    </div>
    
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
                <td>
                    {{ $aset->kode_cabang }}<br>
                    <span style="font-size: 11px; color: var(--t-muted);">{{ Str::limit($aset->lokasi, 40) }}</span>
                </td>
                <td>{{ $aset->nama_pemilik }}</td>
                <td style="text-align:right">{{ number_format($aset->luas_tanah, 2, ',', '.') }}</td>
                <td>{{ $aset->jatuh_tempo_sertifikat ? date('d M Y', strtotime($aset->jatuh_tempo_sertifikat)) : '-' }}</td>
                <td>
                    @php
                        $statusClass = '';
                        if($aset->status_sertifikat === 'Aman' || $aset->status_sertifikat === 'Tidak Ada Jatuh Tempo') $statusClass = 't-new';
                        elseif($aset->status_sertifikat === 'Akan Jatuh Tempo') $statusClass = 't-used';
                        elseif($aset->status_sertifikat === 'Sudah Jatuh Tempo') $statusClass = 't-unavail';
                    @endphp
                    <span class="tag {{ $statusClass }}">{{ $aset->status_sertifikat }}</span>
                </td>
                <td style="font-size: 11px;">{{ $aset->keterangan ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding: 20px;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($asets) > 0)
        <tfoot>
            <tr style="background: var(--bg-muted); font-weight: bold;">
                <td colspan="3" style="text-align:right; padding: 12px;">TOTAL KESELURUHAN LUAS TANAH</td>
                <td style="text-align:right">{{ number_format($totalLuas, 2, ',', '.') }} m&sup2;</td>
                <td colspan="3"></td>
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
        @page { size: landscape; margin: 1cm; }
    }
</style>
@endpush
@endsection
