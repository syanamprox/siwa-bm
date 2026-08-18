<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\JenisIuran;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class IuranGenerationController extends Controller
{
    /**
     * Show form for generating iuran
     */
    public function create()
    {
        $periodeOptions = $this->getPeriodeOptions();
        $jenisIurans = JenisIuran::where('is_aktif', true)->get();

        return view('admin.iuran.generate', compact('periodeOptions', 'jenisIurans'));
    }

    /**
     * Generate iuran for selected periode and filters
     */
    public function generate(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'periode_bulan' => 'required|string|regex:/^\d{4}-\d{2}$/', // YYYY-MM format
            'jenis_iuran_ids' => 'nullable|array',
            'jenis_iuran_ids.*' => 'exists:jenis_iurans,id',
            'rt_id' => 'nullable|exists:wilayahs,id',
            'use_existing_connections' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $periode = $request->periode_bulan;
            $jenisIuranIds = $request->jenis_iuran_ids ?? [];
            $rtId = $request->rt_id;
            $useExistingConnections = $request->boolean('use_existing_connections', true);

            // Get families based on user role and RT filter
            $families = $this->getFamiliesByRoleAndRt($rtId);

            $generatedCount = 0;
            $duplicateCount = 0;
            $skippedCount = 0;

            foreach ($families as $family) {
                // Skip if family is not active
                if ($family->status_keluarga !== 'Aktif') {
                    $skippedCount++;
                    continue;
                }

                if ($useExistingConnections) {
                    // Get all active iuran connections for this family
                    $connections = $family->keluargaIuran()->where('status_aktif', true)->get();

                    foreach ($connections as $connection) {
                        $jenisIuranId = $connection->jenis_iuran_id;
                        $jenisIuran = JenisIuran::find($jenisIuranId);
                        if (!$jenisIuran || !$jenisIuran->is_aktif) continue;

                        // Check if iuran already exists for this period
                        $exists = Iuran::where('kk_id', $family->id)
                            ->where('jenis_iuran_id', $jenisIuranId)
                            ->where('periode_bulan', $periode)
                            ->exists();

                        if ($exists) {
                            $duplicateCount++;
                            continue;
                        }

                        // Create new iuran
                        Iuran::create([
                            'kk_id' => $family->id,
                            'jenis_iuran_id' => $jenisIuranId,
                            'nominal' => $connection->nominal_custom ?? $jenisIuran->jumlah,
                            'periode_bulan' => $periode,
                            'status' => 'belum_bayar',
                            'jatuh_tempo' => $this->calculateJatuhTempo($periode, $jenisIuran->periode_label ?? $jenisIuran->periode),
                            'denda_terlambatan' => 0,
                            'keterangan' => "Generate otomatis periode " . Carbon::parse($periode . '-01')->format('F Y'),
                            'created_by' => auth()->id()
                        ]);

                        $generatedCount++;
                    }
                } else {
                    // Use specific selected jenis iuran
                    foreach ($jenisIuranIds as $jenisIuranId) {
                        // Check if jenis iuran is active
                        $jenisIuran = JenisIuran::find($jenisIuranId);
                        if (!$jenisIuran || !$jenisIuran->is_aktif) {
                            $skippedCount++;
                            continue;
                        }

                        // Check if family is connected to this jenis iuran
                        $connection = $family->keluargaIuran()
                            ->where('jenis_iuran_id', $jenisIuranId)
                            ->where('status_aktif', true)
                            ->first();

                        if (!$connection) {
                            $skippedCount++;
                            continue;
                        }

                        // Check if iuran already exists for this period
                        $exists = Iuran::where('kk_id', $family->id)
                            ->where('jenis_iuran_id', $jenisIuranId)
                            ->where('periode_bulan', $periode)
                            ->exists();

                        if ($exists) {
                            $duplicateCount++;
                            continue;
                        }

                        // Create new iuran
                        Iuran::create([
                            'kk_id' => $family->id,
                            'jenis_iuran_id' => $jenisIuranId,
                            'nominal' => $connection->nominal_custom ?? $jenisIuran->jumlah,
                            'periode_bulan' => $periode,
                            'status' => 'belum_bayar',
                            'jatuh_tempo' => $this->calculateJatuhTempo($periode, $jenisIuran->periode_label ?? $jenisIuran->periode),
                            'denda_terlambatan' => 0,
                            'keterangan' => "Generate otomatis periode " . Carbon::parse($periode . '-01')->format('F Y'),
                            'created_by' => auth()->id()
                        ]);

                        $generatedCount++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil generate {$generatedCount} tagihan iuran untuk periode {$periode}" .
                    ($duplicateCount > 0 ? " ({$duplicateCount} dilewati karena sudah ada)" : "") .
                    ($skippedCount > 0 ? " ({$skippedCount} dilewati karena tidak aktif/tidak terhubung)" : ""),
                'data' => [
                    'generated' => $generatedCount,
                    'duplicates' => $duplicateCount,
                    'skipped' => $skippedCount,
                    'periode' => $periode
                ]
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Handle unique constraint violation
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa tagihan sudah ada. Silakan coba dengan periode yang berbeda.'
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate iuran: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate iuran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get families based on user role and RT filter
     */
    private function getFamiliesByRoleAndRt($rtId = null)
    {
        $query = Keluarga::where('status_keluarga', 'Aktif');

        // Filter by RT if specified
        if ($rtId) {
            $query->where('rt_id', $rtId);
        }

        // TODO: Implement role-based filtering after fixing authentication
        // For now, return all active families

        return $query->with(['wilayah', 'kepalaKeluarga'])->get();
    }

    /**
     * Calculate jatuh tempo based on periode and jenis periode
     */
    private function calculateJatuhTempo($periode, $jenisPeriode)
    {
        $year = substr($periode, 0, 4);
        $month = substr($periode, 5, 2);

        switch ($jenisPeriode) {
            case 'tahunan':
                return Carbon::create($year, 6, 30); // 30 Juni
            case 'sekali':
                return Carbon::create($year, $month, 15); // 15 hari di bulan yang sama
            default: // bulanan
                return Carbon::create($year, $month, 25); // 25 setiap bulan
        }
    }

    /**
     * Get periode options for dropdown
     */
    public function getPeriodeOptions()
    {
        $options = [];
        $currentDate = Carbon::now();

        // Generate options for last 3 months and next 3 months
        for ($i = -3; $i <= 3; $i++) {
            $date = $currentDate->copy()->addMonths($i);
            $options[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }

        return $options;
    }

    /**
     * API: Get RT options based on user role
     */
    public function getRtOptions(): JsonResponse
    {
        try {
            // For now, return all RTs regardless of role
            // TODO: Implement role-based filtering after fixing the authentication issue
            $rts = Wilayah::where('tingkat', 'RT')->get();

            return response()->json([
                'success' => true,
                'data' => $rts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data RT: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Preview iuran generation
     */
    public function preview(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'periode_bulan' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'jenis_iuran_ids' => 'nullable|array',
            'jenis_iuran_ids.*' => 'exists:jenis_iurans,id',
            'rt_id' => 'nullable|exists:wilayahs,id',
            'use_existing_connections' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $periode = $request->periode_bulan;
            $jenisIuranIds = $request->jenis_iuran_ids ?? [];
            $rtId = $request->rt_id;
            $useExistingConnections = $request->boolean('use_existing_connections', true);

            $families = $this->getFamiliesByRoleAndRt($rtId);

            $preview = [];
            $totalFamilies = 0;
            $totalIuran = 0;

            foreach ($families as $family) {
                if ($family->status_keluarga !== 'Aktif') {
                    continue;
                }

                $familyIurans = [];
                $familyTotal = 0;

                if ($useExistingConnections) {
                    // Get all active iuran connections for this family
                    $connections = $family->keluargaIuran()->where('status_aktif', true)->get();

                    foreach ($connections as $connection) {
                        $jenisIuran = JenisIuran::find($connection->jenis_iuran_id);
                        if (!$jenisIuran || !$jenisIuran->is_aktif) continue;

                        $nominal = $connection->nominal_custom ?? $jenisIuran->jumlah;

                        $familyIurans[] = [
                            'jenis_iuran' => $jenisIuran->nama,
                            'nominal' => $nominal
                        ];

                        $familyTotal += $nominal;
                    }
                } else {
                    // Use specific selected jenis iuran
                    foreach ($jenisIuranIds as $jenisIuranId) {
                        // Check if jenis iuran is active
                        $jenisIuran = JenisIuran::find($jenisIuranId);
                        if (!$jenisIuran || !$jenisIuran->is_aktif) {
                            continue;
                        }

                        $connection = $family->keluargaIuran()
                            ->where('jenis_iuran_id', $jenisIuranId)
                            ->where('status_aktif', true)
                            ->first();

                        if (!$connection) {
                            continue;
                        }

                        $nominal = $connection->nominal_custom ?? $jenisIuran->jumlah;

                        $familyIurans[] = [
                            'jenis_iuran' => $jenisIuran->nama,
                            'nominal' => $nominal
                        ];

                        $familyTotal += $nominal;
                    }
                }

                if (!empty($familyIurans)) {
                    $preview[] = [
                        'kk_id' => $family->id,
                        'no_kk' => $family->no_kk,
                        'kepala_keluarga' => $family->kepalaKeluarga?->nama_lengkap ?? '-',
                        'rt' => $family->wilayah->nama ?? '-',
                        'iurans' => $familyIurans,
                        'total' => $familyTotal
                    ];

                    $totalFamilies++;
                    $totalIuran += count($familyIurans);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'preview' => $preview,
                    'summary' => [
                        'total_families' => $totalFamilies,
                        'total_iuran' => $totalIuran,
                        'periode' => $periode
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal preview generate: ' . $e->getMessage()
            ], 500);
        }
    }
}