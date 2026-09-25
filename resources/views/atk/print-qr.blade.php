@extends('layouts.adminator')

@section('title', 'Cetak QR Code')

@section('content')
<div class="action-bar" style="margin-bottom: 20px; display: flex; gap: 10px;">
    <button class="btn" style="background-color: #4b5563; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 4px;" onclick="window.close()">Tutup</button>
    <button class="btn btn--primary" style="padding: 10px 20px; font-size: 14px; border-radius: 4px;" onclick="window.print()">Cetak Label QR (Print)</button>
</div>

<div class="qr-card" style="background: #ffffff; border: 2px dashed #9ca3af; border-radius: 12px; width: 320px; padding: 24px; text-align: center; margin: 0 auto;">
    <div class="logo-header" style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 12px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        <img src="{{ asset('images/bank-nagari-logo.svg') }}" alt="Bank Nagari" style="height: 28px; width: auto;">
        <span style="font-size: 13px; font-weight: 800; color: #111827; letter-spacing: -0.5px;">DIVISI UMUM (GAS)</span>
    </div>
    
    <div class="kode-badge" style="background-color: #eff6ff; color: #1d4ed8; font-size: 16px; font-weight: 800; padding: 6px 12px; border-radius: 6px; display: inline-block; margin-bottom: 8px;">{{ $atk->kode_atk }}</div>
    
    <div class="qr-wrapper" style="margin: 14px auto; display: flex; justify-content: center;">
        <img src="data:image/svg+xml;base64,{{ $qrImage }}" alt="QR Code {{ $atk->kode_atk }}" style="width: 170px; height: 170px;">
    </div>

    <div class="item-name" style="font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 4px;">{{ $atk->nama_atk }}</div>
    <div class="item-category" style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Jenis: {{ $atk->jenis_atk }} | Satuan: {{ $atk->satuan }}</div>

    <div class="footer-text" style="border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600;">
        Sistem Inventaris ATK &bull; Bank Nagari
    </div>
</div>

@push('styles')
<style>
    @media print {
        .d-sidebar, .d-topbar, [data-shell-sidebar], [data-shell-topbar], .action-bar, .crumbs {
            display: none !important;
        }
        .main { margin-left: 0 !important; }
        .content { padding: 0 !important; }
        .qr-card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            margin: 0 auto !important;
            page-break-inside: avoid;
        }
        body { background: transparent !important; }
    }
</style>
@endpush
@endsection
