<?php

use App\Http\Controllers\AtkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemakaianAtkController;
use App\Http\Controllers\PermintaanAtkController;
use App\Http\Controllers\StokAtkController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PerjalananKendaraanController;
use App\Http\Controllers\BbmKendaraanController;
use App\Http\Controllers\PemeliharaanKendaraanController;
use App\Http\Controllers\KeamananController;
use App\Http\Controllers\EvaluasiKeamananController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman Login (Tampilan Utama)
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman yang membutuhkan Login
Route::middleware('auth')->group(function () {
    // Redirect generic dashboard
    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('staff.dashboard');
    })->name('dashboard');

    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard')->middleware('role:admin');

    // Dashboard Staff
    Route::get('/staff/dashboard', function () {
        return view('staff.dashboard');
    })->name('staff.dashboard')->middleware('role:staff');

    // QR Code Scanner & Print & Lookup (Dapat diakses Admin dan Staff)
    Route::get('/atk/scan', [AtkController::class, 'scanView'])->name('atk.scan');
    Route::get('/atk/scan/lookup/{kode}', [AtkController::class, 'scanLookup'])->name('atk.scan.lookup');
    Route::get('/atk/{id}/print-qr', [AtkController::class, 'printQr'])->name('atk.print-qr');

    // Master ATK CRUD (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/atk', [AtkController::class, 'index'])->name('atk.index');
        Route::post('/atk', [AtkController::class, 'store'])->name('atk.store');
        Route::get('/atk/{id}', [AtkController::class, 'show'])->name('atk.show');
        Route::put('/atk/{id}', [AtkController::class, 'update'])->name('atk.update');
        Route::delete('/atk/{id}', [AtkController::class, 'destroy'])->name('atk.destroy');
        Route::post('/atk/{id}/generate-qr', [AtkController::class, 'generateQr'])->name('atk.generate-qr');

        // Approval Permintaan ATK (Admin Only)
        Route::post('/permintaan-atk/{id}/approve', [PermintaanAtkController::class, 'approve'])->name('permintaan-atk.approve');
        Route::post('/permintaan-atk/{id}/reject', [PermintaanAtkController::class, 'reject'])->name('permintaan-atk.reject');

        // Modul Stok ATK (Admin Only)
        Route::get('/stok-atk', [StokAtkController::class, 'index'])->name('stok-atk.index');
        Route::post('/stok-atk', [StokAtkController::class, 'store'])->name('stok-atk.store');
        Route::get('/stok-atk/{id}', [StokAtkController::class, 'show'])->name('stok-atk.show');

        // Update Jurnal Beban Pemakaian Akhir Bulan (Admin Only)
        Route::put('/pemakaian-atk/{id}/jurnal', [PemakaianAtkController::class, 'updateJurnal'])->name('pemakaian-atk.jurnal');
    });

    // Permintaan ATK / PO (Admin & Staff)
    Route::get('/permintaan-atk', [PermintaanAtkController::class, 'index'])->name('permintaan-atk.index');
    Route::post('/permintaan-atk', [PermintaanAtkController::class, 'store'])->name('permintaan-atk.store');
    Route::get('/permintaan-atk/{id}', [PermintaanAtkController::class, 'show'])->name('permintaan-atk.show');
    Route::put('/permintaan-atk/{id}', [PermintaanAtkController::class, 'update'])->name('permintaan-atk.update');
    Route::delete('/permintaan-atk/{id}', [PermintaanAtkController::class, 'destroy'])->name('permintaan-atk.destroy');
    Route::post('/permintaan-atk/{id}/submit', [PermintaanAtkController::class, 'submit'])->name('permintaan-atk.submit');

    // Pemakaian ATK (Admin & Staff)
    Route::get('/pemakaian-atk', [PemakaianAtkController::class, 'index'])->name('pemakaian-atk.index');
    Route::post('/pemakaian-atk', [PemakaianAtkController::class, 'store'])->name('pemakaian-atk.store');
    Route::get('/pemakaian-atk/{id}', [PemakaianAtkController::class, 'show'])->name('pemakaian-atk.show');

    // Modul Kendaraan (Admin & Staff)
    Route::resource('kendaraan', KendaraanController::class)->except(['create', 'edit']);
    Route::resource('perjalanan-kendaraan', PerjalananKendaraanController::class)->except(['create', 'edit']);
    Route::resource('bbm-kendaraan', BbmKendaraanController::class)->except(['create', 'edit']);
    Route::resource('pemeliharaan-kendaraan', PemeliharaanKendaraanController::class)->except(['create', 'edit']);

    // Modul Keamanan (Admin & Staff)
    Route::resource('keamanan', KeamananController::class)->except(['create', 'edit']);
    Route::resource('evaluasi-keamanan', EvaluasiKeamananController::class)->except(['create', 'edit']);
});
