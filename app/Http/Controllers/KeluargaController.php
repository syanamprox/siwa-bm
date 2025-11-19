<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

            // Add foto_kk_url accessor to each item
            $keluargaItems = $keluarga->items();
            $formattedItems = collect($keluargaItems)->map(function ($item) {
                $itemArray = $item->toArray();
                $itemArray['foto_kk_url'] = $item->foto_kk_url; // This will trigger the accessor
                return $itemArray;
            });

            return response()->json([
                'success' => true,
                'data' => $formattedItems,
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
     * Show the form for creating a new keluarga with multi-person input
     */
    public function create(): JsonResponse
    {
        try {
            $data = [
                'daftar_hubungan' => [
                    'Kepala Keluarga',
                    'Suami',
                    'Istri',
                    'Anak',
                    'Menantu',
                    'Cucu',
                    'Orang Tua',
                    'Mertua',
                    'Famili Lain',
                    'Pembantu',
                    'Lainnya'
                ],
                'daftar_agama' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'],
                'daftar_pendidikan' => [
                    'Tidak Sekolah', 'SD/sederajat', 'SMP/sederajat', 'SMA/sederajat',
                    'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'
                ],
                'daftar_pekerjaan' => [
                    'Belum/Tidak Bekerja', 'Mengurus Rumah Tangga', 'Pelajar/Mahasiswa',
                    'Pensiunan', 'Pegawai Negeri Sipil', 'TNI/Polisi', 'Guru/Dosen',
                    'Pegawai Swasta', 'Wiraswasta', 'Petani/Pekebun', 'Peternak',
                    'Nelayan/Perikanan', 'Industri', 'Konstruksi', 'Transportasi',
                    'Karyawan Honorer', 'Tenaga Kesehatan', 'Lainnya'
                ],
                'daftar_status_kawin' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'],
                'daftar_goldar' => ['A', 'B', 'AB', 'O', 'A+', 'B+', 'AB+', 'O+', 'Tidak Tahu'],
                'rt_list' => Wilayah::where('tingkat', 'RT')->orderBy('kode')->get(),
                'rw_list' => Wilayah::where('tingkat', 'RW')->orderBy('kode')->get(),
                'kelurahan_list' => Wilayah::where('tingkat', 'Kelurahan')->orderBy('kode')->get(),
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
     * Store a newly created keluarga with multiple warga in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate keluarga data
            $keluargaValidator = Validator::make($request->all(), [
                'no_kk' => 'required|digits:16|unique:keluargas,no_kk',
                'foto_kk' => 'nullable|image|mimes:jpeg,jpg,png|max:2048', // Max 2MB

                // Alamat KTP (Input Manual Lengkap)
                'alamat_kk' => 'required|string|max:500',
                'rt_kk' => 'nullable|string|max:10',
                'rw_kk' => 'nullable|string|max:10',
                'kelurahan_kk' => 'nullable|string|max:100',
                'kecamatan_kk' => 'nullable|string|max:100',
                'kabupaten_kk' => 'nullable|string|max:100',
                'provinsi_kk' => 'nullable|string|max:100',

                // Alamat Domisili (Koneksi Sistem Wilayah)
                'alamat_domisili' => 'nullable|string|max:500',
                'rt_id' => 'required|exists:wilayahs,id',

                // Status & Keterangan
                'status_domisili_keluarga' => 'required|in:Tetap,Non Domisili,Luar,Sementara',
                'tanggal_mulai_domisili_keluarga' => 'nullable|date',
                'keterangan_status' => 'nullable|string|max:255',

                // Input Mode & Warga Data
                'input_mode' => 'required|in:single,multi',
                'warga_data' => 'required|array|min:1',
                'warga_data.*.nik' => 'required|digits:16|unique:wargas,nik',
                'warga_data.*.nama_lengkap' => 'required|string|max:255',
                'warga_data.*.jenis_kelamin' => 'required|in:L,P',
                'warga_data.*.tempat_lahir' => 'nullable|string|max:100',
                'warga_data.*.tanggal_lahir' => 'nullable|date',
                'warga_data.*.agama' => 'nullable|string|max:50',
                'warga_data.*.pendidikan_terakhir' => 'nullable|string|max:100',
                'warga_data.*.pekerjaan' => 'nullable|string|max:100',
                'warga_data.*.status_perkawinan' => 'nullable|string|max:50',
                'warga_data.*.kewarganegaraan' => 'nullable|string|max:50',
                'warga_data.*.golongan_darah' => 'nullable|string|in:A,B,AB,O,A+,B+,AB+,O+,A-,B-,AB-,O-,Tidak Tahu',
                'warga_data.*.hubungan_keluarga' => 'required|string|max:50',
                'warga_data.*.no_telepon' => 'nullable|string|max:20',
                'warga_data.*.nama_ayah' => 'nullable|string|max:255',
                'warga_data.*.nama_ibu' => 'nullable|string|max:255',
            ], [
                'warga_data.*.nik.unique' => 'NIK :input sudah terdaftar',
                'warga_data.*.nik.digits' => 'NIK :input harus 16 digit',
            ]);

            if ($keluargaValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $keluargaValidator->errors()
                ], 422);
            }

            // Check if at least one warga is marked as Kepala Keluarga
            $hasKepalaKeluarga = collect($request->warga_data)->contains('hubungan_keluarga', 'Kepala Keluarga');
            if (!$hasKepalaKeluarga) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harus ada minimal satu Kepala Keluarga',
                    'errors' => ['kepala_keluarga' => ['Harus ada minimal satu Kepala Keluarga']]
                ], 422);
            }

            DB::beginTransaction();

            // Handle upload foto KK
            $fotoKkPath = null;
            if ($request->hasFile('foto_kk')) {
                $file = $request->file('foto_kk');
                $fileName = 'kk_' . $request->no_kk . '_' . time() . '.' . $file->getClientOriginalExtension();
                $fotoKkPath = $file->storeAs('keluarga/foto_kk', $fileName, 'public');
            }

            // Create keluarga (wilayah info di-load dynamically via rt_id relationship)
            $keluarga = Keluarga::create([
                // Data Kartu Keluarga
                'no_kk' => $request->no_kk,
                'foto_kk' => $fotoKkPath,

                // Alamat KTP (Input Manual Lengkap)
                'alamat_kk' => $request->alamat_kk,
                'rt_kk' => $request->rt_kk,
                'rw_kk' => $request->rw_kk,
                'kelurahan_kk' => $request->kelurahan_kk,
                'kecamatan_kk' => $request->kecamatan_kk,
                'kabupaten_kk' => $request->kabupaten_kk,
                'provinsi_kk' => $request->provinsi_kk,

                // Alamat Domisili (rt_id only - dynamic loading)
                'alamat_domisili' => $request->alamat_domisili,
                'rt_id' => $request->rt_id,

                // Status & Keterangan
                'status_domisili_keluarga' => $request->status_domisili_keluarga,
                'tanggal_mulai_domisili_keluarga' => $request->tanggal_mulai_domisili_keluarga ?? null,
                'keterangan_status' => $request->keterangan_status ?? null,
            ]);

            $kepalaKeluargaId = null;
            $createdWarga = [];

            // Create all warga
            foreach ($request->warga_data as $index => $wargaData) {
                $warga = Warga::create([
                    'nik' => $wargaData['nik'],
                    'nama_lengkap' => $wargaData['nama_lengkap'],
                    'jenis_kelamin' => $wargaData['jenis_kelamin'],
                    'tempat_lahir' => $wargaData['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $wargaData['tanggal_lahir'] ?? null,
                    'agama' => $wargaData['agama'] ?? null,
                    'pendidikan_terakhir' => $wargaData['pendidikan_terakhir'] ?? null,
                    'pekerjaan' => $wargaData['pekerjaan'] ?? null,
                    'status_perkawinan' => $wargaData['status_perkawinan'] ?? null,
                    'kewarganegaraan' => $wargaData['kewarganegaraan'] ?? 'WNI',
                    'golongan_darah' => $wargaData['golongan_darah'] ?? null,
                    'hubungan_keluarga' => $wargaData['hubungan_keluarga'],
                    'no_telepon' => $wargaData['no_telepon'] ?? null,
                    'nama_ayah' => $wargaData['nama_ayah'] ?? null,
                    'nama_ibu' => $wargaData['nama_ibu'] ?? null,
                    'kk_id' => $keluarga->id,
                    'created_by' => auth()->id(),
                ]);

                $createdWarga[] = $warga;

                // Set as kepala keluarga if specified
                if ($wargaData['hubungan_keluarga'] === 'Kepala Keluarga') {
                    $kepalaKeluargaId = $warga->id;
                    $keluarga->update(['kepala_keluarga_id' => $warga->id]);
                }
            }

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'module' => 'keluarga',
                'description' => "Menambah data keluarga: {$keluarga->no_kk} dengan " . count($createdWarga) . " anggota",
                'new_data' => json_encode([
                    'keluarga' => $keluarga->toArray(),
                    'warga_count' => count($createdWarga),
                    'warga_list' => collect($createdWarga)->pluck('nama_lengkap')->toArray()
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga dengan ' . count($createdWarga) . ' anggota berhasil ditambahkan',
                'data' => $keluarga->fresh()->load(['kepalaKeluarga', 'anggotaKeluarga'])
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
                'daftar_hubungan' => [
                    'Kepala Keluarga',
                    'Suami',
                    'Istri',
                    'Anak',
                    'Menantu',
                    'Cucu',
                    'Orang Tua',
                    'Mertua',
                    'Famili Lain',
                    'Pembantu',
                    'Lainnya'
                ],
                'daftar_agama' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'],
                'daftar_pendidikan' => [
                    'Tidak Sekolah', 'SD/sederajat', 'SMP/sederajat', 'SMA/sederajat',
                    'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'
                ],
                'daftar_pekerjaan' => [
                    'Belum/Tidak Bekerja', 'Mengurus Rumah Tangga', 'Pelajar/Mahasiswa',
                    'Pensiunan', 'Pegawai Negeri Sipil', 'TNI/Polisi', 'Guru/Dosen',
                    'Pegawai Swasta', 'Wiraswasta', 'Petani/Pekebun', 'Peternak',
                    'Nelayan/Perikanan', 'Industri', 'Konstruksi', 'Transportasi',
                    'Karyawan Honorer', 'Tenaga Kesehatan', 'Lainnya'
                ],
                'daftar_status_kawin' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'],
                'daftar_goldar' => ['A', 'B', 'AB', 'O', 'A+', 'B+', 'AB+', 'O+', 'Tidak Tahu'],
                'warga_list' => Warga::whereNull('kk_id')->orWhere('kk_id', $keluarga->id)->get(),
                'rt_list' => Wilayah::where('tingkat', 'RT')->orderBy('kode')->get(),
                'rw_list' => Wilayah::where('tingkat', 'RW')->orderBy('kode')->get(),
                'kelurahan_list' => Wilayah::where('tingkat', 'Kelurahan')->orderBy('kode')->get(),
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
                'no_kk' => 'required|digits:16|unique:keluargas,no_kk,' . $keluarga->id,
                'foto_kk' => 'nullable|image|mimes:jpeg,jpg,png|max:2048', // Max 2MB

                // Alamat KTP (Input Manual Lengkap)
                'alamat_kk' => 'required|string|max:500',
                'rt_kk' => 'nullable|string|max:10',
                'rw_kk' => 'nullable|string|max:10',
                'kelurahan_kk' => 'nullable|string|max:100',
                'kecamatan_kk' => 'nullable|string|max:100',
                'kabupaten_kk' => 'nullable|string|max:100',
                'provinsi_kk' => 'nullable|string|max:100',

                // Alamat Domisili (Koneksi Sistem Wilayah)
                'alamat_domisili' => 'nullable|string|max:500',
                'rt_id' => 'required|exists:wilayahs,id',

                // Status & Keterangan
                'status_domisili_keluarga' => 'required|in:Tetap,Non Domisili,Luar,Sementara',
                'tanggal_mulai_domisili_keluarga' => 'nullable|date',
                'keterangan_status' => 'nullable|string|max:255',
                'kepala_keluarga_id' => 'nullable|exists:wargas,id|unique:keluargas,kepala_keluarga_id,' . $keluarga->id,
                'anggota_ids' => 'nullable|array',
                'anggota_ids.*' => 'exists:wargas,id',
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

            // Handle upload foto KK jika ada file baru
            $fotoKkPath = $keluarga->foto_kk; // Keep existing file if no new upload
            if ($request->hasFile('foto_kk')) {
                // Delete old file if exists
                if ($keluarga->foto_kk && Storage::disk('public')->exists($keluarga->foto_kk)) {
                    Storage::disk('public')->delete($keluarga->foto_kk);
                }

                // Upload new file
                $file = $request->file('foto_kk');
                $fileName = 'kk_' . $request->no_kk . '_' . time() . '.' . $file->getClientOriginalExtension();
                $fotoKkPath = $file->storeAs('keluarga/foto_kk', $fileName, 'public');
            }

            // Update keluarga data (wilayah info di-load dynamically via rt_id relationship)
            $keluarga->update([
                // Data Kartu Keluarga
                'no_kk' => $request->no_kk,
                'foto_kk' => $fotoKkPath,

                // Alamat KTP (Input Manual Lengkap)
                'alamat_kk' => $request->alamat_kk,
                'rt_kk' => $request->rt_kk,
                'rw_kk' => $request->rw_kk,
                'kelurahan_kk' => $request->kelurahan_kk,
                'kecamatan_kk' => $request->kecamatan_kk,
                'kabupaten_kk' => $request->kabupaten_kk,
                'provinsi_kk' => $request->provinsi_kk,

                // Alamat Domisili (rt_id only - dynamic loading)
                'alamat_domisili' => $request->alamat_domisili,
                'rt_id' => $request->rt_id,

                // Status & Keterangan
                'status_domisili_keluarga' => $request->status_domisili_keluarga,
                'tanggal_mulai_domisili_keluarga' => $request->tanggal_mulai_domisili_keluarga ?? null,
                'keterangan_status' => $request->keterangan_status ?? null,
                'kepala_keluarga_id' => $request->kepala_keluarga_id,
            ]);

            // Update kepala keluarga
            if ($request->filled('kepala_keluarga_id')) {
                $newKepalaKeluarga = Warga::find($request->kepala_keluarga_id);
                if ($newKepalaKeluarga) {
                    // Remove kepala keluarga status from previous kepala
                    if ($keluarga->kepala_keluarga_id && $keluarga->kepala_keluarga_id != $request->kepala_keluarga_id) {
                        $oldKepalaKeluarga = Warga::find($keluarga->kepala_keluarga_id);
                        if ($oldKepalaKeluarga) {
                            $oldKepalaKeluarga->update([
                                'hubungan_keluarga' => 'Anggota Keluarga'
                            ]);
                        }
                    }

                    $newKepalaKeluarga->update([
                        'hubungan_keluarga' => 'Kepala Keluarga',
                        'kk_id' => $keluarga->id,
                    ]);
                }
            }

            // Update anggota keluarga
            // Handle anggota data from warga_data (for create/edit from form)
            if ($request->filled('warga_data')) {
                // Remove all current anggota except kepala keluarga
                $currentAnggota = $keluarga->anggotaKeluarga()->get();
                foreach ($currentAnggota as $anggota) {
                    if ($anggota->hubungan_keluarga !== 'Kepala Keluarga') {
                        $anggota->update([
                            'kk_id' => null,
                            'hubungan_keluarga' => 'Lainnya'
                        ]);
                    }
                }

                // Update or create anggota from warga_data
                foreach ($request->warga_data as $index => $wargaData) {
                    // Find existing warga by NIK or create new
                    $warga = Warga::where('nik', $wargaData['nik'])->first();

                    if (!$warga) {
                        // Create new warga
                        $warga = Warga::create([
                            'nik' => $wargaData['nik'],
                            'nama_lengkap' => $wargaData['nama_lengkap'],
                            'jenis_kelamin' => $wargaData['jenis_kelamin'],
                            'tempat_lahir' => $wargaData['tempat_lahir'] ?? null,
                            'tanggal_lahir' => $wargaData['tanggal_lahir'] ?? null,
                            'agama' => $wargaData['agama'] ?? null,
                            'pendidikan_terakhir' => $wargaData['pendidikan_terakhir'] ?? null,
                            'pekerjaan' => $wargaData['pekerjaan'] ?? null,
                            'status_perkawinan' => $wargaData['status_perkawinan'] ?? null,
                            'kewarganegaraan' => $wargaData['kewarganegaraan'] ?? 'WNI',
                            'golongan_darah' => $wargaData['golongan_darah'] ?? null,
                            'hubungan_keluarga' => $wargaData['hubungan_keluarga'],
                            'no_telepon' => $wargaData['no_telepon'] ?? null,
                            'kk_id' => $keluarga->id,
                            'created_by' => auth()->id(),
                        ]);
                    } else {
                        // Update existing warga
                        $warga->update([
                            'nama_lengkap' => $wargaData['nama_lengkap'],
                            'jenis_kelamin' => $wargaData['jenis_kelamin'],
                            'tempat_lahir' => $wargaData['tempat_lahir'] ?? null,
                            'tanggal_lahir' => $wargaData['tanggal_lahir'] ?? null,
                            'agama' => $wargaData['agama'] ?? null,
                            'pendidikan_terakhir' => $wargaData['pendidikan_terakhir'] ?? null,
                            'pekerjaan' => $wargaData['pekerjaan'] ?? null,
                            'status_perkawinan' => $wargaData['status_perkawinan'] ?? null,
                            'kewarganegaraan' => $wargaData['kewarganegaraan'] ?? 'WNI',
                            'golongan_darah' => $wargaData['golongan_darah'] ?? null,
                            'hubungan_keluarga' => $wargaData['hubungan_keluarga'],
                            'no_telepon' => $wargaData['no_telepon'] ?? null,
                            'nama_ayah' => $wargaData['nama_ayah'] ?? null,
                            'nama_ibu' => $wargaData['nama_ibu'] ?? null,
                            'kk_id' => $keluarga->id,
                        ]);

                        // Set as kepala keluarga if specified
                        if ($wargaData['hubungan_keluarga'] === 'Kepala Keluarga') {
                            $keluarga->update(['kepala_keluarga_id' => $warga->id]);
                        }
                    }
                }
            } elseif ($request->filled('anggota_ids')) {
                // Handle anggota_ids (for linking existing warga)
                // Remove all current anggota except kepala keluarga
                $currentAnggota = $keluarga->anggotaKeluarga()->get();
                foreach ($currentAnggota as $anggota) {
                    if ($anggota->id != $keluarga->kepala_keluarga_id) {
                        $anggota->update([
                            'kk_id' => null,
                            'hubungan_keluarga' => 'Lainnya'
                        ]);
                    }
                }

                // Add new anggota
                foreach ($request->anggota_ids as $index => $anggotaId) {
                    $warga = Warga::find($anggotaId);
                    if ($warga && $warga->id != $keluarga->kepala_keluarga_id) {
                        $hubungan = $request->hubungan_anggota[$index] ?? 'Anggota Keluarga';
                        $warga->update([
                            'kk_id' => $keluarga->id,
                            'hubungan_keluarga' => $hubungan,
                        ]);
                    }
                }
            }

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'module' => 'keluarga',
                'action' => 'update',
                'description' => "Mengupdate data keluarga: {$keluarga->no_kk}",
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
            DB::beginTransaction();

            // Store data for logging
            $keluargaData = $keluarga->toArray();

            // Soft delete all warga in this keluarga
            $anggotaList = $keluarga->anggotaKeluarga()->get();
            foreach ($anggotaList as $anggota) {
                $anggota->delete(); // Soft delete warga
            }

            // Soft delete the keluarga (iuran data preserved)
            $keluarga->delete();

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'module' => 'keluarga',
                'action' => 'delete',
                'description' => "Menghapus data keluarga: {$keluarga->no_kk} (beserta {$anggotaList->count()} anggota, iuran dipertahankan)",
                'data_lama' => json_encode($keluargaData)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga dan anggota berhasil dihapus. Data iuran dipertahankan untuk audit.'
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
                'warga_id' => 'required|exists:wargas,id',
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
                'module' => 'keluarga',
                        'action' => 'update',
                'description' => "Menambah anggota ke keluarga {$keluarga->no_kk}: {$warga->nama_lengkap}",
                'new_data' => json_encode(['warga_id' => $warga->id, 'hubungan' => $request->hubungan_keluarga])
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
                'module' => 'keluarga',
                        'action' => 'update',
                'description' => "Menghapus anggota dari keluarga {$keluarga->no_kk}: {$warga->nama_lengkap}",
                'old_data' => json_encode(['warga_id' => $warga->id])
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

    /**
     * Get wilayah data for cascading dropdown
     */
    public function getWilayah(Request $request): JsonResponse
    {
        try {
            $level = $request->get('level'); // kelurahan, rw, rt
            $parentId = $request->get('parent_id'); // untuk get children

            // Auto-match parameters
            $rtName = $request->get('rt_name');
            $rwName = $request->get('rw_name');
            $kelurahanName = $request->get('kelurahan_name');

            $query = Wilayah::query();

            if ($level) {
                $query->where('tingkat', ucfirst($level));
            }

            if ($parentId) {
                $query->where('parent_id', $parentId);
            } else {
                // For top level (kelurahan), get records with no parent
                if ($level === 'kelurahan') {
                    $query->whereNull('parent_id');
                }
            }

            // Auto-match logic for RT level
            if ($level === 'rt' && $rtName && $rwName && $kelurahanName) {
                // First find kelurahan
                $kelurahan = Wilayah::where('tingkat', 'Kelurahan')
                    ->where('nama', 'like', '%' . $kelurahanName . '%')
                    ->first();

                if ($kelurahan) {
                    // Then find RW under that kelurahan
                    $rw = Wilayah::where('tingkat', 'Rw')
                        ->where('parent_id', $kelurahan->id)
                        ->where('nama', 'like', '%' . $rwName . '%')
                        ->first();

                    if ($rw) {
                        // Finally find RT under that RW
                        $query->where('parent_id', $rw->id)
                            ->where('nama', 'like', '%' . $rtName . '%');
                    }
                }
            }

            $wilayah = $query->orderBy('kode')->get();

            return response()->json([
                'success' => true,
                'data' => $wilayah,
                'message' => 'Data wilayah berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data wilayah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get RT data with kelurahan and RW info for display
     */
    public function getRtInfo(Request $request): JsonResponse
    {
        try {
            $rtId = $request->get('rt_id');

            if (!$rtId) {
                return response()->json([
                    'success' => false,
                    'message' => 'RT ID is required'
                ], 400);
            }

            $rt = Wilayah::with('parent.parent')->find($rtId);

            if (!$rt) {
                return response()->json([
                    'success' => false,
                    'message' => 'RT not found'
                ], 404);
            }

            // Build complete address info
            $rw = $rt->parent;
            $kelurahan = $rw ? $rw->parent : null;

            $addressInfo = [
                'rt' => [
                    'id' => $rt->id,
                    'kode' => $rt->kode,
                    'nama' => $rt->nama
                ],
                'rw' => $rw ? [
                    'id' => $rw->id,
                    'kode' => $rw->kode,
                    'nama' => $rw->nama
                ] : null,
                'kelurahan' => $kelurahan ? [
                    'id' => $kelurahan->id,
                    'nama' => $kelurahan->nama
                ] : null,
                'alamat_lengkap' => $rt && $rw && $kelurahan
                    ? "RT {$rt->kode}/RW {$rw->kode}, {$kelurahan->nama}, Wonocolo, Surabaya, Jawa Timur"
                    : ''
            ];

            return response()->json([
                'success' => true,
                'data' => $addressInfo,
                'message' => 'Info RT berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat info RT: ' . $e->getMessage()
            ], 500);
        }
    }
}
