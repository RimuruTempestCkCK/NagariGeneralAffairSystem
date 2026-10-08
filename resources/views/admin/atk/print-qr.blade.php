@extends('layout.app')

@section('title', 'Cetak QR Code')
@section('active_menu', 'atk')
@section('breadcrumbs', 'Modul ATK | Master ATK | Cetak QR')

@section('content')
<div class="px-4 pt-6 no-print">
    <div class="p-4 bg-white border border-gray-200 rounded-2xl shadow-sm mb-6   flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl ">Cetak QR Code ATK</h1>
            <p class="text-sm text-gray-500  mt-1">Cetak label QR Code untuk ditempelkan pada barang.</p>
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
    <div class="qr-label">
        <div class="qr-wrapper">
            <img src="data:image/svg+xml;base64,{{ $qrImage }}" alt="QR Code {{ $atk->kode_atk }}">
        </div>
        <div class="label-details">
            <div class="label-name">{{ $atk->nama_atk }}</div>
            <div class="label-code">{{ $atk->kode_atk }}</div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .print-area {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 280px;
        margin-top: 8px;
        padding: 24px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.58);
    }
    .qr-label {
        box-sizing: border-box;
        display: flex;
        align-items: center;
        gap: 4mm;
        width: 100mm;
        height: 40mm;
        padding: 2mm;
        background: #fff;
        color: #111;
        font-family: Arial, sans-serif;
        border-radius: 10px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
    }
    .qr-wrapper { flex: 0 0 34mm; width: 34mm; height: 34mm; }
    .qr-wrapper img { display: block; width: 100%; height: 100%; }
    .label-details { min-width: 0; flex: 1; display: flex; flex-direction: column; align-items: flex-start; gap: 2mm; }
    .label-name { display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; overflow: hidden; max-width: 100%; font-size: 16pt; line-height: 1.1; font-weight: 700; overflow-wrap: anywhere; }
    .label-code { max-width: 100%; padding: 1mm 3mm; border-radius: 10mm; background: #f3f4f6; font-size: 14pt; line-height: 1.2; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    @media print {
        .d-sidebar, .d-topbar, .d-footer, .no-print, [data-shell-sidebar], [data-shell-topbar] {
            display: none !important;
        }
        body, html {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .main { margin: 0 !important; }
        .shell { display: block !important; }
        .content { width: 100% !important; min-height: 0 !important; padding: 0 !important; }
        .print-area {
            display: flex;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            width: 100%;
            min-height: 277mm;
            margin: 0;
            padding: 0;
            border-radius: 0;
            background: #fff !important;
        }
        .qr-label {
            page-break-inside: avoid;
            margin: 0 !important;
            border-radius: 0;
            box-shadow: none;
        }
        @page { size: A4 portrait; margin: 10mm; }
    }
</style>
@endpush
@endsection
