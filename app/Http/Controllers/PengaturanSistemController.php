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
        $settings = PengaturanSistem::all()->pluck('nilai', 'kunci');

        return view('admin.pengaturan.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pengaturan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kunci' => 'required|string|max:255|unique:pengaturan_sistem,kunci',
            'nilai' => 'required|string',
            'deskripsi' => 'nullable|string'
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
                'kunci' => $request->kunci,
                'nilai' => $request->nilai,
                'deskripsi' => $request->deskripsi
            ]);

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'pengaturan_sistem',
                'id_referensi' => $setting->id,
                'jenis_aktivitas' => 'create',
                'deskripsi' => "Menambahkan pengaturan: {$setting->kunci}",
                'data_baru' => json_encode($setting->toArray())
            ]);

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
            'kunci' => 'required|string|max:255|unique:pengaturan_sistem,kunci,'.$id,
            'nilai' => 'required|string',
            'deskripsi' => 'nullable|string'
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
                'kunci' => $request->kunci,
                'nilai' => $request->nilai,
                'deskripsi' => $request->deskripsi
            ]);

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'pengaturan_sistem',
                'id_referensi' => $setting->id,
                'jenis_aktivitas' => 'update',
                'deskripsi' => "Mengupdate pengaturan: {$setting->kunci}",
                'data_lama' => json_encode($dataLama),
                'data_baru' => json_encode($setting->toArray())
            ]);

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
            'settings.*.kunci' => 'required|string',
            'settings.*.nilai' => 'required|string'
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
                    ['kunci' => $settingData['kunci']],
                    ['nilai' => $settingData['nilai']]
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