@extends('layout.app')

@section('title', 'Dashboard Admin')
@section('active_menu', 'dashboard')
@section('breadcrumbs', 'Menu Utama | Dashboard')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow" id="heroDate">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</span>
        <h1 class="hero-title">Selamat datang, <span class="accent">{{ Auth::user()->name }}</span>!</h1>
        <p class="hero-sub">Ini adalah ringkasan data General Affair System terkini.</p>
    </div>
</section>

<!-- Stats Grid -->
<div class="grid kpi-grid" style="margin-bottom: 20px;">
    <!-- Master ATK -->
    <div class="card" style="padding:20px;">
        <span class="eyebrow">Master ATK</span>
        <h2 class="card-title">{{ $totalAtk }} Item</h2>
        <div class="text-sm mt-2 text-gray-500">Total Stok Tersedia: <strong>{{ $totalStok }}</strong></div>
        @if($pendingPermintaan > 0)
        <div class="mt-2"><span class="tag t-used">{{ $pendingPermintaan }} Permintaan Pending</span></div>
        @endif
    </div>
    
    <!-- Kendaraan -->
    <div class="card" style="padding:20px;">
        <span class="eyebrow">Kendaraan</span>
        <h2 class="card-title">{{ $totalKendaraan }} Unit</h2>
        @if($expiringStnk > 0)
        <div class="mt-2"><span class="tag t-unavail">{{ $expiringStnk }} STNK Mendekati Jatuh Tempo</span></div>
        @else
        <div class="mt-2"><span class="tag t-new">STNK Aman</span></div>
        @endif
    </div>

    <!-- Aset -->
    <div class="card" style="padding:20px;">
        <span class="eyebrow">Aset Perusahaan</span>
        <h2 class="card-title">{{ $totalAset }} Aset</h2>
        @if($expiringAset > 0)
        <div class="mt-2"><span class="tag t-unavail">{{ $expiringAset }} Sertifikat Mendekati Jatuh Tempo</span></div>
        @else
        <div class="mt-2"><span class="tag t-new">Sertifikat Aman</span></div>
        @endif
    </div>

    <!-- Keamanan -->
    <div class="card" style="padding:20px;">
        <span class="eyebrow">Laporan Keamanan</span>
        <h2 class="card-title">{{ $totalKeamanan }} Laporan</h2>
        <div class="mt-2"><a href="{{ route('admin.keamanan.index') }}" class="text-blue-500 text-sm hover:underline">Lihat semua laporan &rarr;</a></div>
    </div>
</div>

<section class="card col-12" style="min-height: 250px;">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Aktivitas</span>
            <h2 class="card-title">Permintaan ATK Terbaru</h2>
        </div>
        <a class="card-action" href="{{ route('admin.permintaan-atk.index') }}">Semua Transaksi &rarr;</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nomor PO</th>
                <th>Pemohon</th>
                <th>Unit Kerja</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTrans as $trans)
            <tr>
                <td class="cell-date">{{ $trans->created_at->format('d M Y') }}</td>
                <td>{{ $trans->nomor_po }}</td>
                <td>{{ $trans->user->name ?? '-' }}</td>
                <td>{{ $trans->unit_kerja }}</td>
                <td>
                    @if($trans->status === 'Pending')
                        <span class="tag t-used">Pending</span>
                    @elseif($trans->status === 'Disetujui' || $trans->status === 'Approved')
                        <span class="tag t-new">Disetujui</span>
                    @else
                        <span class="tag t-unavail">{{ $trans->status }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4">Belum ada aktivitas terbaru.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
