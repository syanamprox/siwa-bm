<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\JenisIuranController;
use App\Http\Controllers\KeluargaIuranController;
use App\Http\Controllers\IuranGenerationController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PengaturanSistemController;
use App\Http\Controllers\ChangelogController;
use Illuminate\Support\Facades\Route;

// Redirect root to public portal
Route::get('/', function () {
    return redirect('/portal');
});

// Public Portal Routes
Route::prefix('portal')->group(function () {
    Route::get('/', [App\Http\Controllers\PublicPortalController::class, 'index'])->name('portal.index');
    Route::post('/cek-warga', [App\Http\Controllers\PublicPortalController::class, 'cekWarga'])->name('portal.cek-warga');
    Route::post('/cek-keluarga', [App\Http\Controllers\PublicPortalController::class, 'cekKeluarga'])->name('portal.cek-keluarga');
    Route::post('/cek-iuran', [App\Http\Controllers\PublicPortalController::class, 'cekIuran'])->name('portal.cek-iuran');
    Route::get('/captcha', [App\Http\Controllers\PublicPortalController::class, 'generateCaptcha'])->name('portal.captcha');
});


// Authentication Routes (using Laravel Breeze)
require __DIR__.'/auth.php';

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard (moved to admin group for consistency)
    // Note: Dashboard route moved to admin group below

    // Profile Routes (from Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Only Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // User Management Routes
        Route::get('/users', [UserController::class, 'indexView'])->name('users.index');
        Route::resource('users', UserController::class)->except(['index']);

        // API Routes for AJAX operations
        Route::get('/api/users', [UserController::class, 'index']);
        Route::post('/api/users', [UserController::class, 'store']);
        Route::get('/api/users/create', [UserController::class, 'create']);
        Route::get('/api/users/{user}/edit', [UserController::class, 'edit']);
        Route::put('/api/users/{user}', [UserController::class, 'update']);
        Route::delete('/api/users/{user}', [UserController::class, 'destroy']);
        Route::post('/api/users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::post('/api/users/{user}/reset-password', [UserController::class, 'resetPassword']);

        // Wilayah Management Routes
        Route::get('/wilayah', [WilayahController::class, 'indexView'])->name('wilayah.index');
        Route::resource('wilayah', WilayahController::class)->except(['index']);

        // API Routes for AJAX operations
        Route::get('/api/wilayah', [WilayahController::class, 'index']);
        Route::get('/api/wilayah/create', [WilayahController::class, 'create']);
        Route::get('/api/wilayah/{wilayah}/edit', [WilayahController::class, 'edit']);
        Route::get('/api/wilayah/tree', [WilayahController::class, 'tree']);
        Route::get('/api/wilayah/children/{parentId}', [WilayahController::class, 'getChildren']);

        // Changelog Routes
        Route::get('/changelog', [ChangelogController::class, 'indexView'])->name('changelog.index');
        Route::get('/api/changelog', [ChangelogController::class, 'index']);
        Route::get('/api/changelog/{hash}', [ChangelogController::class, 'show']);
        Route::get('/api/changelog/system-info', [ChangelogController::class, 'systemInfo']);

        // System Settings
        Route::resource('pengaturan', PengaturanSistemController::class);
        Route::get('/pengaturan/group', [PengaturanSistemController::class, 'getGroupSettings'])->name('pengaturan.group');

        // API Routes for AJAX operations
        Route::get('/api/pengaturan', [PengaturanSistemController::class, 'apiIndex']);

        // Warga Management Routes
        Route::get('/warga', [WargaController::class, 'indexView'])->name('warga.index');
        Route::resource('warga', WargaController::class)->except(['index', 'store', 'update', 'destroy']);

        // API Routes for Warga operations
        Route::get('/api/warga', [WargaController::class, 'index']);
        Route::get('/api/warga/create', [WargaController::class, 'create']);
        Route::get('/api/warga/statistics', [WargaController::class, 'statistics']);
        Route::post('/api/warga/export', [WargaController::class, 'export']);
        Route::post('/api/warga/import', [WargaController::class, 'import']);
        Route::get('/api/warga/{warga}/edit', [WargaController::class, 'edit']);
        Route::get('/api/warga/{warga}', [WargaController::class, 'show']);
        Route::put('/api/warga/{warga}', [WargaController::class, 'update']);
        Route::post('/api/warga', [WargaController::class, 'store']);
        Route::delete('/api/warga/{warga}', [WargaController::class, 'destroy']);

        // Keluarga Management Routes
        Route::get('/keluarga', [KeluargaController::class, 'indexView'])->name('keluarga.index');
        Route::resource('keluarga', KeluargaController::class)->except(['index', 'store', 'update', 'destroy']);

        // API Routes for Keluarga operations
        Route::get('/api/keluarga', [KeluargaController::class, 'index']);
        Route::get('/api/keluarga/create', [KeluargaController::class, 'create']);
        Route::get('/api/keluarga/statistics', [KeluargaController::class, 'statistics']);

        // API Routes for wilayah data (admin only) - must be before parameter routes
        Route::get('/api/keluarga/wilayah', [KeluargaController::class, 'getWilayah']);
        Route::get('/api/keluarga/rt-info', [KeluargaController::class, 'getRtInfo']);

        Route::patch('/api/keluarga/{keluarga}/status', [KeluargaController::class, 'updateStatus']);
        Route::post('/api/keluarga/{keluarga}/add-member', [KeluargaController::class, 'addMember']);
        Route::get('/api/keluarga/{keluarga}/edit', [KeluargaController::class, 'edit']);
        Route::get('/api/keluarga/{keluarga}', [KeluargaController::class, 'show']);
        Route::put('/api/keluarga/{keluarga}', [KeluargaController::class, 'update']);
        Route::post('/api/keluarga', [KeluargaController::class, 'store']);
        Route::delete('/api/keluarga/{keluarga}', [KeluargaController::class, 'destroy']);
        Route::delete('/api/keluarga/{keluarga}/remove-member/{warga}', [KeluargaController::class, 'removeMember']);

  
        // Iuran Generation Routes (must be before resource routes)
        Route::get('/iuran/generate', [IuranGenerationController::class, 'create'])->name('iuran.generate');
        Route::post('/iuran/generate', [IuranGenerationController::class, 'generate'])->name('iuran.generate.store');

        // Iuran Management Routes
        Route::get('/iuran', [IuranController::class, 'index'])->name('iuran.index');
        Route::resource('iuran', IuranController::class)->except(['index', 'create']);

        // Jenis Iuran Management Routes
        Route::get('/jenis-iuran', [JenisIuranController::class, 'index'])->name('jenis_iuran.index');
        Route::get('/jenis-iuran/create', [JenisIuranController::class, 'create'])->name('jenis_iuran.create');
        Route::post('/jenis-iuran', [JenisIuranController::class, 'store'])->name('jenis_iuran.store');
        Route::get('/jenis-iuran/{jenis_iuran}', [JenisIuranController::class, 'show'])->name('jenis_iuran.show');
        Route::get('/jenis-iuran/{jenis_iuran}/edit', [JenisIuranController::class, 'edit'])->name('jenis_iuran.edit');
        Route::put('/jenis-iuran/{jenis_iuran}', [JenisIuranController::class, 'update'])->name('jenis_iuran.update');
        Route::delete('/jenis-iuran/{jenis_iuran}', [JenisIuranController::class, 'destroy'])->name('jenis_iuran.destroy');

        // Keluarga-Iuran Connection Management Routes
        Route::get('/keluarga-iuran/overview', [KeluargaIuranController::class, 'overview'])->name('keluarga_iuran.overview');
        Route::get('/keluarga/{keluarga}/iuran', [KeluargaIuranController::class, 'index'])->name('keluarga_iuran.index');
        Route::post('/keluarga/{keluarga}/iuran', [KeluargaIuranController::class, 'store'])->name('keluarga_iuran.store');
        Route::put('/keluarga/{keluarga}/iuran/{jenisIuran}', [KeluargaIuranController::class, 'update'])->name('keluarga_iuran.update');
        Route::delete('/keluarga/{keluarga}/iuran/{jenisIuran}', [KeluargaIuranController::class, 'destroy'])->name('keluarga_iuran.destroy');

        // API Routes for Iuran Generation
        Route::get('/api/iuran/generation/rt-options', [IuranGenerationController::class, 'getRtOptions']);
        Route::get('/api/iuran/generation/preview', [IuranGenerationController::class, 'preview']);
        Route::post('/api/iuran/generation/generate', [IuranGenerationController::class, 'generate']);

        // API Routes for Iuran operations
        Route::get('/api/iuran/statistics', [IuranController::class, 'statistics']);
        Route::get('/api/iuran', [IuranController::class, 'apiIndex']);
        Route::get('/api/iuran/{iuran}', [IuranController::class, 'apiShow']);
        Route::get('/api/iuran/{iuran}/edit', [IuranController::class, 'apiEdit']);
        Route::delete('/api/iuran/{iuran}', [IuranController::class, 'apiDestroy']);
        Route::post('/api/iuran/generate-bulk', [IuranController::class, 'generateBulk']);
        Route::get('/api/iuran/keluarga/{keluargaId}/jenis-iuran', [IuranController::class, 'getKeluargaJenisIuran']);
        Route::post('/api/iuran/payment', [IuranController::class, 'processPayment']);
        Route::get('/api/iuran/{iuranId}/payment-history', [IuranController::class, 'getPaymentHistory']);
        Route::post('/api/pembayaran/{pembayaran}/verify', [IuranController::class, 'verifyPayment']);
        Route::post('/api/pembayaran/{pembayaran}/reject', [IuranController::class, 'rejectPayment']);

        // API Routes for Jenis Iuran operations
        Route::get('/api/jenis-iuran', [JenisIuranController::class, 'index'])->name('api.jenis_iuran.index');
        Route::post('/api/jenis-iuran', [JenisIuranController::class, 'store'])->name('api.jenis_iuran.store');
        Route::get('/api/jenis-iuran/create', [JenisIuranController::class, 'create'])->name('api.jenis_iuran.create');
        Route::get('/api/jenis-iuran/{jenis_iuran}', [JenisIuranController::class, 'show'])->name('api.jenis_iuran.show');
        Route::get('/api/jenis-iuran/{jenis_iuran}/edit', [JenisIuranController::class, 'edit'])->name('api.jenis_iuran.edit');
        Route::put('/api/jenis-iuran/{jenis_iuran}', [JenisIuranController::class, 'update'])->name('api.jenis_iuran.update');
        Route::delete('/api/jenis-iuran/{jenis_iuran}', [JenisIuranController::class, 'destroy'])->name('api.jenis_iuran.destroy');
        Route::put('/api/jenis-iuran/{jenis_iuran}/toggle-status', [JenisIuranController::class, 'toggleStatus'])->name('api.jenis_iuran.toggle_status');

        // API Routes for Keluarga-Iuran operations
        Route::get('/api/keluarga-iuran/overview', [KeluargaIuranController::class, 'apiOverview'])->name('api.keluarga_iuran.overview');
        Route::get('/api/keluarga-iuran/{keluargaId}/available', [KeluargaIuranController::class, 'getAvailableJenisIuran'])->name('api.keluarga_iuran.available');
        Route::get('/api/keluarga-iuran/{keluargaId}/active', [KeluargaIuranController::class, 'getActiveConnections'])->name('api.keluarga_iuran.active');

        // Backup & Restore Routes
        Route::get('/backup', [App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup/create', [App\Http\Controllers\BackupController::class, 'create'])->name('backup.create');
        Route::get('/backup/download/{filename}', [App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
        Route::delete('/backup/delete/{filename}', [App\Http\Controllers\BackupController::class, 'delete'])->name('backup.delete');
        Route::post('/backup/restore', [App\Http\Controllers\BackupController::class, 'restore'])->name('backup.restore');
        Route::get('/backup/status', [App\Http\Controllers\BackupController::class, 'status'])->name('backup.status');
    });

    // All management routes moved to admin group above for consistency

    // Note: API Routes for cascading dropdown moved to public section above

    // Lurah & Admin Routes - Coming soon
    /*
    Route::middleware('lurah')->prefix('lurah')->group(function () {
        Route::get('/laporan/wilayah', [App\Http\Controllers\LaporanController::class, 'wilayah'])->name('laporan.wilayah');
        Route::get('/laporan/kependudukan', [App\Http\Controllers\LaporanController::class, 'kependudukan'])->name('laporan.kependudukan');
        Route::get('/laporan/export', [App\Http\Controllers\LaporanController::class, 'export'])->name('laporan.export');
    });
    */

    // RT Routes - Coming soon
    /*
    Route::middleware('role:rt,rw,lurah,admin')->prefix('rt')->group(function () {
        Route::resource('warga', WargaController::class);
        Route::get('/warga/{warga}/edit-popup', [WargaController::class, 'editPopup'])->name('warga.edit-popup');
        Route::post('/warga/{warga}/update', [WargaController::class, 'updatePopup'])->name('warga.update-popup');
        Route::delete('/warga/{warga}/delete', [WargaController::class, 'destroyPopup'])->name('warga.destroy-popup');
    });

    // RW Routes - Coming soon
    Route::middleware('role:rw,lurah,admin')->prefix('rw')->group(function () {
        Route::resource('keluarga', KeluargaController::class);
        Route::get('/keluarga/{keluarga}/edit-popup', [KeluargaController::class, 'editPopup'])->name('keluarga.edit-popup');
        Route::post('/keluarga/{keluarga}/update', [KeluargaController::class, 'updatePopup'])->name('keluarga.update-popup');
        Route::delete('/keluarga/{keluarga}/delete', [KeluargaController::class, 'destroyPopup'])->name('keluarga.destroy-popup');
    });

    // All Authenticated Users Routes - Coming soon
    Route::middleware('role:rt,rw,lurah,admin')->group(function () {
        // Iuran Management
        Route::resource('iuran', IuranController::class);
        Route::get('/iuran/{iuran}/edit-popup', [IuranController::class, 'editPopup'])->name('iuran.edit-popup');
        Route::post('/iuran/{iuran}/update', [IuranController::class, 'updatePopup'])->name('iuran.update-popup');
        Route::delete('/iuran/{iuran}/delete', [IuranController::class, 'destroyPopup'])->name('iuran.destroy-popup');

        // Payment Management
        Route::post('/iuran/{iuran}/bayar', [App\Http\Controllers\PembayaranController::class, 'bayar'])->name('iuran.bayar');
        Route::get('/pembayaran/{pembayaran}/bukti', [App\Http\Controllers\PembayaranController::class, 'showBukti'])->name('pembayaran.bukti');
    });
    */
});
