@extends("layout.app")

@section("title", "Dashboard Staff")
@section("active_menu", "dashboard")
@section("breadcrumbs", "Menu Utama | Dashboard")

@section("content")
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow" id="heroDate">{{ \Carbon\Carbon::now()->format("l, d F Y") }}</span>
        <h1 class="hero-title">Selamat datang, <span class="accent">{{ Auth::user()->name }}</span>!</h1>
        <p class="hero-sub">Ringkasan aktivitas Anda di General Affair System.</p>
    </div>
</section>

<!-- Stats Grid -->
<div class="grid kpi-grid" style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
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
        <div class="mt-2"><a href="{{ route("staff.keamanan.index") }}" class="text-blue-500 text-sm hover:underline">Kelola laporan &rarr;</a></div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid" style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
    <!-- Chart A: Status Permintaan ATK -->
    <div class="card" style="padding:20px;">
        <div class="card-head">
            <h2 class="card-title">Status Permintaan ATK Saya</h2>
        </div>
        <div style="position: relative; height: 300px; width: 100%; display: flex; align-items: center; justify-content: center;">
            @if(array_sum($permintaanByStatus) === 0)
                <p class="text-gray-500" style="text-align:center;">Belum ada data permintaan untuk ditampilkan.</p>
            @else
                <canvas id="statusChart"></canvas>
            @endif
        </div>
    </div>

    <!-- Chart B: Tren Permintaan ATK -->
    <div class="card" style="padding:20px;">
        <div class="card-head">
            <h2 class="card-title">Tren Permintaan ATK Saya (6 Bulan)</h2>
        </div>
        <div style="position: relative; height: 300px; width: 100%; display: flex; align-items: center; justify-content: center;">
            @if(array_sum($trenPermintaan["data"]) === 0)
                <p class="text-gray-500" style="text-align:center;">Belum ada tren permintaan untuk ditampilkan.</p>
            @else
                <canvas id="trendAtkChart"></canvas>
            @endif
        </div>
    </div>
</div>

<section class="card col-12" style="min-height: 250px;">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Aktivitas Saya</span>
            <h2 class="card-title">Permintaan ATK Terbaru</h2>
        </div>
        <a class="card-action" href="{{ route("staff.permintaan-atk.index") }}">Semua Transaksi &rarr;</a>
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
                <td class="cell-date">{{ $trans->created_at->format("d M Y") }}</td>
                <td>{{ $trans->nomor_po }}</td>
                <td>{{ $trans->unit_kerja }}</td>
                <td>
                    @if($trans->status === "PENDING")
                        <span class="tag t-used">Pending</span>
                    @elseif($trans->status === "APPROVED")
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

@push("scripts")
<!-- Adminator hides window.Chart in its Webpack bundle, so we load it explicitly for this view -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof Chart === "undefined") {
        console.warn("Chart.js is not loaded.");
        return;
    }

    const colors = {
        primary: "#3498db",
        success: "#2ecc71",
        warning: "#f1c40f",
        danger: "#e74c3c",
        neutral: "#95a5a6",
        background: "rgba(52, 152, 219, 0.1)",
        border: "rgba(52, 152, 219, 0.5)"
    };

    // 1. Status Chart (Doughnut)
    const ctxStatus = document.getElementById("statusChart");
    if (ctxStatus) {
        const dataStatus = @json(array_values($permintaanByStatus));
        const labelsStatus = @json(array_keys($permintaanByStatus));
        
        new Chart(ctxStatus, {
            type: "doughnut",
            data: {
                labels: labelsStatus,
                datasets: [{
                    data: dataStatus,
                    backgroundColor: [colors.neutral, colors.warning, colors.success, colors.danger, colors.primary],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "70%",
                plugins: {
                    legend: {
                        position: "right",
                        labels: { font: { family: "Inter, sans-serif" } }
                    }
                }
            }
        });
    }

    // 2. Trend ATK Chart (Line)
    const ctxTrendAtk = document.getElementById("trendAtkChart");
    if (ctxTrendAtk) {
        const trenAtkData = @json($trenPermintaan);
        
        new Chart(ctxTrendAtk, {
            type: "line",
            data: {
                labels: trenAtkData.labels,
                datasets: [{
                    label: "Jumlah Permintaan",
                    data: trenAtkData.data,
                    backgroundColor: colors.background,
                    borderColor: colors.primary,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
});
</script>
@endpush


