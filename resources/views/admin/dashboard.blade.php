@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="px-4 pt-6">
    <!-- Header banner -->
    <div class="p-6 mb-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Selamat Datang, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Panel Administrator Sistem General Affair Bank Nagari (GAS / SIDUM)
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                    <span class="w-2 h-2 mr-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    Role: Administrator
                </span>
            </div>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid w-full grid-cols-1 gap-4 mt-4 xl:grid-cols-4 sm:grid-cols-2">
        <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Jenis ATK</h3>
                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">128 Item</span>
                <p class="flex items-center text-xs text-green-500 mt-2 font-medium">
                    +4 item bulan ini
                </p>
            </div>
        </div>
        <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Kendaraan Dinas</h3>
                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">42 Unit</span>
                <p class="flex items-center text-xs text-blue-500 mt-2 font-medium">
                    38 Aktif, 4 Servis
                </p>
            </div>
        </div>
        <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Sertifikat Aset</h3>
                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">86 Dokumen</span>
                <p class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-2 font-medium">
                    Kantor Pusat & Cabang
                </p>
            </div>
        </div>
        <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Permintaan Pending</h3>
                <span class="text-2xl font-bold leading-none text-yellow-600 sm:text-3xl dark:text-yellow-400">7 Pengajuan</span>
                <p class="flex items-center text-xs text-yellow-600 dark:text-yellow-400 mt-2 font-medium">
                    Perlu Approval
                </p>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="p-4 mt-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Aktivitas Operasional & Modul GAS
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 border border-gray-100 rounded-lg bg-gray-50 dark:bg-gray-700/50 dark:border-gray-600">
                <h4 class="font-semibold text-gray-900 dark:text-white">1. Modul ATK</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Master Data, Approval PO, Stok Masuk & Jurnal Pembukuan Pemakaian.</p>
            </div>
            <div class="p-4 border border-gray-100 rounded-lg bg-gray-50 dark:bg-gray-700/50 dark:border-gray-600">
                <h4 class="font-semibold text-gray-900 dark:text-white">2. Kendaraan & Keamanan</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Jarak tempuh, BBM, pemeliharaan suku cadang & laporan pengamanan.</p>
            </div>
            <div class="p-4 border border-gray-100 rounded-lg bg-gray-50 dark:bg-gray-700/50 dark:border-gray-600">
                <h4 class="font-semibold text-gray-900 dark:text-white">3. Daftar Kepemilikan Aset</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data sertifikat tanah, lokasi, jatuh tempo, serta lampiran bukti.</p>
            </div>
        </div>
    </div>
</div>
@endsection
