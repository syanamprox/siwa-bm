<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PengaturanSistemController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
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
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes (from Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Only Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // User Management Routes
        Route::get('/users', [UserController::class, 'indexView'])->name('users.index');
        Route::resource('users', UserController::class)->except(['index']);

        // API Routes for AJAX operations
        Route::get('/api/users', [UserController::class, 'index']);
        Route::get('/api/users/create', [UserController::class, 'create']);
        Route::get('/api/users/{user}/edit', [UserController::class, 'edit']);
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

        // System Settings
        Route::resource('pengaturan', PengaturanSistemController::class);
    });

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
