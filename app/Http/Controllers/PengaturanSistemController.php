<?php

namespace App\Http\Controllers;

use App\Models\PengaturanSistem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengaturanSistemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = PengaturanSistem::orderBy('key')->get();

        return view('admin.pengaturan.index', compact('settings'));
    }

    /**
     * API method: Get all settings for AJAX
     */
    public function apiIndex()
    {
        $settings = PengaturanSistem::orderBy('key')->get();

        return response()->json([
            'success' => true,
            'data' => $settings,
            'message' => 'Data pengaturan berhasil dimuat'
        ]);
    }

    /**
     * API method: Get settings by group for forms
     */
    public function getGroupSettings()
    {
        $settings = PengaturanSistem::all()->pluck('value', 'key');

        $groupedSettings = [
            'user' => [
                'max_login_attempts' => $settings['max_login_attempts'] ?? 5,
                'password_min_length' => $settings['password_min_length'] ?? 8,
                'session_timeout' => $settings['session_timeout'] ?? 120,
            ],
            'email' => [
                'smtp_host' => $settings['smtp_host'] ?? '',
                'smtp_port' => $settings['smtp_port'] ?? 587,
                'email_from' => $settings['email_from'] ?? '',
                'email_from_name' => $settings['email_from_name'] ?? 'Sistem SIWA',
            ],
            'app' => [
                'app_name' => $settings['app_name'] ?? 'SIWA - Sistem Informasi Warga',
                'app_version' => $settings['app_version'] ?? '1.0.0',
                'timezone' => $settings['timezone'] ?? 'Asia/Jakarta',
                'date_format' => $settings['date_format'] ?? 'd/m/Y',
            ],
            'security' => [
                'require_2fa' => $settings['require_2fa'] ?? '0',
                'log_all_activities' => $settings['log_all_activities'] ?? '1',
                'ip_whitelist' => $settings['ip_whitelist'] ?? '0',
                'allowed_ips' => $settings['allowed_ips'] ?? '',
                'backup_frequency' => $settings['backup_frequency'] ?? 'monthly',
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $groupedSettings
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'tingkat_options' => [
                    'umum' => 'Umum',
                    'aplikasi' => 'Aplikasi',
                    'user' => 'User',
                    'email' => 'Email',
                    'keamanan' => 'Keamanan'
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:pengaturan_sistem,key',
            'value' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $setting = PengaturanSistem::create([
                'key' => $request->key,
                'value' => $request->value,
                'keterangan' => $request->keterangan
            ]);

            // Log activity
            if (auth()->check()) {
                \App\Models\AktivitasLog::create([
                    'user_id' => auth()->id(),
                    'tabel_referensi' => 'pengaturan_sistem',
                    'id_referensi' => $setting->id,
                    'jenis_aktivitas' => 'create',
                    'deskripsi' => "Menambahkan pengaturan: {$setting->key}",
                    'data_baru' => json_encode($setting->toArray())
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil ditambahkan',
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $setting = PengaturanSistem::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $setting = PengaturanSistem::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $setting = PengaturanSistem::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:pengaturan_sistem,key,'.$id,
            'value' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dataLama = $setting->toArray();

            $setting->update([
                'key' => $request->key,
                'value' => $request->value,
                'keterangan' => $request->keterangan
            ]);

            // Log activity
            if (auth()->check()) {
                \App\Models\AktivitasLog::create([
                    'user_id' => auth()->id(),
                    'tabel_referensi' => 'pengaturan_sistem',
                    'id_referensi' => $setting->id,
                    'jenis_aktivitas' => 'update',
                    'deskripsi' => "Mengupdate pengaturan: {$setting->key}",
                    'data_lama' => json_encode($dataLama),
                    'data_baru' => json_encode($setting->toArray())
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil diupdate',
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $setting = PengaturanSistem::findOrFail($id);

        try {
            $dataSetting = $setting->toArray();
            $setting->delete();

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'pengaturan_sistem',
                'id_referensi' => $setting->id,
                'jenis_aktivitas' => 'delete',
                'deskripsi' => "Menghapus pengaturan: {$setting->kunci}",
                'data_lama' => json_encode($dataSetting)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update multiple settings at once
     */
    public function updateMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->settings as $settingData) {
                PengaturanSistem::updateOrCreate(
                    ['key' => $settingData['key']],
                    ['value' => $settingData['value']]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}