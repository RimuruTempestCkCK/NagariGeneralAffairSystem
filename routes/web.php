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
use App\Http\Controllers\AsetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman Login (Tampilan Utama)
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Halaman yang membutuhkan Login
Route::middleware('auth')->group(function () {
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/notifications/{id}/redirect', [\App\Http\Controllers\NotificationController::class, 'markAndRedirect'])->name('notifications.markAndRedirect');

    // Redirect generic dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/staff/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');

    // Reporting Routes
    Route::get('/laporan/atk', [ReportController::class, 'atk'])->name('laporan.atk');
    Route::get('/laporan/kendaraan', [ReportController::class, 'kendaraan'])->name('laporan.kendaraan');
    Route::get('/laporan/aset', [ReportController::class, 'aset'])->name('laporan.aset');

    Route::get('/laporan/atk/export', [ReportController::class, 'exportAtk'])->name('laporan.atk.export');
    Route::get('/laporan/kendaraan/export', [ReportController::class, 'exportKendaraan'])->name('laporan.kendaraan.export');
    Route::get('/laporan/aset/export', [ReportController::class, 'exportAset'])->name('laporan.aset.export');

    // QR Code Scanner & Print & Lookup (Dapat diakses Admin dan Staff)
    Route::get('/admin/atk/scan', [AtkController::class, 'scanView'])->name('admin.atk.scan');
    Route::get('/admin/atk/scan/lookup/{kode}', [AtkController::class, 'scanLookup'])->name('admin.atk.scan.lookup');
    Route::get('/admin/atk/{id}/print-qr', [AtkController::class, 'printQr'])->name('admin.atk.print-qr');
    
    Route::get('/staff/atk/scan', [AtkController::class, 'scanView'])->name('staff.atk.scan');
    Route::get('/staff/atk/scan/lookup/{kode}', [AtkController::class, 'scanLookup'])->name('staff.atk.scan.lookup');

    // Audit Trail (Admin Only)
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/admin/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{id}', [\App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');
    });

    // Master ATK CRUD (Admin Only)
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/atk', [AtkController::class, 'index'])->name('atk.index');
        Route::post('/atk', [AtkController::class, 'store'])->name('atk.store');
        Route::get('/atk/bulk-print', [AtkController::class, 'bulkPrint'])->name('atk.bulk-print');
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

    // Modul Aset (Admin & Staff - tapi mutate di-restrict di controller ke Admin)
    Route::resource('aset', AsetController::class)->except(['create', 'edit']);
});
