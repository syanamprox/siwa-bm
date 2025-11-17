<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Keluarga;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class WargaController extends Controller
{
    /**
     * Display warga management page
     */
    public function indexView()
    {
        return view('admin.warga.index');
    }

    /**
     * Get warga data for AJAX requests
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Warga::with(['keluarga', 'createdBy', 'updatedBy']);

            // Search
            if ($request->filled('search')) {
                $keyword = $request->search;
                $query->search($keyword);
            }

            // Filter by RT/RW
            if ($request->filled('rt')) {
                $query->rtDomisili($request->rt);
            }
            if ($request->filled('rw')) {
                $query->rwDomisili($request->rw);
            }

            // Filter by jenis kelamin
            if ($request->filled('jenis_kelamin')) {
                $query->jenisKelamin($request->jenis_kelamin);
            }

            // Filter by agama
            if ($request->filled('agama')) {
                $query->agama($request->agama);
            }

            // Filter by pendidikan
            if ($request->filled('pendidikan')) {
                $query->pendidikan($request->pendidikan);
            }

            // Filter by status KK
            if ($request->filled('status_kk')) {
                if ($request->status_kk === 'punya_kk') {
                    $query->memilikiKK();
                } elseif ($request->status_kk === 'tanpa_kk') {
                    $query->whereNull('kk_id');
                }
            }

            // Sort
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $warga = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $warga->items(),
                'pagination' => [
                    'current_page' => $warga->currentPage(),
                    'last_page' => $warga->lastPage(),
                    'per_page' => $warga->perPage(),
                    'total' => $warga->total(),
                    'from' => $warga->firstItem(),
                    'to' => $warga->lastItem(),
                ],
                'message' => 'Data warga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data warga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new warga.
     */
    public function create(): JsonResponse
    {
        try {
            $data = [
                'daftar_agama' => Warga::getDaftarAgama(),
                'daftar_status_perkawinan' => Warga::getDaftarStatusPerkawinan(),
                'daftar_pekerjaan' => Warga::getDaftarPekerjaan(),
                'daftar_pendidikan' => Warga::getDaftarPendidikan(),
                'daftar_hubungan' => Keluarga::getDaftarHubungan(),
                'keluarga_list' => Keluarga::withKepalaKeluarga()->get(),
                'rt_list' => Wilayah::where('tingkat', 'RT')->pluck('kode', 'id'),
                'rw_list' => Wilayah::where('tingkat', 'RW')->pluck('kode', 'id'),
                'kelurahan_list' => Wilayah::where('tingkat', 'Kelurahan')->pluck('kode', 'id'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Form create warga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat form create: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created warga in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                // Data KTP (required fields)
                'nik' => 'required|digits:16|unique:warga,nik',
                'nama_lengkap' => 'required|string|max:255',
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date|before:today',
                'jenis_kelamin' => 'required|in:L,P',
                'golongan_darah' => 'nullable|in:A,B,AB,O',
                'alamat_ktp' => 'required|string|max:500',
                'rt_ktp' => 'required|string|max:10',
                'rw_ktp' => 'required|string|max:10',
                'kelurahan_ktp' => 'required|string|max:100',
                'kecamatan_ktp' => 'required|string|max:100',
                'kabupaten_ktp' => 'required|string|max:100',
                'provinsi_ktp' => 'required|string|max:100',
                'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
                'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
                'pekerjaan' => 'required|string|max:100',
                'kewarganegaraan' => 'required|in:WNI,WNA',
                'pendidikan_terakhir' => 'required|in:Tidak/Sekolah,SD,SMP,SMA,D1/D2/D3,S1,S2,S3',
                'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

                // Data Domisili
                'kk_id' => 'nullable|exists:keluarga,id',
                'hubungan_keluarga' => 'nullable|required_if:kk_id,!=,null|string|max:50',
                'alamat_domisili' => 'nullable|string|max:500',
                'rt_domisili' => 'nullable|string|max:10',
                'rw_domisili' => 'nullable|string|max:10',
                'kelurahan_domisili' => 'nullable|string|max:100',
                'no_telepon' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'status_domisili' => 'nullable|in:Tetap,Non Domisili,Luar,Sementara',
                'tanggal_mulai_domisili' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Handle file upload
            $fotoKtpPath = null;
            if ($request->hasFile('foto_ktp')) {
                $file = $request->file('foto_ktp');
                $filename = 'ktp_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $file->getClientOriginalExtension();
                $fotoKtpPath = $file->storeAs('uploads/ktp', $filename, 'public');
            }

            $wargaData = $request->all();
            $wargaData['foto_ktp'] = $fotoKtpPath;
            $wargaData['created_by'] = auth()->id();

            // Set domisili sama dengan KTP jika tidak diisi
            if (empty($wargaData['alamat_domisili'])) {
                $wargaData['alamat_domisili'] = $wargaData['alamat_ktp'];
                $wargaData['rt_domisili'] = $wargaData['rt_ktp'];
                $wargaData['rw_domisili'] = $wargaData['rw_ktp'];
                $wargaData['kelurahan_domisili'] = $wargaData['kelurahan_ktp'];
                $wargaData['status_domisili'] = 'Tetap';
            }

            $warga = Warga::create($wargaData);

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'warga',
                'id_referensi' => $warga->id,
                'jenis_aktivitas' => 'create',
                'deskripsi' => "Menambah data warga: {$warga->nama_lengkap} (NIK: {$warga->nik})",
                'data_baru' => json_encode($warga->toArray())
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data warga berhasil ditambahkan',
                'data' => $warga->load(['keluarga', 'createdBy'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah data warga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified warga.
     */
    public function show(Warga $warga): JsonResponse
    {
        try {
            $warga->load(['keluarga', 'keluarga.anggotaKeluarga', 'createdBy', 'updatedBy']);

            return response()->json([
                'success' => true,
                'data' => $warga,
                'message' => 'Data warga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data warga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified warga.
     */
    public function edit(Warga $warga): JsonResponse
    {
        try {
            $warga->load(['keluarga', 'createdBy', 'updatedBy']);

            $data = [
                'warga' => $warga,
                'daftar_agama' => Warga::getDaftarAgama(),
                'daftar_status_perkawinan' => Warga::getDaftarStatusPerkawinan(),
                'daftar_pekerjaan' => Warga::getDaftarPekerjaan(),
                'daftar_pendidikan' => Warga::getDaftarPendidikan(),
                'daftar_hubungan' => Keluarga::getDaftarHubungan(),
                'keluarga_list' => Keluarga::withKepalaKeluarga()->get(),
                'rt_list' => Wilayah::where('tingkat', 'RT')->pluck('kode', 'id'),
                'rw_list' => Wilayah::where('tingkat', 'RW')->pluck('kode', 'id'),
                'kelurahan_list' => Wilayah::where('tingkat', 'Kelurahan')->pluck('kode', 'id'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Form edit warga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat form edit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified warga in storage.
     */
    public function update(Request $request, Warga $warga): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                // Data KTP (required fields)
                'nik' => 'required|digits:16|unique:warga,nik,' . $warga->id,
                'nama_lengkap' => 'required|string|max:255',
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date|before:today',
                'jenis_kelamin' => 'required|in:L,P',
                'golongan_darah' => 'nullable|in:A,B,AB,O',
                'alamat_ktp' => 'required|string|max:500',
                'rt_ktp' => 'required|string|max:10',
                'rw_ktp' => 'required|string|max:10',
                'kelurahan_ktp' => 'required|string|max:100',
                'kecamatan_ktp' => 'required|string|max:100',
                'kabupaten_ktp' => 'required|string|max:100',
                'provinsi_ktp' => 'required|string|max:100',
                'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
                'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
                'pekerjaan' => 'required|string|max:100',
                'kewarganegaraan' => 'required|in:WNI,WNA',
                'pendidikan_terakhir' => 'required|in:Tidak/Sekolah,SD,SMP,SMA,D1/D2/D3,S1,S2,S3',
                'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

                // Data Domisili
                'kk_id' => 'nullable|exists:keluarga,id',
                'hubungan_keluarga' => 'nullable|required_if:kk_id,!=,null|string|max:50',
                'alamat_domisili' => 'nullable|string|max:500',
                'rt_domisili' => 'nullable|string|max:10',
                'rw_domisili' => 'nullable|string|max:10',
                'kelurahan_domisili' => 'nullable|string|max:100',
                'no_telepon' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'status_domisili' => 'nullable|in:Tetap,Non Domisili,Luar,Sementara',
                'tanggal_mulai_domisili' => 'nullable|date',
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
            $oldData = $warga->toArray();

            // Handle file upload
            $fotoKtpPath = $warga->foto_ktp;
            if ($request->hasFile('foto_ktp')) {
                // Delete old photo
                if ($warga->foto_ktp && Storage::disk('public')->exists($warga->foto_ktp)) {
                    Storage::disk('public')->delete($warga->foto_ktp);
                }

                $file = $request->file('foto_ktp');
                $filename = 'ktp_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $file->getClientOriginalExtension();
                $fotoKtpPath = $file->storeAs('uploads/ktp', $filename, 'public');
            }

            $wargaData = $request->all();
            $wargaData['foto_ktp'] = $fotoKtpPath;
            $wargaData['updated_by'] = auth()->id();

            // Set domisili sama dengan KTP jika tidak diisi
            if (empty($wargaData['alamat_domisili'])) {
                $wargaData['alamat_domisili'] = $wargaData['alamat_ktp'];
                $wargaData['rt_domisili'] = $wargaData['rt_ktp'];
                $wargaData['rw_domisili'] = $wargaData['rw_ktp'];
                $wargaData['kelurahan_domisili'] = $wargaData['kelurahan_ktp'];
                $wargaData['status_domisili'] = 'Tetap';
            }

            $warga->update($wargaData);

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'warga',
                'id_referensi' => $warga->id,
                'jenis_aktivitas' => 'update',
                'deskripsi' => "Mengupdate data warga: {$warga->nama_lengkap} (NIK: {$warga->nik})",
                'data_lama' => json_encode($oldData),
                'data_baru' => json_encode($warga->fresh()->toArray())
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data warga berhasil diperbarui',
                'data' => $warga->fresh()->load(['keluarga', 'updatedBy'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data warga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified warga from storage.
     */
    public function destroy(Warga $warga): JsonResponse
    {
        try {
            // Check dependencies
            if ($warga->iuran()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warga tidak dapat dihapus karena memiliki data iuran terkait'
                ], 422);
            }

            // Check if warga is kepala keluarga
            if ($warga->keluarga()->exists() || $warga->isKepalaKeluarga()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warga tidak dapat dihapus karena merupakan kepala keluarga'
                ], 422);
            }

            DB::beginTransaction();

            // Store data for logging
            $wargaData = $warga->toArray();

            // Delete photo
            if ($warga->foto_ktp && Storage::disk('public')->exists($warga->foto_ktp)) {
                Storage::disk('public')->delete($warga->foto_ktp);
            }

            $warga->delete();

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'warga',
                'id_referensi' => $warga->id,
                'jenis_aktivitas' => 'delete',
                'deskripsi' => "Menghapus data warga: {$warga->nama_lengkap} (NIK: {$warga->nik})",
                'data_lama' => json_encode($wargaData)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data warga berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data warga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalWarga = Warga::count();
            $wargaDenganKK = Warga::memilikiKK()->count();
            $wargaTanpaKK = Warga::whereNull('kk_id')->count();
            $wargaLaki = Warga::where('jenis_kelamin', 'L')->count();
            $wargaPerempuan = Warga::where('jenis_kelamin', 'P')->count();

            // Statistik by RT/RW
            $wargaByRt = Warga::selectRaw('rt_domisili, count(*) as total')
                ->whereNotNull('rt_domisili')
                ->groupBy('rt_domisili')
                ->orderBy('total', 'desc')
                ->get();

            $wargaByRw = Warga::selectRaw('rw_domisili, count(*) as total')
                ->whereNotNull('rw_domisili')
                ->groupBy('rw_domisili')
                ->orderBy('total', 'desc')
                ->get();

            // Statistik by agama
            $wargaByAgama = Warga::selectRaw('agama, count(*) as total')
                ->groupBy('agama')
                ->orderBy('total', 'desc')
                ->get();

            // Statistik by pendidikan
            $wargaByPendidikan = Warga::selectRaw('pendidikan_terakhir, count(*) as total')
                ->groupBy('pendidikan_terakhir')
                ->orderBy('total', 'desc')
                ->get();

            // Statistik by pekerjaan (top 10)
            $wargaByPekerjaan = Warga::selectRaw('pekerjaan, count(*) as total')
                ->groupBy('pekerjaan')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_warga' => $totalWarga,
                    'warga_dengan_kk' => $wargaDenganKK,
                    'warga_tanpa_kk' => $wargaTanpaKK,
                    'warga_laki' => $wargaLaki,
                    'warga_perempuan' => $wargaPerempuan,
                    'warga_by_rt' => $wargaByRt,
                    'warga_by_rw' => $wargaByRw,
                    'warga_by_agama' => $wargaByAgama,
                    'warga_by_pendidikan' => $wargaByPendidikan,
                    'warga_by_pekerjaan' => $wargaByPekerjaan,
                ],
                'message' => 'Statistik warga berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik warga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export warga data to Excel/CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            // Implementation for export functionality
            return response()->json([
                'success' => false,
                'message' => 'Fitur export akan segera tersedia'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import warga data from Excel/CSV
     */
    public function import(Request $request): JsonResponse
    {
        try {
            // Implementation for import functionality
            return response()->json([
                'success' => false,
                'message' => 'Fitur import akan segera tersedia'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 500);
        }
    }
}
