@extends('layout.app')

@section('title', 'Laporan ATK')
@section('active_menu', 'laporan-atk')
@section('breadcrumbs', 'Modul Reporting | Laporan ATK')

@section('content')
<section class="hero" id="heroSection">
    <div class="hero-text">
        <span class="eyebrow">Modul Reporting</span>
        <h1 class="hero-title">Laporan Histori & Persediaan ATK</h1>
        <p class="hero-sub">Periode: {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} s.d {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Sekarang' }}</p>
    </div>
    <div class="hero-actions">
        <a href="{{ route('admin.laporan.atk.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn--primary" style="background: #28a745; border-color: #28a745; margin-right: 5px;">Excel</a>
        <a href="{{ route('admin.laporan.atk.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank" class="btn btn--primary" style="background: #dc3545; border-color: #dc3545; margin-right: 5px;">PDF</a>
        <button class="btn btn--primary" onclick="window.print()" style="background: #2c3e50; border-color: #2c3e50;">
            Print Laporan
        </button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head" id="filterSection">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Persediaan ATK</h2>
        </div>
        
        <form action="{{ route('admin.laporan.atk') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Cari ATK..." style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft); width: 200px;">
            <button type="submit" class="btn btn--primary" style="padding: 5px 15px;">Filter</button>
            <a href="{{ route('admin.laporan.atk') }}" class="btn btn--ghost" style="padding: 5px 15px; text-decoration: none;">Reset</a>
        </form>
    </div>
    
    <div style="display:none;" id="printHeader">
        <h2 style="text-align:center; font-family:sans-serif; margin-bottom: 5px;">LAPORAN PERSEDIAAN DAN PEMAKAIAN ATK</h2>
        <h4 style="text-align:center; font-family:sans-serif; font-weight:normal; margin-top:0;">
            Periode: {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} s.d {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Sekarang' }}
        </h4>
        <hr style="margin-bottom: 20px;">
    </div>
    
    <div class="table-scroll">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Kode ATK</th>
                    <th>Nama ATK</th>
                    <th style="text-align:right">Stok Masuk</th>
                    <th style="text-align:right">Pemakaian</th>
                    <th style="text-align:right">Stok Akhir</th>
                    <th style="text-align:right">Beban Biaya (Rp)</th>
                    <th style="text-align:right">Nilai Sisa (Rp)</th>
                    <th>Info Jurnal</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalBeban = 0; 
                    $totalNilai = 0;
                @endphp
                @forelse($atks as $atk)
                @php
                    $totalBeban += $atk->beban_biaya_periode;
                    $totalNilai += $atk->nilai_persediaan_saat_ini;
                @endphp
                <tr>
                    <td><strong>{{ $atk->kode_atk }}</strong></td>
                    <td class="cell-name">{{ $atk->nama_atk }}</td>
                    <td style="text-align:right; color: var(--success);">+{{ number_format($atk->stok_masuk_periode, 0, ',', '.') }} {{ $atk->satuan }}</td>
                    <td style="text-align:right; color: var(--danger);">-{{ number_format($atk->pemakaian_periode, 0, ',', '.') }} {{ $atk->satuan }}</td>
                    <td style="text-align:right"><strong>{{ number_format($atk->stok, 0, ',', '.') }}</strong> {{ $atk->satuan }}</td>
                    <td style="text-align:right; color: var(--danger); font-weight: bold;">{{ number_format($atk->beban_biaya_periode, 0, ',', '.') }}</td>
                    <td style="text-align:right; color: var(--success); font-weight: bold;">{{ number_format($atk->nilai_persediaan_saat_ini, 0, ',', '.') }}</td>
                    <td style="font-size: 11px; font-family: monospace;">
                        @if(count($atk->jurnal_info) > 0)
                            {!! implode('<br>', $atk->jurnal_info) !!}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding: 30px; color: var(--t-muted);">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
            @if(count($atks) > 0)
            <tfoot>
                <tr style="background: var(--bg-muted); font-weight: bold;">
                    <td colspan="5" style="text-align:right; padding: 12px;">TOTAL KESELURUHAN</td>
                    <td style="text-align:right; color: var(--danger);">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                    <td style="text-align:right; color: var(--success);">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                    <td></td>
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
