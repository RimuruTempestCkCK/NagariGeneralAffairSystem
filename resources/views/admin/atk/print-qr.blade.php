@extends('layout.app')

@section('title', 'Cetak QR Code')
@section('active_menu', 'atk')
@section('breadcrumbs', 'Modul ATK | Master ATK | Cetak QR')

@section('content')
<div class="px-4 pt-6 no-print">
    <div class="p-4 bg-white border border-gray-200 rounded-2xl shadow-sm mb-6 dark:border-gray-700 dark:bg-gray-800 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white">Cetak QR Code ATK</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cetak label QR Code untuk ditempelkan pada barang.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn btn--ghost" onclick="window.close()">Tutup</button>
            <button class="btn btn--primary" onclick="window.print()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" style="margin-right: 5px;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Cetak Label (Print)
            </button>
        </div>
    </div>
</div>

<div class="print-area">
    <div class="qr-card" style="background: #ffffff; border: 2px dashed #9ca3af; border-radius: 12px; width: 320px; padding: 24px; text-align: center; margin: 0 auto; color: #111827;">
        <div class="logo-header" style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 12px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            <span style="font-size: 15px; font-weight: 800; letter-spacing: -0.5px;">DIVISI UMUM (GAS)</span>
        </div>
        
        <div class="kode-badge" style="background-color: #eff6ff; color: #1d4ed8; font-size: 16px; font-weight: 800; padding: 6px 12px; border-radius: 6px; display: inline-block; margin-bottom: 8px;">{{ $atk->kode_atk }}</div>
        
        <div class="qr-wrapper" style="margin: 14px auto; display: flex; justify-content: center;">
            <img src="data:image/svg+xml;base64,{{ $qrImage }}" alt="QR Code {{ $atk->kode_atk }}" style="width: 170px; height: 170px;">
        </div>

        <div class="item-name" style="font-size: 15px; font-weight: 700; margin-bottom: 4px;">{{ $atk->nama_atk }}</div>
        <div class="item-category" style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Jenis: {{ $atk->jenis_atk }} | Satuan: {{ $atk->satuan }}</div>

        <div class="footer-text" style="border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600;">
            Sistem Inventaris ATK &bull; Bank Nagari
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .d-sidebar, .d-topbar, .no-print, [data-shell-sidebar], [data-shell-topbar] {
            display: none !important;
        }
        body, html {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .main { margin: 0 !important; }
        .content { padding: 0 !important; }
        .print-area {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .qr-card {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
        }
    }
</style>
@endpush
@endsection
