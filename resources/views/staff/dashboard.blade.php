@extends('layouts.adminator')

@section('title', 'Staff Dashboard')

@section('content')
<div class="px-4 pt-6">
    <!-- Header banner -->
    <div class="p-6 mb-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Halo, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Portal Layanan Operasional & Pengajuan Divisi Umum Bank Nagari
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                    <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full"></span>
                    Role: Staff Operasional
                </span>
            </div>
        </div>
    </div>

    <!-- Menu Cepat Pengajuan -->
    <div class="grid w-full grid-cols-1 gap-4 xl:grid-cols-3 sm:grid-cols-2">
        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-300">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                </div>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">PO / ATK</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pengajuan ATK</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-4">Ajukan kebutuhan alat tulis kantor untuk unit kerja Anda.</p>
            <a href="#" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
                Buat Permintaan &rarr;
            </a>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900 flex items-center justify-center text-orange-600 dark:text-orange-300">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                </div>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">Kendaraan</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Log BBM & Servis</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-4">Catat kilometer perjalanan, struk pembelian BBM, dan pemeliharaan.</p>
            <a href="#" class="inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-700 dark:text-orange-400">
                Input Log &rarr;
            </a>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center text-purple-600 dark:text-purple-300">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </div>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">Keamanan</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Pengamanan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-4">Penginputan laporan harian/insiden pos pengamanan kantor.</p>
            <a href="#" class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-700 dark:text-purple-400">
                Input Laporan &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
