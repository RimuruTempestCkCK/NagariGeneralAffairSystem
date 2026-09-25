@extends('layouts.dashboard')

@section('title', 'Scan QR Code ATK')

@section('content')
<div class="px-4 pt-6">
    <div class="p-4 bg-white border border-gray-200 rounded-2xl shadow-sm mb-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white">Scanner QR Code Master ATK</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Arahkan kamera ke QR Code label ATK atau masukkan kode ATK secara manual untuk melihat data inventaris.</p>
            </div>
            <a href="{{ route('atk.index') }}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">
                &larr; Kembali ke Master ATK
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Area Kamera Scanner -->
        <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Kamera Scanner
            </h3>
            <div id="reader" class="w-full rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700"></div>

            <!-- Alternatif input manual -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <label for="manual-code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Atau Masukkan Kode ATK Manual</label>
                <div class="flex gap-2">
                    <input type="text" id="manual-code" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: ATK-00001">
                    <button type="button" onclick="lookupCode(document.getElementById('manual-code').value)" class="text-white bg-primary-700 hover:bg-primary-800 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700">Cari</button>
                </div>
            </div>
        </div>

        <!-- Panel Hasil Scan / Detail ATK -->
        <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Hasil Identifikasi ATK
            </h3>

            <div id="scan-placeholder" class="py-12 text-center text-gray-400 dark:text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <p class="text-sm">Scan QR Code untuk menampilkan rincian barang dari database secara otomatis.</p>
            </div>

            <div id="scan-result" class="hidden space-y-4">
                <div class="p-4 bg-primary-50 rounded-xl border border-primary-100 dark:bg-gray-700/50 dark:border-gray-600 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-primary-700 dark:text-primary-300 uppercase tracking-wider">Kode ATK</span>
                        <h2 id="res-kode" class="text-2xl font-black text-primary-900 dark:text-white"></h2>
                    </div>
                    <span id="res-status" class="px-3 py-1 rounded-full text-xs font-bold"></span>
                </div>

                <div class="space-y-3">
                    <div class="border-b pb-2 dark:border-gray-700">
                        <span class="text-xs text-gray-500">Nama Barang</span>
                        <p id="res-nama" class="text-lg font-bold text-gray-900 dark:text-white"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 border-b pb-2 dark:border-gray-700">
                        <div>
                            <span class="text-xs text-gray-500">Jenis Barang</span>
                            <p id="res-jenis" class="text-sm font-semibold text-gray-800 dark:text-gray-200"></p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Stok Tersedia</span>
                            <p id="res-stok" class="text-sm font-bold text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                    <div class="border-b pb-2 dark:border-gray-700">
                        <span class="text-xs text-gray-500">Harga Satuan</span>
                        <p id="res-harga" class="text-base font-bold text-gray-900 dark:text-white"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 border-b pb-2 dark:border-gray-700">
                        <div>
                            <span class="text-xs text-gray-500">Rekening Penampungan (BYD)</span>
                            <p id="res-rekening-penampungan" class="text-sm font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Rekening Biaya</span>
                            <p id="res-rekening-biaya" class="text-sm font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex gap-3">
                    <a id="res-print-link" href="#" target="_blank" class="w-full text-center text-white bg-primary-700 hover:bg-primary-800 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700">
                        Cetak Label QR
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Html5QrcodeScanner !== 'undefined') {
            const scanner = new Html5QrcodeScanner("reader", { 
                fps: 10, 
                qrbox: { width: 250, height: 250 } 
            });

            scanner.render(onScanSuccess, onScanFailure);

            function onScanSuccess(decodedText) {
                // Ekstrak kode jika URL discan
                let code = decodedText.trim();
                if (code.includes('/')) {
                    const parts = code.split('/');
                    code = parts[parts.length - 1];
                }
                lookupCode(code);
            }

            function onScanFailure(error) {
                // Lewatkan frame gagal biasa
            }
        }
    });

    function lookupCode(code) {
        if (!code) {
            Swal.fire('Perhatian', 'Silakan masukkan kode ATK terlebih dahulu.', 'warning');
            return;
        }

        fetch(`/atk/scan/lookup/${encodeURIComponent(code)}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    document.getElementById('scan-placeholder').classList.add('hidden');
                    document.getElementById('scan-result').classList.remove('hidden');

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
                        statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                    } else {
                        statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                    }

                    document.getElementById('res-print-link').href = `/atk/${d.id}/print-qr`;

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
@endsection
