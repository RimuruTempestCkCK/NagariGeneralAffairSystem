@extends('layout.app')

@section('title', 'Dashboard Staff')
@section('active_menu', 'dashboard')
@section('breadcrumbs', 'Menu Utama | Dashboard')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow" id="heroDate">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</span>
        <h1 class="hero-title">Selamat datang, <span class="accent">{{ Auth::user()->name }}</span>!</h1>
        <p class="hero-sub">Ringkasan aktivitas Anda di General Affair System.</p>
    </div>
</section>

<!-- Stats Grid -->
<div class="grid kpi-grid" style="margin-bottom: 20px;">
    <!-- Permintaan ATK -->
    <div class="card" style="padding:20px;">
        <span class="eyebrow">Permintaan ATK Saya</span>
        <h2 class="card-title">{{ $myPermintaan }} Transaksi</h2>
        @if($pendingPermintaan > 0)
        <div class="mt-2"><span class="tag t-used">{{ $pendingPermintaan }} Menunggu Persetujuan</span></div>
        @else
        <div class="mt-2"><span class="tag t-new">Semua selesai diproses</span></div>
        @endif
    </div>
    
    <!-- Laporan Keamanan -->
    <div class="card" style="padding:20px;">
        <span class="eyebrow">Laporan Keamanan Saya</span>
        <h2 class="card-title">{{ $myKeamanan }} Laporan</h2>
        <div class="mt-2"><a href="{{ route('keamanan.index') }}" class="text-blue-500 text-sm hover:underline">Kelola laporan &rarr;</a></div>
    </div>
</div>

<section class="card col-12" style="min-height: 250px;">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Aktivitas Saya</span>
            <h2 class="card-title">Permintaan ATK Terbaru</h2>
        </div>
        <a class="card-action" href="{{ route('permintaan-atk.index') }}">Semua Transaksi &rarr;</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nomor PO</th>
                <th>Unit Kerja</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTrans as $trans)
            <tr>
                <td class="cell-date">{{ $trans->created_at->format('d M Y') }}</td>
                <td>{{ $trans->nomor_po }}</td>
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
                <td colspan="4" class="text-center py-4">Belum ada aktivitas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
