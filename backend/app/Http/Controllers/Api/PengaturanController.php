<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Models\PengaturanSistem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    use LogsActivity;

    /**
     * GET /api/pengaturan — grouped settings (dengan default).
     */
    public function index(): JsonResponse
    {
        $groups = [
            'app' => [
                ['key' => 'app_name', 'value' => PengaturanSistem::getValue('app_name', 'SIWA - Sistem Informasi Warga'), 'keterangan' => 'Nama aplikasi'],
                ['key' => 'app_version', 'value' => PengaturanSistem::getValue('app_version', '1.0.0'), 'keterangan' => 'Versi aplikasi'],
                ['key' => 'timezone', 'value' => PengaturanSistem::getValue('timezone', 'Asia/Jakarta'), 'keterangan' => 'Timezone'],
                ['key' => 'date_format', 'value' => PengaturanSistem::getValue('date_format', 'd/m/Y'), 'keterangan' => 'Format tanggal'],
            ],
            'kelurahan' => [
                ['key' => 'kelurahan_nama', 'value' => PengaturanSistem::getValue('kelurahan_nama', 'Bendul Merisi'), 'keterangan' => 'Nama kelurahan'],
                ['key' => 'kecamatan_nama', 'value' => PengaturanSistem::getValue('kecamatan_nama', 'Wonocolo'), 'keterangan' => 'Nama kecamatan'],
                ['key' => 'kota_nama', 'value' => PengaturanSistem::getValue('kota_nama', 'Kota Surabaya'), 'keterangan' => 'Nama kota'],
                ['key' => 'kontak_telepon', 'value' => PengaturanSistem::getValue('kontak_telepon', ''), 'keterangan' => 'Telepon kantor kelurahan'],
            ],
            'keamanan' => [
                ['key' => 'max_login_attempts', 'value' => PengaturanSistem::getValue('max_login_attempts', '5'), 'keterangan' => 'Maksimal percobaan login'],
                ['key' => 'session_timeout', 'value' => PengaturanSistem::getValue('session_timeout', '120'), 'keterangan' => 'Timeout sesi (menit)'],
                ['key' => 'log_all_activities', 'value' => PengaturanSistem::getValue('log_all_activities', '1'), 'keterangan' => 'Log semua aktivitas'],
            ],
        ];

        return response()->json(['data' => $groups]);
    }

    /**
     * PUT /api/pengaturan — update banyak sekaligus [{key, value}].
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array', 'min:1'],
            'settings.*.key' => ['required', 'string', 'max:255'],
            'settings.*.value' => ['required', 'string'],
        ]);

        foreach ($validated['settings'] as $setting) {
            PengaturanSistem::setValue($setting['key'], $setting['value']);
        }
        $this->logActivity($request, 'update', 'pengaturan', 'Update pengaturan sistem', null, ['keys' => array_column($validated['settings'], 'key')]);

        return response()->json(['message' => 'Pengaturan tersimpan.']);
    }
}
