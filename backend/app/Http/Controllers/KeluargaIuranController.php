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
     * Display overview of all keluarga-iuran connections across all families
     */
    public function overview(Request $request)
    {
        // Get all jenis iurans for filter dropdown
        $allJenisIurans = JenisIuran::where('is_aktif', true)->orderBy('nama')->get();

        // Build query with filters
        $query = KeluargaIuran::with(['keluarga.kepalaKeluarga', 'jenisIuran'])
            ->whereHas('jenisIuran') // Only include records with existing jenis iuran
            ->whereHas('keluarga');   // Only include records with existing keluarga

        // Apply combined search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('keluarga', function($subQ) use ($searchTerm) {
                    $subQ->where('no_kk', 'like', '%' . $searchTerm . '%')
                          ->orWhereHas('kepalaKeluarga', function($subSubQ) use ($searchTerm) {
                              $subSubQ->where('nama_lengkap', 'like', '%' . $searchTerm . '%');
                          });
                });
            });
        }

        if ($request->filled('filter_jenis_iuran')) {
            $query->where('jenis_iuran_id', $request->filter_jenis_iuran);
        }

        if ($request->filled('filter_status')) {
            $query->where('status_aktif', $request->filter_status === '1');
        }

        $connections = $query->orderBy('created_at', 'desc')->paginate(25);

        $summary = [
            'total_connections' => KeluargaIuran::count(),
            'active_connections' => KeluargaIuran::where('status_aktif', true)->count(),
            'families_with_iuran' => KeluargaIuran::pluck('keluarga_id')->unique()->count(),
            'total_custom_nominals' => KeluargaIuran::whereNotNull('nominal_custom')->count()
        ];

        return view('admin.keluarga_iuran.overview', compact('connections', 'summary', 'allJenisIurans'));
    }

    /**
     * Index: Show all iuran connections for a specific family
     * URL: /admin/keluarga/{keluarga}/iuran
     */
    public function index(Keluarga $keluarga)
    {
        $connections = $keluarga->keluargaIuran()
            ->with(['jenisIuran' => function($query) {
                $query->withTrashed();
            }])
            ->whereHas('jenisIuran', function($query) {
                $query->withTrashed();
            }) // Include connections with soft-deleted jenis iuran
            ->orderBy('created_at', 'desc')
            ->get();

        $availableIurans = JenisIuran::where('is_aktif', true)
            ->whereNotIn('id', $connections->pluck('jenis_iuran_id'))
            ->get();

        return view('admin.keluarga_iuran.index', compact('keluarga', 'connections', 'availableIurans'));
    }

    /**
     * Store: Connect family to iuran type (AJAX support)
     * URL: POST /admin/keluarga/{keluarga}/iuran
     */
    public function store(Request $request, Keluarga $keluarga): JsonResponse
    {
        $rules = [
            'jenis_iuran_id' => 'required|exists:jenis_iurans,id',
            'nominal_custom' => 'nullable|numeric|min:0',
            'alasan_custom' => 'nullable|string|max:255'
        ];

        $validated = $request->validate($rules);

        // Handle boolean checkbox separately
        $validated['status_aktif'] = $request->boolean('status_aktif', true);

        // Check if connection already exists (only active records)
        $exists = $keluarga->keluargaIuran()
            ->where('jenis_iuran_id', $validated['jenis_iuran_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi iuran ini sudah ada untuk keluarga tersebut'
            ], 422);
        }

        $connection = $keluarga->keluargaIuran()->create([
            'jenis_iuran_id' => $validated['jenis_iuran_id'],
            'nominal_custom' => $validated['nominal_custom'],
            'status_aktif' => $validated['status_aktif'] ?? true,
            'alasan_custom' => $validated['alasan_custom'],
            'created_by' => auth()->id()
        ]);

        $connection->load(['jenisIuran' => function($query) {
            $query->withTrashed();
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Koneksi iuran berhasil ditambahkan',
            'data' => [
                'id' => $connection->id,
                'keluarga_id' => $connection->keluarga_id,
                'jenis_iuran_id' => $connection->jenis_iuran_id,
                'nominal_custom' => $connection->nominal_custom,
                'status_aktif' => $connection->status_aktif,
                'alasan_custom' => $connection->alasan_custom,
                'jenisIuran' => $connection->jenisIuran ? [
                    'id' => $connection->jenisIuran->id,
                    'nama' => $connection->jenisIuran->nama,
                    'kode' => $connection->jenisIuran->kode,
                    'jumlah' => $connection->jenisIuran->jumlah,
                    'periode_label' => $connection->jenisIuran->periode_label
                ] : null
            ]
        ]);
    }

    /**
     * Update: Modify connection (AJAX support)
     * URL: PUT /admin/keluarga/{keluarga}/iuran/{jenisIuran}
     */
    public function update(Request $request, Keluarga $keluarga, JenisIuran $jenisIuran): JsonResponse
    {
        $connection = $keluarga->keluargaIuran()
            ->where('jenis_iuran_id', $jenisIuran->id)
            ->firstOrFail();

        $rules = [
            'nominal_custom' => 'nullable|numeric|min:0',
            'alasan_custom' => 'nullable|string|max:255'
        ];

        $validated = $request->validate($rules);

        // Handle boolean checkbox separately
        $validated['status_aktif'] = $request->boolean('status_aktif', false);

        $connection->update($validated);

        // Load relationship with debugging
        $connection->load(['jenisIuran' => function($query) {
            $query->withTrashed();
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Koneksi iuran berhasil diperbarui',
            'data' => [
                'id' => $connection->id,
                'keluarga_id' => $connection->keluarga_id,
                'jenis_iuran_id' => $connection->jenis_iuran_id,
                'nominal_custom' => $connection->nominal_custom,
                'status_aktif' => $connection->status_aktif,
                'alasan_custom' => $connection->alasan_custom,
                'jenisIuran' => $connection->jenisIuran ? [
                    'id' => $connection->jenisIuran->id,
                    'nama' => $connection->jenisIuran->nama,
                    'kode' => $connection->jenisIuran->kode,
                    'jumlah' => $connection->jenisIuran->jumlah,
                    'periode_label' => $connection->jenisIuran->periode_label
                ] : null
            ]
        ]);
    }

    /**
     * Destroy: Remove connection (AJAX support)
     * URL: DELETE /admin/keluarga/{keluarga}/iuran/{jenisIuran}
     */
    public function destroy(Keluarga $keluarga, JenisIuran $jenisIuran): JsonResponse
    {
        $connection = $keluarga->keluargaIuran()
            ->where('jenis_iuran_id', $jenisIuran->id)
            ->firstOrFail();

        $connection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Koneksi iuran berhasil dihapus'
        ]);
    }

    /**
     * API: Get available jenis iuran for a family
     */
    public function getAvailableJenisIuran(Keluarga $keluarga): JsonResponse
    {
        $existingConnections = $keluarga->keluargaIuran()->pluck('jenis_iuran_id');

        $availableIurans = JenisIuran::where('is_aktif', true)
            ->whereNotIn('id', $existingConnections)
            ->get(['id', 'nama', 'kode', 'jumlah', 'periode']);

        return response()->json([
            'success' => true,
            'data' => $availableIurans
        ]);
    }

    /**
     * API: Get active connections for a family
     */
    public function getActiveConnections(Keluarga $keluarga): JsonResponse
    {
        $connections = $keluarga->keluargaIuran()
            ->where('status_aktif', true)
            ->with('jenisIuran')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $connections
        ]);
    }

    /**
     * API: Get filtered connections for overview with pagination
     */
    public function apiOverview(Request $request): JsonResponse
    {
        // Get all jenis iurans for filter dropdown
        $allJenisIurans = JenisIuran::where('is_aktif', true)->orderBy('nama')->get();

        // Build query with filters
        $query = KeluargaIuran::with(['keluarga.kepalaKeluarga', 'jenisIuran'])
            ->whereHas('jenisIuran') // Only include records with existing jenis iuran
            ->whereHas('keluarga');   // Only include records with existing keluarga

        // Apply combined search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('keluarga', function($subQ) use ($searchTerm) {
                    $subQ->where('no_kk', 'like', '%' . $searchTerm . '%')
                          ->orWhereHas('kepalaKeluarga', function($subSubQ) use ($searchTerm) {
                              $subSubQ->where('nama_lengkap', 'like', '%' . $searchTerm . '%');
                          });
                });
            });
        }

        if ($request->filled('filter_jenis_iuran')) {
            $query->where('jenis_iuran_id', $request->filter_jenis_iuran);
        }

        if ($request->filled('filter_status')) {
            $query->where('status_aktif', $request->filter_status === '1');
        }

        // Pagination
        $perPage = $request->per_page ?? 25;
        $page = $request->page ?? 1;

        $connections = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Load kepala keluarga data for each connection
        $connectionsWithKepala = $connections->getCollection()->map(function ($connection) {
            // Convert to array and add kepala keluarga data
            $connectionArray = $connection->toArray();

            // Load kepala keluarga if exists
            if ($connection->keluarga && $connection->keluarga->kepala_keluarga_id) {
                $kepalaKeluarga = \App\Models\Warga::find($connection->keluarga->kepala_keluarga_id);
                if ($kepalaKeluarga) {
                    $connectionArray['keluarga']['kepala_keluarga'] = [
                        'id' => $kepalaKeluarga->id,
                        'nama_lengkap' => $kepalaKeluarga->nama_lengkap
                    ];
                }
            }

            return $connectionArray;
        });

        // Summary data (filtered)
        $summary = [
            'total_connections' => $query->count(),
            'active_connections' => $query->where('status_aktif', true)->count(),
            'families_with_iuran' => $query->pluck('keluarga_id')->unique()->count(),
            'total_custom_nominals' => $query->whereNotNull('nominal_custom')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $connectionsWithKepala->values()->all(),
            'pagination' => [
                'current_page' => $connections->currentPage(),
                'last_page' => $connections->lastPage(),
                'per_page' => $connections->perPage(),
                'total' => $connections->total(),
                'from' => $connections->firstItem(),
                'to' => $connections->lastItem()
            ],
            'summary' => $summary
        ]);
    }
}