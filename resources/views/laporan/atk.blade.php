@extends('layouts.adminator')

@section('title', 'Laporan ATK')
@section('active_menu', 'laporan-atk')
@section('breadcrumbs', 'Modul Reporting | Laporan ATK')

@section('content')
<section class="card" style="min-height: 500px;">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Modul Reporting</span>
            <h2 class="card-title">Laporan Histori & Persediaan ATK</h2>
        </div>
        
        <!-- Toolbar for Print & Filter -->
        <div class="data-toolbar">
            <form action="{{ route('laporan.atk') }}" method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <div>
                    <label style="font-size:12px;">Mulai Tanggal</label><br>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" style="width:150px; padding:6px; margin:0;">
                </div>
                <div>
                    <label style="font-size:12px;">Sampai Tanggal</label><br>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" style="width:150px; padding:6px; margin:0;">
                </div>
                <div>
                    <label style="font-size:12px;">Pencarian (Kode/Nama)</label><br>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="width:200px; padding:6px; margin:0;" placeholder="Cari ATK...">
                </div>
                <div style="margin-top: 18px;">
                    <button type="submit" class="btn btn--primary" style="padding: 6px 12px;">Filter</button>
                    <a href="{{ route('laporan.atk') }}" class="btn btn--ghost" style="padding: 6px 12px;">Reset</a>
                </div>
            </form>
            
            <div style="margin-top: 18px; margin-left: 20px;">
                <a href="{{ route('laporan.atk.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn--success" style="padding: 6px 12px; margin-right: 5px; background: #28a745; border-color: #28a745; color: white; text-decoration: none;">Excel</a> <a href="{{ route('laporan.atk.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank" class="btn btn--danger" style="padding: 6px 12px; margin-right: 5px; background: #dc3545; border-color: #dc3545; color: white; text-decoration: none;">PDF</a> <button class="btn btn--primary" onclick="window.print()" style="padding: 6px 12px; background: #2c3e50; border-color: #2c3e50;">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2" style="margin-right: 5px;">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg> Print Laporan
                </button>
            </div>
        </div>
    </div>
    
    <!-- Report Title for Print -->
    <div style="display:none;" id="printHeader">
        <h2 style="text-align:center; font-family:sans-serif; margin-bottom: 5px;">LAPORAN PERSEDIAAN DAN PEMAKAIAN ATK</h2>
        <h4 style="text-align:center; font-family:sans-serif; font-weight:normal; margin-top:0;">
            Periode: {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} s.d {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Sekarang' }}
        </h4>
        <hr style="margin-bottom: 20px;">
    </div>
    
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
                <td>{{ $atk->nama_atk }}</td>
                <td style="text-align:right">{{ number_format($atk->stok_masuk_periode, 0, ',', '.') }} {{ $atk->satuan }}</td>
                <td style="text-align:right">{{ number_format($atk->pemakaian_periode, 0, ',', '.') }} {{ $atk->satuan }}</td>
                <td style="text-align:right"><strong>{{ number_format($atk->stok, 0, ',', '.') }}</strong> {{ $atk->satuan }}</td>
                <td style="text-align:right; color: #e74c3c;">{{ number_format($atk->beban_biaya_periode, 0, ',', '.') }}</td>
                <td style="text-align:right; color: #27ae60;">{{ number_format($atk->nilai_persediaan_saat_ini, 0, ',', '.') }}</td>
                <td style="font-size: 11px;">
                    @if(count($atk->jurnal_info) > 0)
                        {!! implode('<br>', $atk->jurnal_info) !!}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; padding: 20px;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($atks) > 0)
        <tfoot>
            <tr style="background: var(--bg-muted); font-weight: bold;">
                <td colspan="5" style="text-align:right; padding: 12px;">TOTAL KESELURUHAN</td>
                <td style="text-align:right; color: #e74c3c;">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                <td style="text-align:right; color: #27ae60;">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                <td></td>
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
