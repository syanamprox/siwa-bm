<?php

use App\Http\Controllers\Api\AktivitasController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BackupController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IuranController;
use App\Http\Controllers\Api\IuranGenerationController;
use App\Http\Controllers\Api\JenisIuranController;
use App\Http\Controllers\Api\KasController;
use App\Http\Controllers\Api\KeluargaController;
use App\Http\Controllers\Api\KeluargaIuranController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\PengaturanController;
use App\Http\Controllers\Api\PortalController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WargaController;
use App\Http\Controllers\Api\WilayahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SIWA (Next.js SPA backend)
|--------------------------------------------------------------------------
*/

// Auth (session-based via Sanctum stateful)
Route::post('/login', [AuthController::class, 'login'])->middleware('web');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('web');

// Portal publik (rate-limited di controller)
Route::prefix('portal')->group(function () {
    Route::post('/cek-warga', [PortalController::class, 'cekWarga']);
    Route::post('/cek-keluarga', [PortalController::class, 'cekKeluarga']);
    Route::post('/cek-iuran', [PortalController::class, 'cekIuran']);

    // Kas publik (transparansi keuangan, read-only ringkas)
    Route::get('/kas/units', [KasController::class, 'unitsPublic']);
    Route::get('/kas/summary', [KasController::class, 'summaryPublic']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Kependudukan
    Route::get('/warga/statistics', [WargaController::class, 'statistics']);
    Route::post('/warga/{warga}/verify', [WargaController::class, 'verify'])->middleware('admin');
    Route::apiResource('warga', WargaController::class)->parameters(['warga' => 'warga']);
    Route::get('/keluarga/statistics', [KeluargaController::class, 'statistics']);
    Route::get('/keluarga/kk-token', [KeluargaController::class, 'kkToken']);
    Route::post('/keluarga/{keluarga}/verify', [KeluargaController::class, 'verify'])->middleware('admin');
    Route::apiResource('keluarga', KeluargaController::class)->parameters(['keluarga' => 'keluarga']);
    Route::patch('/keluarga/{keluarga}/status', [KeluargaController::class, 'updateStatus']);
    Route::post('/keluarga/{keluarga}/members', [KeluargaController::class, 'addMember']);
    Route::delete('/keluarga/{keluarga}/members/{warga}', [KeluargaController::class, 'removeMember']);

    // Konfigurasi iuran keluarga
    Route::get('/keluarga-iuran', [KeluargaIuranController::class, 'index']);
    Route::get('/keluarga/{keluarga}/iuran-available', [KeluargaIuranController::class, 'available']);
    Route::post('/keluarga/{keluarga}/iuran', [KeluargaIuranController::class, 'store']);
    Route::put('/keluarga-iuran/{conn}', [KeluargaIuranController::class, 'update']);
    Route::delete('/keluarga-iuran/{conn}', [KeluargaIuranController::class, 'destroy']);

    // Iuran
    Route::get('/iuran/statistics', [IuranController::class, 'statistics']);
    Route::get('/iuran', [IuranController::class, 'index']);
    Route::post('/iuran/bayar-batch', [IuranController::class, 'bayarBatch']);
    Route::post('/iuran/{iuran}/bayar', [IuranController::class, 'bayar']);
    Route::get('/iuran/{iuran}/payments', [IuranController::class, 'payments']);

    // Generate tagihan
    Route::get('/iuran/generation/rt-options', [IuranGenerationController::class, 'rtOptions']);
    Route::get('/iuran/generation/preview', [IuranGenerationController::class, 'preview']);
    Route::post('/iuran/generation/generate', [IuranGenerationController::class, 'generate']);

    // Jenis iuran
    Route::put('/jenis-iuran/{jenisIuran}/toggle-status', [JenisIuranController::class, 'toggleStatus']);
    Route::apiResource('jenis-iuran', JenisIuranController::class)->parameters(['jenis-iuran' => 'jenisIuran']);

    // Laporan
    Route::get('/laporan/kependudukan', [LaporanController::class, 'kependudukan']);
    Route::get('/laporan/wilayah', [LaporanController::class, 'wilayah']);

    // Kas (semua role — scoping internal per wilayah)
    Route::get('/kas/units', [KasController::class, 'units']);
    Route::post('/kas/units', [KasController::class, 'storeUnit']);
    Route::delete('/kas/units/{unit}', [KasController::class, 'destroyUnit']);
    Route::get('/kas/summary', [KasController::class, 'summary']);
    Route::post('/kas/transaksis', [KasController::class, 'storeTrx']);
    Route::delete('/kas/transaksis/{trx}', [KasController::class, 'destroyTrx']);

    // Wilayah (admin-managed, read untuk semua role)
    Route::get('/wilayah/tree', [WilayahController::class, 'tree']);
    Route::get('/wilayah/children/{parentId}', [WilayahController::class, 'children']);
    Route::get('/wilayah', [WilayahController::class, 'index']);

    // Aktivitas log (admin + lurah)
    Route::get('/aktivitas', [AktivitasController::class, 'index'])->middleware('role:admin,lurah');

    // ── Admin only ──
    Route::middleware('admin')->group(function () {
        Route::post('/wilayah', [WilayahController::class, 'store']);
        Route::put('/wilayah/{wilayah}', [WilayahController::class, 'update']);
        Route::delete('/wilayah/{wilayah}', [WilayahController::class, 'destroy']);

        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword']);
        Route::apiResource('users', UserController::class);

        Route::put('/pengaturan', [PengaturanController::class, 'update']);
        Route::get('/pengaturan', [PengaturanController::class, 'index']);

        Route::get('/backup', [BackupController::class, 'index']);
        Route::post('/backup', [BackupController::class, 'create']);
        Route::post('/backup/restore', [BackupController::class, 'restore']);
        Route::get('/backup/{filename}/download', [BackupController::class, 'download']);
        Route::delete('/backup/{filename}', [BackupController::class, 'destroy']);
    });
});
