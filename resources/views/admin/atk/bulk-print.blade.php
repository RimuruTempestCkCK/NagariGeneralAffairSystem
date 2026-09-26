@extends('layout.app')

@section('title', 'Bulk Print QR ATK')
@section('active_menu', 'atk')
@section('breadcrumbs', 'Modul ATK | Master ATK | Bulk Print QR')

@section('content')
<div class="px-4 pt-6 no-print">
    <div class="p-4 bg-white border border-gray-200 rounded-2xl shadow-sm mb-6 dark:border-gray-700 dark:bg-gray-800 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white">Cetak QR Code Massal</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cetak beberapa label QR Code secara bersamaan.</p>
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

<div class="print-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; padding: 20px;">
    @foreach($atks as $atk)
        <div class="label-box" style="border: 2px dashed var(--border-soft); padding: 15px; text-align: center; border-radius: 8px; background: var(--bg-base);">
            <div class="qr-wrapper" style="margin-bottom: 10px; display: flex; justify-content: center;">
                {!! QrCode::format('svg')->size(100)->generate($atk->kode_atk) !!}
            </div>
            <div class="label-code" style="font-weight: bold; font-size: 16px; margin-bottom: 5px; color: var(--accent);">{{ $atk->kode_atk }}</div>
            <div class="label-name" style="font-size: 13px; color: var(--t-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $atk->nama_atk }}</div>
        </div>
    @endforeach
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
        .label-box { 
            border: 1px solid #000 !important; 
            break-inside: avoid;
            page-break-inside: avoid;
            background: white !important;
        }
        .print-container {
            grid-template-columns: repeat(4, 1fr) !important;
        }
        @page { margin: 1cm; size: A4 portrait; }
    }
</style>
@endpush

@push('scripts')
<script>
    window.onload = function() {
        // Otomatis trigger print setelah render
        setTimeout(() => {
            window.print();
        }, 500);
    }
</script>
@endpush
@endsection
