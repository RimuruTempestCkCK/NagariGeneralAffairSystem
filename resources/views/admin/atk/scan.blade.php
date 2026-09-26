@extends('layout.app')

@section('title', 'Scan QR Code ATK')
@section('active_menu', 'scan')
@section('breadcrumbs', 'Modul ATK | QR Scanner')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Modul ATK</span>
        <h1 class="hero-title">Scanner QR Code ATK</h1>
        <p class="hero-sub">Arahkan kamera ke QR Code label ATK atau masukkan kode ATK secara manual untuk melihat data inventaris.</p>
    </div>
    <div class="hero-actions">
        <a href="{{ Auth::user()->role === 'admin' ? route('atk.index') : route(Auth::user()->role . '.dashboard') }}" class="btn btn--ghost">
            &larr; Kembali
        </a>
    </div>
</section>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- Area Kamera Scanner -->
    <section class="card">
        <div class="card-head">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" style="vertical-align: text-bottom; margin-right: 5px;"><path d="M3 9a2 2 0 0 1 2-2h.93a2 2 0 0 0 1.664-.89l.812-1.22A2 2 0 0 1 10.07 4h3.86a2 2 0 0 1 1.664.89l.812 1.22A2 2 0 0 0 18.07 7H19a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"></path><circle cx="12" cy="13" r="3"></circle></svg>
                Kamera Scanner
            </h2>
        </div>
        <div style="padding: 20px;">
            <div id="reader" style="width: 100%; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-soft);"></div>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-soft);">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500;">Atau Masukkan Kode ATK Manual</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="manual-code" class="input" placeholder="Contoh: ATK-00001" style="flex: 1; padding: 10px; border: 1px solid var(--border-soft); border-radius: 6px;">
                    <button type="button" onclick="lookupCode(document.getElementById('manual-code').value)" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Panel Hasil Scan / Detail ATK -->
    <section class="card">
        <div class="card-head">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="var(--success)" stroke-width="2" fill="none" style="vertical-align: text-bottom; margin-right: 5px;"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Hasil Identifikasi
            </h2>
        </div>
        <div style="padding: 20px;">
            <div id="scan-placeholder" style="text-align: center; padding: 40px 20px; color: var(--t-muted);">
                <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1.5" fill="none" style="opacity: 0.5; margin-bottom: 10px;"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <p>Scan QR Code untuk menampilkan rincian barang dari database secara otomatis.</p>
            </div>

            <div id="scan-result" style="display: none;">
                <div style="background: var(--bg-muted); padding: 20px; border-radius: 8px; border: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; color: var(--t-muted); font-weight: bold;">Kode ATK</span>
                        <h2 id="res-kode" style="margin: 5px 0 0 0; font-size: 24px; color: var(--accent);"></h2>
                    </div>
                    <span id="res-status" class="tag" style="font-size: 14px;"></span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 10px;">
                        <span style="font-size: 12px; color: var(--t-muted);">Nama Barang</span>
                        <div id="res-nama" style="font-size: 18px; font-weight: bold;"></div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; border-bottom: 1px solid var(--border-soft); padding-bottom: 10px;">
                        <div>
                            <span style="font-size: 12px; color: var(--t-muted);">Jenis Barang</span>
                            <div id="res-jenis" style="font-weight: 500;"></div>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: var(--t-muted);">Stok Tersedia</span>
                            <div id="res-stok" style="font-weight: bold; color: var(--success);"></div>
                        </div>
                    </div>
                    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 10px;">
                        <span style="font-size: 12px; color: var(--t-muted);">Harga Satuan</span>
                        <div id="res-harga" style="font-weight: bold;"></div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; border-bottom: 1px solid var(--border-soft); padding-bottom: 10px;">
                        <div>
                            <span style="font-size: 12px; color: var(--t-muted);">Rekening Penampungan (BYD)</span>
                            <div id="res-rekening-penampungan" style="font-weight: 500; font-family: monospace;"></div>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: var(--t-muted);">Rekening Biaya</span>
                            <div id="res-rekening-biaya" style="font-weight: 500; font-family: monospace;"></div>
                        </div>
                    </div>
                </div>

                @if(Auth::user()->role === 'admin')
                <div style="margin-top: 20px;">
                    <a id="res-print-link" href="#" target="_blank" class="btn btn-primary" style="width: 100%; text-align: center;">
                        Cetak Label QR
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Html5QrcodeScanner !== 'undefined') {
            const scanner = new Html5QrcodeScanner("reader", { 
                fps: 10, 
                qrbox: { width: 250, height: 250 } 
            });

            scanner.render(onScanSuccess, onScanFailure);

            function onScanSuccess(decodedText) {
                let code = decodedText.trim();
                if (code.includes('/')) {
                    const parts = code.split('/');
                    code = parts[parts.length - 1];
                }
                lookupCode(code);
            }

            function onScanFailure(error) {}
        }
    });

    function lookupCode(code) {
        if (!code) {
            Swal.fire('Perhatian', 'Silakan masukkan kode ATK terlebih dahulu.', 'warning');
            return;
        }

        const role = window.GAS_USER_ROLE || 'staff';
        fetch(`/${role}/atk/scan/lookup/${encodeURIComponent(code)}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    document.getElementById('scan-placeholder').style.display = 'none';
                    document.getElementById('scan-result').style.display = 'block';

                    document.getElementById('res-kode').innerText = d.kode_atk;
                    document.getElementById('res-nama').innerText = d.nama_atk;
                    document.getElementById('res-jenis').innerText = d.jenis_atk;
                    document.getElementById('res-stok').innerText = `${d.jumlah} ${d.satuan}`;
                    document.getElementById('res-harga').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(d.harga)}`;
                    document.getElementById('res-rekening-penampungan').innerText = d.rekening_penampungan || '-';
                    document.getElementById('res-rekening-biaya').innerText = d.rekening_biaya || '-';

                    const statusBadge = document.getElementById('res-status');
                    statusBadge.innerText = d.status;
                    if (d.status === 'Aktif') {
                        statusBadge.className = 'tag t-active';
                    } else {
                        statusBadge.className = 'tag t-unavail';
                    }

                    const printLink = document.getElementById('res-print-link');
                    if(printLink) {
                        printLink.href = `/admin/atk/${d.id}/print-qr`;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'ATK Ditemukan!',
                        text: `${d.nama_atk} (${d.kode_atk})`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Data Tidak Ditemukan', res.message || 'Data ATK tidak ditemukan.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Data Tidak Ditemukan', 'Data ATK tidak ditemukan untuk kode: ' + code, 'error');
            });
    }
</script>
@endpush
@endsection
