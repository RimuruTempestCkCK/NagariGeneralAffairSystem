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
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.atk.index') : route(Auth::user()->role . '.dashboard') }}" class="btn btn--ghost">
            &larr; Kembali
        </a>
    </div>
</section>

<div style="display: flex; flex-direction: column; gap: 20px; max-width: 600px; margin: 0 auto;">
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
                    <button type="button" onclick="lookupCode(document.getElementById('manual-code').value)" class="btn btn--primary">Cari</button>
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
                        <h2 id="res-kode" style="margin: 5px 0 0 0; font-size: 24px; color: var(--primary);"></h2>
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
                    <a id="res-print-link" href="#" target="_blank" class="btn btn--primary btn--block">
                        Cetak Label QR
                    </a>
                </div>
                @else
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-soft);">
                    <h3 style="margin-bottom: 15px;">Catat Pemakaian ATK</h3>
                    <form id="form-pemakaian-scan" onsubmit="submitPemakaian(event)">
                        <input type="hidden" id="pemakaian-atk-id">
                        <div class="form-group">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" id="pemakaian-unit" class="input" required placeholder="Contoh: Divisi IT">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jumlah Pemakaian</label>
                            <input type="number" id="pemakaian-qty" class="input" min="1" required placeholder="Jumlah yang diambil">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Keperluan (Opsional)</label>
                            <textarea id="pemakaian-keperluan" class="textarea" rows="2" placeholder="Keperluan..."></textarea>
                        </div>
                        <button type="submit" class="btn btn--primary btn--block">
                            Submit Pemakaian
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let isLookingUp = false;
    let html5Qrcode = null;
    let currentAtkId = null;
    let currentMaxStok = 0;
    
    let scannerStarting = false;
    let scannerRunning = false;

    document.addEventListener('DOMContentLoaded', async function () {
        if (typeof Html5Qrcode === 'undefined') {
            console.error("[Scanner] Html5Qrcode library failed to load.");
            document.getElementById('reader').innerHTML = '<div style="padding:20px;color:red;">Library scanner gagal dimuat. Periksa koneksi internet.</div>';
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showCameraError(new Error("API Kamera (MediaDevices) tidak tersedia."), "Pastikan akses menggunakan HTTPS.");
            return;
        }

        console.log("[Scanner] Environment validated. Requesting initial permission...");

        try {
            // Permission Primer
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
            console.log("[Scanner] Initial permission SUCCESS");
            stream.getTracks().forEach(track => track.stop());
            
            // Allow hardware to release before starting the real scanner
            setTimeout(startScanner, 200);
        } catch (err) {
            console.error("[Scanner] Initial permission FAILED:", err);
            showCameraError(err, "Gagal mendapatkan izin awal kamera dari browser.");
        }
    });

    async function startScanner() {
        if (scannerStarting || scannerRunning) return;
        scannerStarting = true;

        html5Qrcode = new Html5Qrcode("reader");

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        try {
            console.log("[Scanner] Starting Html5Qrcode with { facingMode: 'environment' }");
            // Menggunakan facingMode: "environment" sesuai spesifikasi ketat library
            await html5Qrcode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure);
            console.log("[Scanner] Scanner started successfully");
            scannerRunning = true;
            scannerStarting = false;
        } catch (err1) {
            console.warn("[Scanner] facingMode 'environment' failed:", err1);
            
            try {
                console.log("[Scanner] Starting enumeration fallback");
                const devices = await Html5Qrcode.getCameras();
                if (devices && devices.length > 0) {
                    let cameraId = null;
                    for (let i = 0; i < devices.length; i++) {
                        const label = devices[i].label.toLowerCase();
                        if (label.includes('back') || label.includes('rear') || label.includes('environment')) {
                            cameraId = devices[i].id;
                            break;
                        }
                    }
                    if (!cameraId) cameraId = devices[devices.length - 1].id;

                    await html5Qrcode.start(cameraId, config, onScanSuccess, onScanFailure);
                    console.log("[Scanner] Scanner started via fallback camera ID");
                    scannerRunning = true;
                    scannerStarting = false;
                } else {
                    scannerStarting = false;
                    showCameraError(new Error("Daftar kamera kosong."), "Kamera tidak terdeteksi saat enumerasi.");
                }
            } catch (enumErr) {
                scannerStarting = false;
                console.error("[Scanner] Fallback enumeration failed:", enumErr);
                showCameraError(enumErr, "Gagal menggunakan kamera secara murni dan fallback enumerasi perangkat ditolak.");
            }
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        if(isLookingUp) return;
        let code = decodedText.trim();
        if (code.includes('/')) {
            const parts = code.split('/');
            code = parts[parts.length - 1];
        }
        
        if (html5Qrcode && html5Qrcode.getState() === 2) {
            html5Qrcode.pause();
        }
        
        lookupCode(code);
    }

    function onScanFailure(errorMessage) {
        // Ignore silent background scan errors
    }

    function showCameraError(err, contextMsg = "") {
        console.error("Camera error object:", err);
        
        let errName = err && err.name ? err.name : "Unknown";
        let errMsg = err && err.message ? err.message : String(err);
        
        let msg = 'Terjadi kendala saat mengakses kamera.';
        
        if (errName === 'NotAllowedError') {
            msg = 'Akses kamera ditolak oleh pengguna.';
        } else if (errName === 'NotFoundError') {
            msg = 'Kamera tidak ditemukan di perangkat ini.';
        } else if (errName === 'NotReadableError') {
            msg = 'Kamera sedang digunakan oleh aplikasi lain atau hardware bermasalah.';
        } else if (errName === 'OverconstrainedError') {
            msg = 'Kamera tidak memenuhi constraint yang diminta.';
        } else if (errName === 'SecurityError') {
            msg = 'Akses ditolak karena masalah keamanan. Pastikan akses dari HTTPS.';
        } else if (errName === 'AbortError') {
            msg = 'Proses dibatalkan.';
        }
        
        let finalHtml = `<div style="padding:20px;color:red; background: var(--bg-muted); border: 1px solid var(--border-soft); border-radius: 8px;">
            <strong style="font-size: 16px;">${msg}</strong><br><br>
            <div style="font-family: monospace; font-size: 12px; color: var(--t-muted); word-break: break-all;">
                Error Name: ${errName}<br>
                Error Message: ${errMsg}
            </div>
        `;
        
        if (contextMsg) {
            finalHtml += `<br><div style="font-size: 13px; font-weight: bold; color: var(--danger);">Konteks: ${contextMsg}</div>`;
        }
        
        finalHtml += `</div>`;
        document.getElementById('reader').innerHTML = finalHtml;
    }

    function resumeScanner() {
        if(html5Qrcode && html5Qrcode.getState() === 3 /* PAUSED */) {
            try { html5Qrcode.resume(); } catch(e){}
        }
        isLookingUp = false;
    }

    function lookupCode(code) {
        if (!code || code.trim() === '') {
            Swal.fire('Perhatian', 'Silakan masukkan kode ATK terlebih dahulu.', 'warning').then(() => { resumeScanner(); });
            return;
        }

        if(isLookingUp) return;
        isLookingUp = true;
        
        code = code.trim();
        const role = window.GAS_USER_ROLE || 'staff';
        
        Swal.fire({
            title: 'Mencari Data...',
            text: 'Tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`/${role}/atk/scan/lookup/${encodeURIComponent(code)}`)
            .then(res => {
                if(!res.ok && res.status === 403) throw new Error("Akses ditolak (403).");
                return res.json();
            })
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
                    statusBadge.className = d.status === 'Aktif' ? 'tag t-active' : 'tag t-unavail';

                    const printLink = document.getElementById('res-print-link');
                    if(printLink) printLink.href = `/admin/atk/${d.id}/print-qr`;
                    
                    const atkIdInput = document.getElementById('pemakaian-atk-id');
                    if(atkIdInput) {
                        atkIdInput.value = d.id;
                        currentAtkId = d.id;
                        currentMaxStok = d.jumlah;
                        document.getElementById('pemakaian-qty').max = d.jumlah;
                        document.getElementById('pemakaian-qty').value = '';
                    }

                    Swal.fire({ icon: 'success', title: 'ATK Ditemukan!', text: `${d.nama_atk} (${d.kode_atk})`, timer: 1500, showConfirmButton: false }).then(() => { resumeScanner(); });
                    document.getElementById('manual-code').value = '';
                } else {
                    Swal.fire('Data Tidak Ditemukan', res.message || 'Data ATK tidak ditemukan.', 'error').then(() => { resumeScanner(); });
                }
            })
            .catch((e) => {
                Swal.fire('Error', e.message || 'Terjadi kesalahan jaringan.', 'error').then(() => { resumeScanner(); });
            });
    }

    async function submitPemakaian(e) {
        e.preventDefault();
        if(!currentAtkId) return;
        
        const qty = parseInt(document.getElementById('pemakaian-qty').value) || 0;
        if (qty > currentMaxStok) {
            Swal.fire('Stok Kurang', `Jumlah (${qty}) melebihi stok tersedia (${currentMaxStok}).`, 'error');
            return;
        }
        
        const role = window.GAS_USER_ROLE || 'staff';
        const payload = {
            atk_id: currentAtkId,
            unit_kerja: document.getElementById('pemakaian-unit').value,
            tanggal: new Date().toISOString().split('T')[0],
            jumlah: qty,
            keperluan: document.getElementById('pemakaian-keperluan').value,
            _token: '{{ csrf_token() }}'
        };
        
        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        try {
            const res = await fetch(`/${role}/pemakaian-atk`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
            const data = await res.json();
            
            if(data.success) {
                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                    document.getElementById('scan-result').style.display = 'none';
                    document.getElementById('scan-placeholder').style.display = 'block';
                    currentAtkId = null;
                    document.getElementById('form-pemakaian-scan').reset();
                    resumeScanner();
                });
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
        }
    }
</script>
@endpush
@endsection
