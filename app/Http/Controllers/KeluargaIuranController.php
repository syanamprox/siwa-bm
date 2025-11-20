<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\KeluargaIuran;
use App\Models\JenisIuran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KeluargaIuranController extends Controller
{
    /**
     * Display all iuran connections for a specific keluarga.
     */
    public function index(Keluarga $keluarga)
    {
        $connections = $keluarga->keluargaIuran()
            ->with('jenisIuran')
            ->get();

        $availableIurans = JenisIuran::where('is_aktif', true)
            ->whereNotIn('id', $connections->pluck('jenis_iuran_id'))
            ->get();

        return view('admin.keluarga_iuran.index', compact('keluarga', 'connections', 'availableIurans'));
    }

    /**
     * Store a new connection between keluarga and jenis iuran.
     */
    public function store(Request $request, Keluarga $keluarga): JsonResponse
    {
        $validated = $request->validate([
            'jenis_iuran_id' => 'required|exists:jenis_iurans,id',
            'nominal_custom' => 'nullable|numeric|min:0',
            'alasan_custom' => 'nullable|string|max:255'
        ]);

        // Check if connection already exists
        $exists = $keluarga->keluargaIuran()
            ->where('jenis_iuran_id', $validated['jenis_iuran_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi iuran sudah ada untuk keluarga ini'
            ], 422);
        }

        $keluarga->keluargaIuran()->attach($validated['jenis_iuran_id'], [
            'nominal_custom' => $validated['nominal_custom'],
            'status_aktif' => true,
            'alasan_custom' => $validated['alasan_custom'],
            'created_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Koneksi iuran berhasil ditambahkan'
        ]);
    }

    /**
     * Update an existing connection.
     */
    public function update(Request $request, Keluarga $keluarga, JenisIuran $jenisIuran): JsonResponse
    {
        $validated = $request->validate([
            'nominal_custom' => 'nullable|numeric|min:0',
            'status_aktif' => 'required|boolean',
            'alasan_custom' => 'nullable|string|max:255'
        ]);

        $connection = $keluarga->keluargaIuran()
            ->where('jenis_iuran_id', $jenisIuran->id)
            ->firstOrFail();

        $connection->update([
            'nominal_custom' => $validated['nominal_custom'],
            'status_aktif' => $validated['status_aktif'],
            'alasan_custom' => $validated['alasan_custom']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Koneksi iuran berhasil diperbarui'
        ]);
    }

    /**
     * Remove a connection.
     */
    public function destroy(Keluarga $keluarga, JenisIuran $jenisIuran): JsonResponse
    {
        // Check if there are related iuran bills
        $relatedBills = $keluarga->iuran()
            ->where('jenis_iuran_id', $jenisIuran->id)
            ->count();

        if ($relatedBills > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tidak dapat menghapus koneksi yang sudah memiliki tagihan iuran ({$relatedBills} tagihan)"
            ], 422);
        }

        $keluarga->keluargaIuran()->detach($jenisIuran->id);

        return response()->json([
            'success' => true,
            'message' => 'Koneksi iuran berhasil dihapus'
        ]);
    }

    /**
     * API: Get all jenis iuran available for a keluarga.
     */
    public function getAvailableJenisIuran($keluargaId): JsonResponse
    {
        try {
            $keluarga = Keluarga::findOrFail($keluargaId);

            $connectedIds = $keluarga->keluargaIuran()
                ->wherePivot('status_aktif', true)
                ->pluck('jenis_iuran_id');

            $availableIurans = JenisIuran::where('is_aktif', true)
                ->whereNotIn('id', $connectedIds)
                ->get(['id', 'nama', 'kode', 'jumlah', 'periode']);

            return response()->json([
                'success' => true,
                'data' => $availableIurans
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get all active connections for a keluarga.
     */
    public function getActiveConnections($keluargaId): JsonResponse
    {
        try {
            $keluarga = Keluarga::findOrFail($keluargaId);

            $connections = $keluarga->keluargaIuran()
                ->wherePivot('status_aktif', true)
                ->with('jenisIuran')
                ->get()
                ->map(function($connection) {
                    return [
                        'id' => $connection->id,
                        'jenis_iuran_id' => $connection->jenis_iuran_id,
                        'nama' => $connection->jenisIuran->nama,
                        'nominal_default' => $connection->jenisIuran->jumlah,
                        'nominal_custom' => $connection->nominal_custom,
                        'nominal_effective' => $connection->nominal_custom ?? $connection->jenisIuran->jumlah,
                        'periode' => $connection->jenisIuran->periode,
                        'alasan_custom' => $connection->alasan_custom
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $connections
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display overview of all keluarga-iuran connections.
     */
    public function overview()
    {
        $connections = KeluargaIuran::with(['keluarga', 'jenisIuran'])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_connections' => $connections->count(),
            'active_connections' => $connections->where('status_aktif', true)->count(),
            'families_with_iuran' => $connections->pluck('keluarga_id')->unique()->count(),
            'total_custom_nominals' => $connections->whereNotNull('nominal_custom')->count()
        ];

        return view('admin.keluarga_iuran.overview', compact('connections', 'summary'));
    }
}