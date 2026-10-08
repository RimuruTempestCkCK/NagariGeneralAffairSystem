@extends('layout.app')

@section('title', 'Bulk Print QR ATK')
@section('active_menu', 'atk')
@section('breadcrumbs', 'Modul ATK | Master ATK | Bulk Print QR')

@section('content')
<div class="px-4 pt-6 no-print">
    <div class="p-4 bg-white border border-gray-200 rounded-2xl shadow-sm mb-6   flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl ">Cetak QR Code Massal</h1>
            <p class="text-sm text-gray-500  mt-1">Cetak beberapa label QR Code secara bersamaan.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn btn--ghost" onclick="window.close()">Tutup</button>
            <button class="btn btn--primary" onclick="window.print()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" style="margin-right: 5px;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Print Sekarang
            </button>
        </div>
    </div>
</div>

<div class="print-container">
    @foreach($atks as $atk)
        <div class="label-box">
            <div class="qr-wrapper">
                {!! QrCode::format('svg')->size(136)->generate($atk->kode_atk) !!}
            </div>
            <div class="label-details">
                <div class="label-name">{{ $atk->nama_atk }}</div>
                <div class="label-code">{{ $atk->kode_atk }}</div>
            </div>
        </div>
    @endforeach
</div>

@push('styles')
<style>
    .print-container {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 20px;
    }
    .label-box {
        box-sizing: border-box;
        display: flex;
        align-items: center;
        gap: 4mm;
        width: 100mm;
        height: 40mm;
        padding: 2mm;
        color: #111;
        background: #fff;
        font-family: Arial, sans-serif;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
    }
    .qr-wrapper { flex: 0 0 34mm; width: 34mm; height: 34mm; }
    .qr-wrapper svg { display: block; width: 100%; height: 100%; }
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
        .content { padding: 0 !important; }
        .label-box {
            break-inside: avoid;
            page-break-inside: avoid;
            background: white !important;
            border-radius: 0;
            box-shadow: none;
        }
        .print-container {
            gap: 5mm;
            padding: 0;
        }
        @page { margin: 10mm; size: A4 portrait; }
    }
</style>
@endpush

@push('scripts')
<script>
    window.addEventListener('load', function () {
        if (window.__bulkPrintTriggered) {
            return;
        }

        window.__bulkPrintTriggered = true;

        setTimeout(function () {
            window.print();
        }, 500);
    });
</script>
@endpush
@endsection
