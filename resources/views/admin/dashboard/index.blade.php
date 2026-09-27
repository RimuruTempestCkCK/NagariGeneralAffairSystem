@extends("layout.app")

@section("title", "Dashboard Admin")
@section("active_menu", "dashboard")
@section("breadcrumbs", "Menu Utama | Dashboard")

@section("content")
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow" id="heroDate">{{ \Carbon\Carbon::now()->format("l, d F Y") }}</span>
        <h1 class="hero-title">Selamat datang, <span class="accent">{{ Auth::user()->name }}</span>!</h1>
        <p class="hero-sub">Ini adalah ringkasan data General Affair System terkini.</p>
    </div>
</section>

<!-- Stats Grid -->
<div class="grid kpi-grid grid--kpi">
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
        <div class="mt-2"><a href="{{ route("admin.keamanan.index") }}" class="text-blue-500 text-sm hover:underline">Lihat semua laporan &rarr;</a></div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid grid--charts">
    <!-- Chart A: Status Permintaan ATK -->
    <div class="card" style="padding:20px;">
        <div class="card-head">
            <h2 class="card-title">Status Permintaan ATK</h2>
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
            <h2 class="card-title">Tren Permintaan ATK (6 Bulan)</h2>
        </div>
        <div style="position: relative; height: 300px; width: 100%; display: flex; align-items: center; justify-content: center;">
            @if(array_sum($trenPermintaan["data"]) === 0)
                <p class="text-gray-500" style="text-align:center;">Belum ada tren permintaan untuk ditampilkan.</p>
            @else
                <canvas id="trendAtkChart"></canvas>
            @endif
        </div>
    </div>

    <!-- Chart C: Tren Perjalanan Kendaraan -->
    <div class="card" style="padding:20px;">
        <div class="card-head">
            <h2 class="card-title">Aktivitas Kendaraan (6 Bulan)</h2>
        </div>
        <div style="position: relative; height: 300px; width: 100%; display: flex; align-items: center; justify-content: center;">
            @if(array_sum($trenPerjalanan["data"]) === 0)
                <p class="text-gray-500" style="text-align:center;">Belum ada aktivitas kendaraan untuk ditampilkan.</p>
            @else
                <canvas id="trendKendaraanChart"></canvas>
            @endif
        </div>
    </div>
</div>

<section class="card col-12" style="min-height: 250px;">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Aktivitas</span>
            <h2 class="card-title">Permintaan ATK Terbaru</h2>
        </div>
        <a class="card-action" href="{{ route("admin.permintaan-atk.index") }}">Semua Transaksi &rarr;</a>
    </div>
    <div class="table-scroll">
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
                <td class="cell-date">{{ $trans->created_at->format("d M Y") }}</td>
                <td>{{ $trans->nomor_po }}</td>
                <td>{{ $trans->user->name ?? "-" }}</td>
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
                <td colspan="5" class="text-center py-4">Belum ada aktivitas terbaru.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</section>
@endsection

@push("scripts")
<!-- Adminator hides window.Chart in its Webpack bundle, so we load it explicitly for this view -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Use existing chart.js from adminator or global window.Chart -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // We assume Chart is available globally via Adminator vendor JS.
    if (typeof Chart === "undefined") {
        console.warn("Chart.js is not loaded.");
        return;
    }

    // Colors that fit Adminator style (restrained, professional)
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
                        labels: {
                            font: { family: "Inter, sans-serif" }
                        }
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
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 3. Trend Kendaraan Chart (Bar)
    const ctxTrendKendaraan = document.getElementById("trendKendaraanChart");
    if (ctxTrendKendaraan) {
        const trenKendaraanData = @json($trenPerjalanan);
        
        new Chart(ctxTrendKendaraan, {
            type: "bar",
            data: {
                labels: trenKendaraanData.labels,
                datasets: [{
                    label: "Perjalanan Kendaraan",
                    data: trenKendaraanData.data,
                    backgroundColor: colors.success,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
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


