<?php

namespace App\Http\Controllers;

use App\Models\JenisIuran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JenisIuranController extends Controller
{
    /**
     * Display a listing of the resource with AJAX support.
     */
    public function index(Request $request)
    {
        // Handle AJAX requests for data loading
        if ($request->ajax()) {
            return $this->handleAjaxRequest($request);
        }

        // Return normal view for initial page load
        return view('admin.jenis_iuran.index');
    }

    /**
     * Handle AJAX requests for DataTable
     */
    private function handleAjaxRequest(Request $request)
    {
        // Debug log
        \Log::info('JenisIuran AJAX Request', [
            'request_data' => $request->all(),
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id()
        ]);

        // Try direct DB query for debugging
        $query = JenisIuran::query();
        $dbQuery = \DB::table('jenis_iurans');

        // Log both queries
        \Log::info('Model Query', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        \Log::info('DB Query', ['sql' => $dbQuery->toSql(), 'bindings' => $dbQuery->getBindings()]);

        // Test direct DB count
        $dbCount = $dbQuery->count();
        \Log::info('Direct DB Count', ['count' => $dbCount]);

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $searchValue = $request->search;
            $query->where(function($q) use ($searchValue) {
                $q->where('nama', 'like', "%{$searchValue}%")
                  ->orWhere('kode', 'like', "%{$searchValue}%")
                  ->orWhere('keterangan', 'like', "%{$searchValue}%");
            });
        }

        // Apply periode filter
        if ($request->has('periode') && !empty($request->periode)) {
            $query->where('periode', $request->periode);
        }

        // Apply status filter - only if status is explicitly set
        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $isActive = $request->status == '1';
            $query->where('is_aktif', $isActive);
            \Log::info('Status Filter Applied', ['status' => $request->status, 'is_aktif' => $isActive]);
        }

        // Apply ordering
        $query->orderBy('nama', 'asc');

        // Handle statistics request
        if ($request->has('statistics')) {
            return $this->getStatistics($query);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        // Debug final query
        \Log::info('Final SQL before count', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        $total = $query->count();
        \Log::info('Query count result', ['count' => $total]);

        $jenisIurans = $query->skip(($page - 1) * $perPage)
                           ->take($perPage)
                           ->get();

        // Add computed attributes
        $jenisIurans = $jenisIurans->map(function ($item) {
            return $item->toArray() + [
                'periode_label' => $item->periode_label
            ];
        });

        \Log::info('JenisIuran Data Found', [
            'total_count' => $total,
            'data_count' => $jenisIurans->count(),
            'page' => $page,
            'per_page' => $perPage
        ]);

        return response()->json([
            'data' => $jenisIurans,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'per_page' => $perPage,
            'total' => $total
        ]);
    }

    /**
     * Get statistics data
     */
    private function getStatistics($query)
    {
        $stats = [
            'total' => $query->count(),
            'aktif' => $query->where('is_aktif', true)->count(),
            'non_aktif' => $query->where('is_aktif', false)->count(),
            'total_nominal' => $query->sum('jumlah')
        ];

        return response()->json($stats);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'periode_options' => [
                    'bulanan' => 'Bulanan',
                    'tahunan' => 'Tahunan',
                    'sekali' => 'Sekali'
                ],
                'sasaran_options' => [
                    'semua' => 'Semua KK',
                    'kk' => 'Per KK',
                    'warga' => 'Per Warga'
                ]
            ]);
        }

        return view('admin.jenis_iuran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:10|unique:jenis_iurans',
            'jumlah' => 'required|numeric|min:0',
            'periode' => 'required|in:bulanan,tahunan,sekali',
            'keterangan' => 'nullable|string',
        ];

        // Handle is_aktif checkbox separately
        if ($request->has('is_aktif')) {
            $validated = array_merge(
                $request->validate($rules),
                ['is_aktif' => $request->boolean('is_aktif', false)]
            );
        } else {
            $validated = array_merge(
                $request->validate($rules),
                ['is_aktif' => false]
            );
        }

        $jenisIuran = JenisIuran::create($validated);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jenis iuran berhasil ditambahkan',
                'data' => $jenisIuran
            ]);
        }

        return redirect()->route('jenis_iuran.index')
            ->with('success', 'Jenis iuran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, JenisIuran $jenisIuran)
    {
        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($jenisIuran);
        }

        return view('admin.jenis_iuran.show', compact('jenisIuran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, JenisIuran $jenisIuran)
    {
        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($jenisIuran);
        }

        return view('admin.jenis_iuran.edit', compact('jenisIuran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisIuran $jenisIuran)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:10|unique:jenis_iurans,kode,' . $jenisIuran->id,
            'jumlah' => 'required|numeric|min:0',
            'periode' => 'required|in:bulanan,tahunan,sekali',
            'keterangan' => 'nullable|string',
        ];

        // Handle is_aktif checkbox separately
        if ($request->has('is_aktif')) {
            $validated = array_merge(
                $request->validate($rules),
                ['is_aktif' => $request->boolean('is_aktif', false)]
            );
        } else {
            $validated = array_merge(
                $request->validate($rules),
                ['is_aktif' => false]
            );
        }

        $jenisIuran->update($validated);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jenis iuran berhasil diperbarui',
                'data' => $jenisIuran
            ]);
        }

        return redirect()->route('jenis_iuran.index')
            ->with('success', 'Jenis iuran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, JenisIuran $jenisIuran)
    {
        // Soft delete the jenis iuran (soft deletes are now enabled)
        $jenisIuran->delete();
        $success = 'Jenis iuran berhasil dihapus';

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $success
            ]);
        }

        return redirect()->route('jenis_iuran.index')
            ->with('success', $success);
    }

    /**
     * API: Get all active jenis iuran
     */
    public function apiIndex(): JsonResponse
    {
        $jenisIurans = JenisIuran::where('is_aktif', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode', 'jumlah', 'periode']);

        return response()->json([
            'success' => true,
            'data' => $jenisIurans
        ]);
    }

    /**
     * API: Toggle active status
     */
    public function toggleStatus(Request $request, JenisIuran $jenisIuran): JsonResponse
    {
        $jenisIuran->update(['is_aktif' => !$jenisIuran->is_aktif]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'is_aktif' => $jenisIuran->is_aktif
        ]);
    }
}