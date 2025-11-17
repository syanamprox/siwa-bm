<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KeluargaController extends Controller
{
    /**
     * Display keluarga management page
     */
    public function indexView()
    {
        return view('admin.keluarga.index');
    }

    /**
     * Get keluarga data for AJAX requests
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Keluarga::with(['kepalaKeluarga', 'anggotaKeluarga']);

            // Search
            if ($request->filled('search')) {
                $keyword = $request->search;
                $query->search($keyword);
            }

            // Filter by RT/RW
            if ($request->filled('rt')) {
                $query->rt($request->rt);
            }
            if ($request->filled('rw')) {
                $query->rw($request->rw);
            }

            // Sort
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $keluarga = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $keluarga->items(),
                'pagination' => [
                    'current_page' => $keluarga->currentPage(),
                    'last_page' => $keluarga->lastPage(),
                    'per_page' => $keluarga->perPage(),
                    'total' => $keluarga->total(),
                    'from' => $keluarga->firstItem(),
                    'to' => $keluarga->lastItem(),
                ],
                'message' => 'Data keluarga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new keluarga.
     */
    public function create(): JsonResponse
    {
        try {
            $data = [
                'daftar_hubungan' => Keluarga::getDaftarHubungan(),
                'warga_list' => Warga::whereNull('kk_id')->get(),
                'rt_list' => Wilayah::where('tingkat', 'RT')->pluck('kode', 'id'),
                'rw_list' => Wilayah::where('tingkat', 'RW')->pluck('kode', 'id'),
                'kelurahan_list' => Wilayah::where('tingkat', 'Kelurahan')->pluck('kode', 'id'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Form create keluarga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat form create: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created keluarga in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'no_kk' => 'required|digits:16|unique:keluarga,no_kk',
                'alamat_kk' => 'required|string|max:500',
                'rt_kk' => 'required|string|max:10',
                'rw_kk' => 'required|string|max:10',
                'kelurahan_kk' => 'required|string|max:100',
                'kepala_keluarga_id' => 'nullable|exists:warga,id|unique:keluarga,kepala_keluarga_id',
                'anggota_ids' => 'nullable|array',
                'anggota_ids.*' => 'exists:warga,id',
                'hubungan_anggota' => 'nullable|array',
                'hubungan_anggota.*' => 'string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $keluargaData = $request->all();
            $keluarga = Keluarga::create($keluargaData);

            // Set kepala keluarga jika dipilih
            if ($request->filled('kepala_keluarga_id')) {
                $kepalaKeluarga = Warga::find($request->kepala_keluarga_id);
                if ($kepalaKeluarga) {
                    $keluarga->setKepalaKeluarga($kepalaKeluarga);
                }
            }

            // Tambah anggota keluarga
            if ($request->filled('anggota_ids')) {
                foreach ($request->anggota_ids as $index => $anggotaId) {
                    $warga = Warga::find($anggotaId);
                    if ($warga) {
                        $hubungan = $request->hubungan_anggota[$index] ?? 'Anggota Keluarga';
                        $keluarga->tambahAnggota($warga, $hubungan);
                    }
                }
            }

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'keluarga',
                'id_referensi' => $keluarga->id,
                'jenis_aktivitas' => 'create',
                'deskripsi' => "Menambah data keluarga: {$keluarga->no_kk}",
                'data_baru' => json_encode($keluarga->toArray())
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga berhasil ditambahkan',
                'data' => $keluarga->load(['kepalaKeluarga', 'anggotaKeluarga'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah data keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified keluarga.
     */
    public function show(Keluarga $keluarga): JsonResponse
    {
        try {
            $keluarga->load(['kepalaKeluarga', 'anggotaKeluarga' => function($query) {
                $query->orderBy('created_at');
            }]);

            return response()->json([
                'success' => true,
                'data' => $keluarga,
                'message' => 'Data keluarga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified keluarga.
     */
    public function edit(Keluarga $keluarga): JsonResponse
    {
        try {
            $keluarga->load(['kepalaKeluarga', 'anggotaKeluarga']);

            $data = [
                'keluarga' => $keluarga,
                'daftar_hubungan' => Keluarga::getDaftarHubungan(),
                'warga_list' => Warga::whereNull('kk_id')->orWhere('kk_id', $keluarga->id)->get(),
                'rt_list' => Wilayah::where('tingkat', 'RT')->pluck('kode', 'id'),
                'rw_list' => Wilayah::where('tingkat', 'RW')->pluck('kode', 'id'),
                'kelurahan_list' => Wilayah::where('tingkat', 'Kelurahan')->pluck('kode', 'id'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Form edit keluarga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat form edit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified keluarga in storage.
     */
    public function update(Request $request, Keluarga $keluarga): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'no_kk' => 'required|digits:16|unique:keluarga,no_kk,' . $keluarga->id,
                'alamat_kk' => 'required|string|max:500',
                'rt_kk' => 'required|string|max:10',
                'rw_kk' => 'required|string|max:10',
                'kelurahan_kk' => 'required|string|max:100',
                'kepala_keluarga_id' => 'nullable|exists:warga,id|unique:keluarga,kepala_keluarga_id,' . $keluarga->id,
                'anggota_ids' => 'nullable|array',
                'anggota_ids.*' => 'exists:warga,id',
                'hubungan_anggota' => 'nullable|array',
                'hubungan_anggota.*' => 'string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Store old data for logging
            $oldData = $keluarga->toArray();

            // Update keluarga data
            $keluargaData = $request->all();
            $keluarga->update($keluargaData);

            // Update kepala keluarga
            if ($request->filled('kepala_keluarga_id')) {
                $newKepalaKeluarga = Warga::find($request->kepala_keluarga_id);
                if ($newKepalaKeluarga) {
                    // Remove kepala keluarga status from previous kepala
                    if ($keluarga->kepala_keluarga_id) {
                        $oldKepalaKeluarga = Warga::find($keluarga->kepala_keluarga_id);
                        if ($oldKepalaKeluarga) {
                            $oldKepalaKeluarga->update([
                                'hubungan_keluarga' => 'Anggota Keluarga'
                            ]);
                        }
                    }

                    $keluarga->setKepalaKeluarga($newKepalaKeluarga);
                }
            }

            // Update anggota keluarga
            if ($request->filled('anggota_ids')) {
                // Remove all current anggota
                $currentAnggota = $keluarga->anggotaKeluarga()->get();
                foreach ($currentAnggota as $anggota) {
                    if ($anggota->id != $keluarga->kepala_keluarga_id) {
                        $keluarga->hapusAnggota($anggota);
                    }
                }

                // Add new anggota
                foreach ($request->anggota_ids as $index => $anggotaId) {
                    $warga = Warga::find($anggotaId);
                    if ($warga && $warga->id != $keluarga->kepala_keluarga_id) {
                        $hubungan = $request->hubungan_anggota[$index] ?? 'Anggota Keluarga';
                        $keluarga->tambahAnggota($warga, $hubungan);
                    }
                }
            }

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'keluarga',
                'id_referensi' => $keluarga->id,
                'jenis_aktivitas' => 'update',
                'deskripsi' => "Mengupdate data keluarga: {$keluarga->no_kk}",
                'data_lama' => json_encode($oldData),
                'data_baru' => json_encode($keluarga->fresh()->toArray())
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga berhasil diperbarui',
                'data' => $keluarga->fresh()->load(['kepalaKeluarga', 'anggotaKeluarga'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified keluarga from storage.
     */
    public function destroy(Keluarga $keluarga): JsonResponse
    {
        try {
            // Check dependencies
            if ($keluarga->iuran()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keluarga tidak dapat dihapus karena memiliki data iuran terkait'
                ], 422);
            }

            DB::beginTransaction();

            // Store data for logging
            $keluargaData = $keluarga->toArray();

            // Remove all anggota from this keluarga
            $anggotaList = $keluarga->anggotaKeluarga()->get();
            foreach ($anggotaList as $anggota) {
                $keluarga->hapusAnggota($anggota);
            }

            $keluarga->delete();

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'keluarga',
                'id_referensi' => $keluarga->id,
                'jenis_aktivitas' => 'delete',
                'deskripsi' => "Menghapus data keluarga: {$keluarga->no_kk}",
                'data_lama' => json_encode($keluargaData)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalKeluarga = Keluarga::count();
            $totalAnggota = Warga::memilikiKK()->count();
            $rataRataAnggota = $totalKeluarga > 0 ? round($totalAnggota / $totalKeluarga, 2) : 0;

            // Statistik by RT/RW
            $kkByRt = Keluarga::selectRaw('rt_kk, count(*) as total')
                ->groupBy('rt_kk')
                ->orderBy('total', 'desc')
                ->get();

            $kkByRw = Keluarga::selectRaw('rw_kk, count(*) as total')
                ->groupBy('rw_kk')
                ->orderBy('total', 'desc')
                ->get();

            // KK dengan anggota terbanyak
            $kkTerbesar = Keluarga::withCount('anggotaKeluarga')
                ->orderBy('anggota_keluarga_count', 'desc')
                ->limit(5)
                ->get();

            // KK tanpa kepala keluarga
            $kkTanpaKepala = Keluarga::whereNull('kepala_keluarga_id')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_keluarga' => $totalKeluarga,
                    'total_anggota' => $totalAnggota,
                    'rata_rata_anggota' => $rataRataAnggota,
                    'kk_tanpa_kepala' => $kkTanpaKepala,
                    'kk_by_rt' => $kkByRt,
                    'kk_by_rw' => $kkByRw,
                    'kk_terbesar' => $kkTerbesar,
                ],
                'message' => 'Statistik keluarga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add member to existing keluarga
     */
    public function addMember(Request $request, Keluarga $keluarga): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'warga_id' => 'required|exists:warga,id',
                'hubungan_keluarga' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $warga = Warga::find($request->warga_id);

            // Check if warga already has KK
            if ($warga->kk_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warga sudah tergabung dalam keluarga lain'
                ], 422);
            }

            DB::beginTransaction();

            $keluarga->tambahAnggota($warga, $request->hubungan_keluarga);

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'keluarga',
                'id_referensi' => $keluarga->id,
                'jenis_aktivitas' => 'update',
                'deskripsi' => "Menambah anggota ke keluarga {$keluarga->no_kk}: {$warga->nama_lengkap}",
                'data_baru' => json_encode(['warga_id' => $warga->id, 'hubungan' => $request->hubungan_keluarga])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Anggota berhasil ditambahkan ke keluarga',
                'data' => $keluarga->fresh()->load(['anggotaKeluarga'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah anggota: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove member from keluarga
     */
    public function removeMember(Request $request, Keluarga $keluarga, Warga $warga): JsonResponse
    {
        try {
            // Check if warga is member of this keluarga
            if (!$keluarga->isAnggota($warga)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warga bukan anggota dari keluarga ini'
                ], 422);
            }

            // Check if warga is kepala keluarga
            if ($warga->id === $keluarga->kepala_keluarga_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kepala keluarga. Ganti kepala keluarga terlebih dahulu.'
                ], 422);
            }

            DB::beginTransaction();

            $keluarga->hapusAnggota($warga);

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'keluarga',
                'id_referensi' => $keluarga->id,
                'jenis_aktivitas' => 'update',
                'deskripsi' => "Menghapus anggota dari keluarga {$keluarga->no_kk}: {$warga->nama_lengkap}",
                'data_lama' => json_encode(['warga_id' => $warga->id])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Anggota berhasil dihapus dari keluarga',
                'data' => $keluarga->fresh()->load(['anggotaKeluarga'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus anggota: ' . $e->getMessage()
            ], 500);
        }
    }
}
