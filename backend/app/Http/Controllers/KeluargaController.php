<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\Warga;
use App\Models\Wilayah;
use App\Models\AktivitasLog;
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

            // Filter by Kelurahan
            if ($request->filled('kelurahan')) {
                $query->kelurahan($request->kelurahan);
            }

            // Filter by Status
            if ($request->filled('status_filter')) {
                $query->status($request->status_filter);
            }

            // Sort
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $keluarga = $query->paginate($perPage);

            // Add accessors to each item for frontend
            $keluargaItems = $keluarga->items();
            $formattedItems = collect($keluargaItems)->map(function ($item) {
                $itemArray = $item->toArray();
                $itemArray['foto_kk_url'] = $item->foto_kk_url; // Foto KK accessor
                $itemArray['status_label'] = $item->status_label; // Status label
                $itemArray['status_badge_class'] = $item->status_badge_class; // Status badge class
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
                $fileName = 'kk_' . str_replace(' ', '', $request->no_kk) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $fotoKkPath = $file->storeAs('documents/kk', $fileName, 'public');
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
                $fileName = 'kk_' . str_replace(' ', '', $request->no_kk) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $fotoKkPath = $file->storeAs('documents/kk', $fileName, 'public');
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
     * Update status keluarga
     */
    public function updateStatus(Request $request, Keluarga $keluarga): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status_keluarga' => 'required|in:Aktif,Pindah,Non-Aktif,Dibubarkan',
                'keterangan_status' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldStatus = $keluarga->status_keluarga;
            $newStatus = $request->status_keluarga;
            $keterangan = $request->keterangan_status;

            // Update status menggunakan method dari model
            $success = $keluarga->updateStatus($newStatus, $keterangan);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate status keluarga'
                ], 500);
            }

            
            // Reload data untuk response
            $keluarga->refresh();

            return response()->json([
                'success' => true,
                'message' => "Status keluarga berhasil diubah menjadi {$newStatus}",
                'data' => [
                    'id' => $keluarga->id,
                    'no_kk' => $keluarga->no_kk,
                    'status_keluarga' => $keluarga->status_keluarga,
                    'status_label' => $keluarga->status_label,
                    'status_badge_class' => $keluarga->status_badge_class,
                    'keterangan_status' => $keluarga->keterangan_status,
                    'tanggal_status' => $keluarga->tanggal_status?->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status keluarga: ' . $e->getMessage()
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

    /**
     * Import keluarga and warga data from Excel file
     */
    public function import(Request $request): JsonResponse
    {
        // Check if file exists first
        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded',
                'errors' => ['Please select a file to upload'],
                'debug_info' => [
                    'has_file' => $request->hasFile('file'),
                    'request_all' => $request->all(),
                    'files_all' => $request->files->all()
                ]
            ], 422);
        }

        $file = $request->file('file');

        // Debug file info
        \Log::info('File details:', [
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'client_mime_type' => $file->getClientMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
            'is_valid' => $file->isValid()
        ]);

        // Simple validation without mime type check for now
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240'  // Just check if it's a file and within size limit
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed: ', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->all(),
                'file_info' => [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'client_mime_type' => $file->getClientMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getErrorMessage()
                ]
            ], 422);
        }

        // Additional file extension check
        $allowedExtensions = ['xlsx', 'xls'];
        $fileExtension = strtolower($file->getClientOriginalExtension());

        if (!in_array($fileExtension, $allowedExtensions)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file format',
                'errors' => ['Only Excel files (.xlsx, .xls) are allowed'],
                'file_info' => [
                    'extension' => $fileExtension,
                    'allowed_extensions' => $allowedExtensions
                ]
            ], 422);
        }

        try {
            // Import both sheets
            $keluargaData = $this->parseKeluargaSheet($file);
            $wargaData = $this->parseWargaSheet($file);

            // Validate data
            $validator = new SimpleImportValidator();
            $errors = $validator->validate($keluargaData, $wargaData);

            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'errors' => $errors
                ], 422);
            }

            // Import data
            $importService = new SimpleImportService();
            $importService->import($keluargaData, $wargaData);

            // Log activity
            AktivitasLog::create([
                'user_id' => auth()->id(),
                'action' => 'import',
                'module' => 'keluarga',
                'description' => 'Import data keluarga dan warga dari Excel',
                'old_data' => null,
                'new_data' => json_encode([
                    'keluarga_count' => count($keluargaData),
                    'warga_count' => count($wargaData),
                    'file_name' => $file->getClientOriginalName()
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent() ?? 'Unknown'
            ]);

            return response()->json([
                'success' => true,
                'keluarga_count' => count($keluargaData),
                'warga_count' => count($wargaData),
                'message' => 'Data berhasil diimport'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/templates/import-keluarga-warga.xlsx');

        // Create template if doesn't exist
        if (!file_exists($filePath)) {
            $this->createImportTemplate();
        }

        return response()->download($filePath, 'template_import_keluarga_warga.xlsx');
    }

    /**
     * Parse keluarga sheet from Excel file
     */
    private function parseKeluargaSheet($file): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $worksheet = $spreadsheet->getSheetByName('Keluarga');

        if (!$worksheet) {
            throw new \Exception('Sheet "Keluarga" tidak ditemukan');
        }

        $data = [];
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        // Skip header row (row 1)
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            // Skip empty rows
            if (empty($rowData[0])) continue;

            // Extract first row from the nested array and ensure values are strings
            $rowValues = $rowData[0] ?? [];

            $data[] = [
                'no_kk' => (string) ($rowValues[0] ?? ''),
                'kepala_keluarga_nik' => (string) ($rowValues[1] ?? ''),
                'alamat_kk' => (string) ($rowValues[2] ?? ''),
                'rt_kk' => (string) ($rowValues[3] ?? ''),
                'rw_kk' => (string) ($rowValues[4] ?? ''),
                'kelurahan_kk' => (string) ($rowValues[5] ?? ''),
                'kecamatan_kk' => (string) ($rowValues[6] ?? 'Wonocolo'),
                'kabupaten_kk' => (string) ($rowValues[7] ?? 'Surabaya'),
                'provinsi_kk' => (string) ($rowValues[8] ?? 'Jawa Timur'),
                'alamat_domisili' => !empty($rowValues[9]) ? (string) $rowValues[9] : null,
                'rt_id' => !empty($rowValues[10]) ? (string) $rowValues[10] : null,
                'status_domisili_keluarga' => (string) ($rowValues[11] ?? 'Tetap'),
                'tanggal_mulai_domisili' => !empty($rowValues[12]) ? (string) $rowValues[12] : null,
            ];
        }

        return $data;
    }

    /**
     * Parse warga sheet from Excel file
     */
    private function parseWargaSheet($file): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $worksheet = $spreadsheet->getSheetByName('Warga');

        if (!$worksheet) {
            throw new \Exception('Sheet "Warga" tidak ditemukan');
        }

        $data = [];
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        // Skip header row (row 1)
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            // Skip empty rows
            if (empty($rowData[0])) continue;

            // Extract first row from the nested array and ensure values are strings
            $rowValues = $rowData[0] ?? [];

            $data[] = [
                'no_kk' => (string) ($rowValues[0] ?? ''),
                'nik' => (string) ($rowValues[1] ?? ''),
                'nama_lengkap' => (string) ($rowValues[2] ?? ''),
                'tempat_lahir' => (string) ($rowValues[3] ?? ''),
                'tanggal_lahir' => (string) ($rowValues[4] ?? ''),
                'jenis_kelamin' => (string) ($rowValues[5] ?? ''),
                'golongan_darah' => !empty($rowValues[6]) ? (string) $rowValues[6] : null,
                'agama' => (string) ($rowValues[7] ?? ''),
                'status_perkawinan' => (string) ($rowValues[8] ?? ''),
                'pekerjaan' => (string) ($rowValues[9] ?? ''),
                'kewarganegaraan' => (string) ($rowValues[10] ?? 'WNI'),
                'pendidikan_terakhir' => (string) ($rowValues[11] ?? ''),
                'hubungan_keluarga' => (string) ($rowValues[12] ?? ''),
                'no_telepon' => !empty($rowValues[13]) ? (string) $rowValues[13] : null,
                'email' => !empty($rowValues[14]) ? (string) $rowValues[14] : null,
                'nama_ayah' => !empty($rowValues[15]) ? (string) $rowValues[15] : null,
                'nama_ibu' => !empty($rowValues[16]) ? (string) $rowValues[16] : null,
            ];
        }

        return $data;
    }

    /**
     * Create Excel template for import
     */
    private function createImportTemplate()
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            $filePath = storage_path('app/public/templates/import-keluarga-warga.xlsx');

        // Remove default sheet and create new sheets
        $spreadsheet->removeSheetByIndex(0);

        // Create Keluarga sheet
        $keluargaSheet = $spreadsheet->createSheet();
        $keluargaSheet->setTitle('Keluarga');

        // Headers for Keluarga sheet
        $keluargaHeaders = [
            'no_kk', 'kepala_keluarga_nik', 'alamat_kk', 'rt_kk', 'rw_kk',
            'kelurahan_kk', 'kecamatan_kk', 'kabupaten_kk', 'provinsi_kk',
            'alamat_domisili', 'rt_id', 'status_domisili_keluarga', 'tanggal_mulai_domisili'
        ];

        $keluargaSheet->fromArray([$keluargaHeaders], null, 'A1');

        // Style headers
        // Simple styling for headers
        $keluargaSheet->getStyle('A1:M1')->getFont()->setBold(true);

        // Sample data for Keluarga (5 families in Wonocolo with unique data)
        $keluargaSample = [
            ['3578029901220001', '3578028801650001', 'Jl. Ahmad Yani No. 123', '01', '01',
             'Jemursari', 'Wonocolo', 'Surabaya', 'Jawa Timur',
             'Jl. Ahmad Yani No. 123', '16', 'Tetap', '2024-01-15'],

            ['3578029901220002', '3578028801650007', 'Jl. Raya Darmo No. 456', '02', '02',
             'Darmo', 'Wonocolo', 'Surabaya', 'Jawa Timur',
             'Jl. Raya Darmo No. 456', '17', 'Tetap', '2024-02-20'],

            ['3578029901220003', '3578028801650013', 'Jl. Bengawan No. 789', '03', '03',
             'Margorejo', 'Wonocolo', 'Surabaya', 'Jawa Timur',
             'Jl. Bengawan No. 789', '18', 'Tetap', '2024-03-10'],

            ['3578029901220004', '3578028801650019', 'Jl. Embong Malang No. 321', '04', '04',
             'Gubeng', 'Wonocolo', 'Surabaya', 'Jawa Timur',
             'Jl. Embong Malang No. 321', '19', 'Sementara', '2024-04-05'],

            ['3578029901220005', '3578028801650025', 'Jl. Sukolilo No. 654', '05', '05',
             'Sukolilo', 'Wonocolo', 'Surabaya', 'Jawa Timur',
             'Jl. Sukolilo No. 654', '20', 'Tetap', '2024-05-12']
        ];

        $keluargaSheet->fromArray($keluargaSample, null, 'A2');

        // Create Warga sheet
        $wargaSheet = $spreadsheet->createSheet();
        $wargaSheet->setTitle('Warga');

        // Headers for Warga sheet
        $wargaHeaders = [
            'no_kk', 'nik', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin', 'golongan_darah', 'agama', 'status_perkawinan',
            'pekerjaan', 'kewarganegaraan', 'pendidikan_terakhir', 'hubungan_keluarga',
            'no_telepon', 'email', 'nama_ayah', 'nama_ibu'
        ];

        $wargaSheet->fromArray([$wargaHeaders], null, 'A1');

        // Style headers
        $wargaSheet->getStyle('A1:Q1')->getFont()->setBold(true);

        // Sample data for Warga (30 residents, 6 per family with unique data)
        $wargaSample = [
            // Family 1 - KK: 3578029901220001
            ['3578029901220001', '3578028801650001', 'Rudi Hartono', 'Jakarta', '1980-03-15',
             'L', 'A', 'Islam', 'Kawin', 'Manager', 'WNI', 'S1', 'Kepala Keluarga',
             '08111111111', 'rudi@email.com', 'Bambang', 'Sumiati'],
            ['3578029901220001', '3578028801650002', 'Diana Putri', 'Surabaya', '1982-07-20',
             'P', 'B', 'Islam', 'Kawin', 'Guru', 'WNI', 'S1', 'Istri',
             '08111111112', 'diana@email.com', 'Hadi', 'Siti'],
            ['3578029901220001', '3578028801650003', 'Andi Pratama', 'Surabaya', '2005-11-10',
             'L', 'O', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SMA', 'Anak',
             '08111111113', 'andi@email.com', 'Rudi Hartono', 'Diana Putri'],
            ['3578029901220001', '3578028801650004', 'Siti Aminah', 'Surabaya', '2008-04-25',
             'P', 'A', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SMP', 'Anak',
             '08111111114', 'siti.a@email.com', 'Rudi Hartono', 'Diana Putri'],
            ['3578029901220001', '3578028801650005', 'Budi Santoso', 'Surabaya', '2010-09-15',
             'L', 'AB', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SD', 'Anak',
             '08111111115', 'budi@email.com', 'Rudi Hartono', 'Diana Putri'],
            ['3578029901220001', '3578028801650006', 'Mardiyem', 'Jakarta', '1955-12-05',
             'P', 'B', 'Islam', 'Cerai Hidup', 'Pensiunan', 'WNI', 'SMA', 'Lainnya',
             '08111111116', 'mardiyem@email.com', 'Suparno', 'Sukinem'],

            // Family 2 - KK: 3578029901220002
            ['3578029901220002', '3578028801650007', 'Ahmad Fauzi', 'Malang', '1975-06-12',
             'L', 'A', 'Islam', 'Kawin', 'Engineer', 'WNI', 'S1', 'Kepala Keluarga',
             '08122222221', 'fauzi@email.com', 'Chotib', 'Marni'],
            ['3578029901220002', '3578028801650008', 'Fitri Handayani', 'Surabaya', '1977-11-28',
             'P', 'O', 'Islam', 'Kawin', 'Dokter', 'WNI', 'S1', 'Istri',
             '08122222222', 'fitri@email.com', 'Sudirman', 'Ratna'],
            ['3578029901220002', '3578028801650009', 'Rizky Ananda', 'Surabaya', '2002-02-14',
             'L', 'B', 'Islam', 'Belum Kawin', 'Mahasiswa', 'WNI', 'SMA', 'Anak',
             '08122222223', 'rizky@email.com', 'Ahmad Fauzi', 'Fitri Handayani'],
            ['3578029901220002', '3578028801650010', 'Nur Azizah', 'Surabaya', '2004-08-30',
             'P', 'A', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SMA', 'Anak',
             '08122222224', 'azizah@email.com', 'Ahmad Fauzi', 'Fitri Handayani'],
            ['3578029901220002', '3578028801650011', 'Dimas Pratama', 'Surabaya', '2007-01-20',
             'L', 'AB', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SMP', 'Anak',
             '08122222225', 'dimas@email.com', 'Ahmad Fauzi', 'Fitri Handayani'],
            ['3578029901220002', '3578028801650012', 'Siti Aisyah', 'Malang', '1950-05-18',
             'P', 'B', 'Islam', 'Cerai Hidup', 'Pensiunan', 'WNI', 'SMA', 'Lainnya',
             '08122222226', 'aisyah@email.com', 'Karjo', 'Sukini'],

            // Family 3 - KK: 3578029901220003
            ['3578029901220003', '3578028801650013', 'Bambang Sutrisno', 'Surabaya', '1978-09-03',
             'L', 'A', 'Islam', 'Kawin', 'Wiraswasta', 'WNI', 'D3', 'Kepala Keluarga',
             '08133333331', 'bambang@email.com', 'Paijo', 'Warni'],
            ['3578029901220003', '3578028801650014', 'Yuni Astuti', 'Surabaya', '1980-12-17',
             'P', 'O', 'Islam', 'Kawin', 'Perawat', 'WNI', 'D3', 'Istri',
             '08133333332', 'yuni@email.com', 'Wagiman', 'Sukiyem'],
            ['3578029901220003', '3578028801650015', 'Fikri Akbar', 'Surabaya', '2003-05-22',
             'L', 'B', 'Islam', 'Belum Kawin', 'Mahasiswa', 'WNI', 'SMA', 'Anak',
             '08133333333', 'fikri@email.com', 'Bambang Sutrisno', 'Yuni Astuti'],
            ['3578029901220003', '3578028801650016', 'Laila Rahmawati', 'Surabaya', '2006-10-08',
             'P', 'A', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SMP', 'Anak',
             '08133333334', 'laila@email.com', 'Bambang Sutrisno', 'Yuni Astuti'],
            ['3578029901220003', '3578028801650017', 'Arif Rahman', 'Surabaya', '2009-03-14',
             'L', 'AB', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SD', 'Anak',
             '08133333335', 'arif@email.com', 'Bambang Sutrisno', 'Yuni Astuti'],
            ['3578029901220003', '3578028801650018', 'Waginah', 'Surabaya', '1952-07-25',
             'P', 'B', 'Islam', 'Cerai Hidup', 'Ibu Rumah Tangga', 'WNI', 'SD', 'Lainnya',
             '08133333336', 'waginah@email.com', 'Tarmudi', 'Sarini'],

            // Family 4 - KK: 3578029901220004
            ['3578029901220004', '3578028801650019', 'Cahyo Wibowo', 'Sidoarjo', '1972-02-10',
             'L', 'A', 'Islam', 'Kawin', 'PNS', 'WNI', 'S1', 'Kepala Keluarga',
             '08144444441', 'cahyo@email.com', 'Sumarno', 'Sukarti'],
            ['3578029901220004', '3578028801650020', 'Ratna Dewi', 'Surabaya', '1974-06-25',
             'P', 'O', 'Islam', 'Kawin', 'Guru', 'WNI', 'S1', 'Istri',
             '08144444442', 'ratna@email.com', 'Supardi', 'Murwati'],
            ['3578029901220004', '3578028801650021', 'Ghina Aulia', 'Surabaya', '2001-09-30',
             'P', 'B', 'Islam', 'Belum Kawin', 'Mahasiswi', 'WNI', 'S1', 'Anak',
             '08144444443', 'ghina@email.com', 'Cahyo Wibowo', 'Ratna Dewi'],
            ['3578029901220004', '3578028801650022', 'Raka Pratama', 'Surabaya', '2004-01-15',
             'L', 'A', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SMA', 'Anak',
             '08144444444', 'raka@email.com', 'Cahyo Wibowo', 'Ratna Dewi'],
            ['3578029901220004', '3578028801650023', 'Zahra Karimah', 'Surabaya', '2007-07-20',
             'P', 'AB', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SMP', 'Anak',
             '08144444445', 'zahra@email.com', 'Cahyo Wibowo', 'Ratna Dewi'],
            ['3578029901220004', '3578028801650024', 'Sukijan', 'Sidoarjo', '1947-11-12',
             'L', 'B', 'Islam', 'Cerai Mati', 'Pensiunan', 'WNI', 'SMA', 'Lainnya',
             '08144444446', 'sukijan@email.com', 'Pardi', 'Warni'],

            // Family 5 - KK: 3578029901220005
            ['3578029901220005', '3578028801650025', 'Eko Nugroho', 'Madiun', '1976-04-08',
             'L', 'A', 'Islam', 'Kawin', 'Programmer', 'WNI', 'S1', 'Kepala Keluarga',
             '08155555551', 'eko@email.com', 'Tarno', 'Sukini'],
            ['3578029901220005', '3578028801650026', 'Indri Permata', 'Surabaya', '1978-08-23',
             'P', 'O', 'Islam', 'Kawin', 'Accountant', 'WNI', 'S1', 'Istri',
             '08155555552', 'indri@email.com', 'Harsono', 'Sutini'],
            ['3578029901220005', '3578028801650027', 'Rafi Ahmad', 'Surabaya', '2002-12-05',
             'L', 'B', 'Islam', 'Belum Kawin', 'Mahasiswa', 'WNI', 'S1', 'Anak',
             '08155555553', 'rafi@email.com', 'Eko Nugroho', 'Indri Permata'],
            ['3578029901220005', '3578028801650028', 'Naura Sabrina', 'Surabaya', '2005-03-18',
             'P', 'A', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SMA', 'Anak',
             '08155555554', 'naura@email.com', 'Eko Nugroho', 'Indri Permata'],
            ['3578029901220005', '3578028801650029', 'Dafa Putra', 'Surabaya', '2008-10-12',
             'L', 'AB', 'Islam', 'Belum Kawin', 'Pelajar', 'WNI', 'SMP', 'Anak',
             '08155555555', 'dafa@email.com', 'Eko Nugroho', 'Indri Permata'],
            ['3578029901220005', '3578028801650030', 'Saminah', 'Madiun', '1951-02-28',
             'P', 'B', 'Islam', 'Cerai Hidup', 'Pensiunan', 'WNI', 'SD', 'Lainnya',
             '08155555556', 'saminah@email.com', 'Kasiran', 'Parni']
        ];

        $wargaSheet->fromArray($wargaSample, null, 'A2');

        // Create Panduan sheet
        $panduanSheet = $spreadsheet->createSheet();
        $panduanSheet->setTitle('Panduan');

        // Headers for Panduan sheet
        $panduanHeaders = ['Sheet', 'Kolom', 'Deskripsi', 'Format', 'Contoh', 'Wajib'];
        $panduanSheet->fromArray([$panduanHeaders], null, 'A1');
        $panduanSheet->getStyle('A1:F1')->getFont()->setBold(true);

        // Panduan data
        $panduanData = [
            // Keluarga Sheet Guide
            ['Keluarga', 'no_kk', 'Nomor Kartu Keluarga', '16 digit angka', '3578020609220004', 'Ya'],
            ['Keluarga', 'kepala_keluarga_nik', 'NIK Kepala Keluarga', '16 digit angka', '3578027006710001', 'Ya'],
            ['Keluarga', 'alamat_kk', 'Alamat KK', 'Text alamat lengkap', 'Bendul Merisi 4/37', 'Ya'],
            ['Keluarga', 'rt_kk', 'RT KK', '2 digit angka', '02', 'Ya'],
            ['Keluarga', 'rw_kk', 'RW KK', '2 digit angka', '03', 'Ya'],
            ['Keluarga', 'kelurahan_kk', 'Kelurahan KK', 'Text nama kelurahan', 'Darmo', 'Ya'],
            ['Keluarga', 'kecamatan_kk', 'Kecamatan KK', 'Text nama kecamatan', 'Wonocolo', 'Ya'],
            ['Keluarga', 'kabupaten_kk', 'Kabupaten KK', 'Text nama kabupaten', 'Surabaya', 'Ya'],
            ['Keluarga', 'provinsi_kk', 'Provinsi KK', 'Text nama provinsi', 'Jawa Timur', 'Ya'],
            ['Keluarga', 'alamat_domisili', 'Alamat Domisili', 'Text alamat lengkap', 'Bendul Merisi 4/37', 'Ya'],
            ['Keluarga', 'rt_id', 'ID RT (sesuai database)', 'Angka ID RT', '16', 'Ya'],
            ['Keluarga', 'status_domisili_keluarga', 'Status Domisili', 'Tetap/Non Domisili/Luar/Sementara', 'Tetap', 'Ya'],
            ['Keluarga', 'tanggal_mulai_domisili', 'Tanggal Mulai Domisili', 'YYYY-MM-DD', '2022-06-01', 'Ya'],

            // Warga Sheet Guide
            ['Warga', 'no_kk', 'Nomor KK (referensi ke sheet Keluarga)', '16 digit angka', '3578020609220004', 'Ya'],
            ['Warga', 'nik', 'Nomor Induk Kependudukan', '16 digit angka', '3578027006710001', 'Ya'],
            ['Warga', 'nama_lengkap', 'Nama Lengkap', 'Text nama lengkap', 'Ahmad Susanto', 'Ya'],
            ['Warga', 'tempat_lahir', 'Tempat Lahir', 'Text kota/kabupaten', 'Surabaya', 'Ya'],
            ['Warga', 'tanggal_lahir', 'Tanggal Lahir', 'YYYY-MM-DD', '1971-06-30', 'Ya'],
            ['Warga', 'jenis_kelamin', 'Jenis Kelamin', 'L/P', 'L', 'Ya'],
            ['Warga', 'golongan_darah', 'Golongan Darah', 'A/B/AB/O', 'A', 'Ya'],
            ['Warga', 'agama', 'Agama', 'Text nama agama', 'Islam', 'Ya'],
            ['Warga', 'status_perkawinan', 'Status Perkawinan', 'Belum Kawin/Kawin/Cerai Hidup/Cerai Mati', 'Kawin', 'Ya'],
            ['Warga', 'pekerjaan', 'Pekerjaan', 'Text nama pekerjaan', 'Pegawai Swasta', 'Ya'],
            ['Warga', 'kewarganegaraan', 'Kewarganegaraan', 'WNI/WNA', 'WNI', 'Ya'],
            ['Warga', 'pendidikan_terakhir', 'Pendidikan Terakhir', 'SD/SMP/SMA/D3/S1/S2/S3', 'S1', 'Ya'],
            ['Warga', 'hubungan_keluarga', 'Hubungan Keluarga', 'Kepala Keluarga/Suami/Istri/Anak/Menantu/Cucu/Orang Tua/Mertua/Famili Lain/Pembantu/Lainnya', 'Kepala Keluarga', 'Ya'],
            ['Warga', 'no_telepon', 'Nomor Telepon', '10-15 digit angka', '08123456789', 'Tidak'],
            ['Warga', 'email', 'Email', 'Format email valid', 'email@domain.com', 'Tidak'],
            ['Warga', 'nama_ayah', 'Nama Ayah', 'Text nama lengkap ayah', 'Sukarno', 'Ya'],
            ['Warga', 'nama_ibu', 'Nama Ibu', 'Text nama lengkap ibu', 'Siti Aminah', 'Ya'],

            // Additional Information
            ['Informasi Umum', '', 'Tips Tambahan:', '', '', ''],
            ['Informasi Umum', '', '1. Pastikan no_kk di sheet Warga sama dengan data di sheet Keluarga', '', '', ''],
            ['Informasi Umum', '', '2. Gunakan format tanggal YYYY-MM-DD', '', '', ''],
            ['Informasi Umum', '', '3. NIK harus 16 digit angka unik per warga', '', '', ''],
            ['Informasi Umum', '', '4. No KK harus 16 digit angka unik per keluarga', '', '', ''],
            ['Informasi Umum', '', '5. Hubungi admin jika RT ID tidak diketahui', '', '', ''],
            ['Informasi Umum', '', '6. Sistem akan memvalidasi data sebelum import:', '', '', ''],
            ['Informasi Umum', '', '   - Cek duplikasi NIK dan No KK', '', '', ''],
            ['Informasi Umum', '', '   - Validasi format enum (status domisili, status perkawinan, hubungan keluarga)', '', '', ''],
            ['Informasi Umum', '', '   - Verifikasi format tanggal dan angka', '', '', ''],
            ['Informasi Umum', '', '7. Nilai Enum yang Valid:', '', '', ''],
            ['Informasi Umum', 'status_domisili_keluarga', 'Tetap, Non Domisili, Luar, Sementara', '', '', ''],
            ['Informasi Umum', 'status_perkawinan', 'Belum Kawin, Kawin, Cerai Hidup, Cerai Mati', '', '', ''],
            ['Informasi Umum', 'hubungan_keluarga', 'Kepala Keluarga, Suami, Istri, Anak, Menantu, Cucu, Orang Tua, Mertua, Famili Lain, Pembantu, Lainnya', '', '', ''],
        ];

        $panduanSheet->fromArray($panduanData, null, 'A2');

        // Style panduan sheet
        $panduanSheet->getStyle('A:F')->getAlignment()->setWrapText(true);
        $panduanSheet->getColumnDimension('A')->setWidth(15);
        $panduanSheet->getColumnDimension('B')->setWidth(25);
        $panduanSheet->getColumnDimension('C')->setWidth(40);
        $panduanSheet->getColumnDimension('D')->setWidth(25);
        $panduanSheet->getColumnDimension('E')->setWidth(25);
        $panduanSheet->getColumnDimension('F')->setWidth(10);

        // Auto-size columns for all sheets
        foreach (range('A', 'Q') as $column) {
            $keluargaSheet->getColumnDimension($column)->setAutoSize(true);
            $wargaSheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Create directory if doesn't exist
        $directory = storage_path('app/public/templates');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $writer->save($filePath);

        } catch (\Exception $e) {
            \Log::error('Error creating Excel template: ' . $e->getMessage());
            throw new \Exception('Failed to create Excel template: ' . $e->getMessage());
        }
    }
}

/**
 * Simple import validator
 */
class SimpleImportValidator
{
    public function validate(array $keluargaData, array $wargaData): array
    {
        $errors = [];

        // Validate keluarga
        foreach ($keluargaData as $index => $keluarga) {
            $row = $index + 2; // +2 karena header dimulai dari baris 1

            // Required fields
            if (empty($keluarga['no_kk'])) {
                $errors[] = "Sheet Keluarga, Baris {$row}: No KK kosong";
            }
            if (empty($keluarga['kepala_keluarga_nik'])) {
                $errors[] = "Sheet Keluarga, Baris {$row}: NIK Kepala Keluarga kosong";
            }
            if (empty($keluarga['alamat_kk'])) {
                $errors[] = "Sheet Keluarga, Baris {$row}: Alamat KTP kosong";
            }

            // Format validation
            if (!empty($keluarga['no_kk']) && !preg_match('/^\d{16}$/', $keluarga['no_kk'])) {
                $errors[] = "Sheet Keluarga, Baris {$row}: No KK harus 16 digit";
            }
            if (!empty($keluarga['kepala_keluarga_nik']) && !preg_match('/^\d{16}$/', $keluarga['kepala_keluarga_nik'])) {
                $errors[] = "Sheet Keluarga, Baris {$row}: NIK Kepala Keluarga harus 16 digit";
            }

            // Validate status_domisili_keluarga enum values
            if (!empty($keluarga['status_domisili_keluarga'])) {
                $validStatus = ['Tetap', 'Non Domisili', 'Luar', 'Sementara'];
                if (!in_array($keluarga['status_domisili_keluarga'], $validStatus)) {
                    $errors[] = "Sheet Keluarga, Baris {$row}: Status Domisili tidak valid. Pilih salah satu: " . implode(', ', $validStatus);
                }
            }
        }

        // Validate warga
        foreach ($wargaData as $index => $warga) {
            $row = $index + 2;

            // Required fields
            if (empty($warga['no_kk'])) {
                $errors[] = "Sheet Warga, Baris {$row}: No KK kosong";
            }
            if (empty($warga['nik'])) {
                $errors[] = "Sheet Warga, Baris {$row}: NIK kosong";
            }
            if (empty($warga['nama_lengkap'])) {
                $errors[] = "Sheet Warga, Baris {$row}: Nama kosong";
            }

            // Format validation
            if (!empty($warga['nik']) && !preg_match('/^\d{16}$/', $warga['nik'])) {
                $errors[] = "Sheet Warga, Baris {$row}: NIK harus 16 digit";
            }
            if (!empty($warga['jenis_kelamin']) && !in_array($warga['jenis_kelamin'], ['L', 'P'])) {
                $errors[] = "Sheet Warga, Baris {$row}: Jenis kelamin harus L atau P";
            }

            // Date validation
            if (!empty($warga['tanggal_lahir'])) {
                try {
                    Carbon::createFromFormat('Y-m-d', $warga['tanggal_lahir']);
                } catch (\Exception $e) {
                    $errors[] = "Sheet Warga, Baris {$row}: Format tanggal lahir salah (gunakan YYYY-MM-DD)";
                }
            }

            // Validate status_perkawinan enum values
            if (!empty($warga['status_perkawinan'])) {
                $validStatus = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
                if (!in_array($warga['status_perkawinan'], $validStatus)) {
                    $errors[] = "Sheet Warga, Baris {$row}: Status Perkawinan tidak valid. Pilih salah satu: " . implode(', ', $validStatus);
                }
            }

            // Validate hubungan_keluarga enum values
            if (!empty($warga['hubungan_keluarga'])) {
                $validHubungan = ['Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu', 'Lainnya'];
                if (!in_array($warga['hubungan_keluarga'], $validHubungan)) {
                    $errors[] = "Sheet Warga, Baris {$row}: Hubungan Keluarga tidak valid. Pilih salah satu: " . implode(', ', $validHubungan);
                }
            }
        }

        // Check duplicates
        $kkNumbers = array_column($keluargaData, 'no_kk');
        $nikNumbers = array_column($wargaData, 'nik');

        if (count($kkNumbers) !== count(array_unique($kkNumbers))) {
            $errors[] = "Ada No KK yang duplikat di sheet Keluarga";
        }

        if (count($nikNumbers) !== count(array_unique($nikNumbers))) {
            $errors[] = "Ada NIK yang duplikat di sheet Warga";
        }

        // Check existing data in database
        $existingKK = Keluarga::whereIn('no_kk', $kkNumbers)->pluck('no_kk')->toArray();
        foreach ($existingKK as $kk) {
            $errors[] = "No KK {$kk} sudah ada di database";
        }

        $existingNIK = Warga::whereIn('nik', $nikNumbers)->pluck('nik')->toArray();
        foreach ($existingNIK as $nik) {
            $errors[] = "NIK {$nik} sudah ada di database";
        }

        return $errors;
    }
}

/**
 * Simple import service
 */
class SimpleImportService
{
    public function import(array $keluargaData, array $wargaData)
    {
        DB::transaction(function() use ($keluargaData, $wargaData) {
            $kepalaKeluargaMap = [];

            // Step 1: Import keluarga dulu
            foreach ($keluargaData as $keluarga) {
                $keluargaModel = Keluarga::create([
                    'no_kk' => $keluarga['no_kk'],
                    'kepala_keluarga_id' => null, // Akan diisi setelah warga dibuat

                    // Alamat KTP (manual input)
                    'alamat_kk' => $keluarga['alamat_kk'],
                    'rt_kk' => $keluarga['rt_kk'],
                    'rw_kk' => $keluarga['rw_kk'],
                    'kelurahan_kk' => $keluarga['kelurahan_kk'],
                    'kecamatan_kk' => $keluarga['kecamatan_kk'],
                    'kabupaten_kk' => $keluarga['kabupaten_kk'],
                    'provinsi_kk' => $keluarga['provinsi_kk'],

                    // Alamat Domisili (rt_id connection)
                    'alamat_domisili' => $keluarga['alamat_domisili'] ?? null,
                    'rt_id' => $keluarga['rt_id'] ?? null,

                    // Status Domisili
                    'status_domisili_keluarga' => $keluarga['status_domisili_keluarga'] ?? 'Tetap',
                    'tanggal_mulai_domisili_keluarga' => !empty($keluarga['tanggal_mulai_domisili'])
                        ? Carbon::createFromFormat('Y-m-d', $keluarga['tanggal_mulai_domisili'])
                        : null,
                    'keterangan_status' => null,

                    'created_by' => auth()->id(),
                ]);

                // Simpan mapping untuk update kepala keluarga
                $kepalaKeluargaMap[$keluarga['no_kk']] = [
                    'keluarga_id' => $keluargaModel->id,
                    'kepala_nik' => $keluarga['kepala_keluarga_nik']
                ];
            }

            // Step 2: Import warga dan update kepala keluarga
            foreach ($wargaData as $warga) {
                $wargaModel = Warga::create([
                    'nik' => $warga['nik'],
                    'nama_lengkap' => $warga['nama_lengkap'],
                    'tempat_lahir' => $warga['tempat_lahir'],
                    'tanggal_lahir' => Carbon::createFromFormat('Y-m-d', $warga['tanggal_lahir']),
                    'jenis_kelamin' => $warga['jenis_kelamin'],
                    'golongan_darah' => $warga['golongan_darah'] ?? null,
                    'agama' => $warga['agama'],
                    'status_perkawinan' => $warga['status_perkawinan'],
                    'pekerjaan' => $warga['pekerjaan'],
                    'kewarganegaraan' => $warga['kewarganegaraan'],
                    'pendidikan_terakhir' => $warga['pendidikan_terakhir'],
                    'hubungan_keluarga' => $warga['hubungan_keluarga'],
                    'no_telepon' => $warga['no_telepon'] ?? null,
                    'email' => !empty($warga['email']) ? $warga['email'] : null,
                    'nama_ayah' => $warga['nama_ayah'] ?? null,
                    'nama_ibu' => $warga['nama_ibu'] ?? null,
                    'kk_id' => $kepalaKeluargaMap[$warga['no_kk']]['keluarga_id'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                // Step 3: Update kepala keluarga jika ini adalah kepala keluarga
                if (isset($kepalaKeluargaMap[$warga['no_kk']])) {
                    // Use trim to handle potential whitespace issues from Excel parsing
                    $wargaNik = trim((string) $warga['nik']);
                    $expectedNik = trim((string) $kepalaKeluargaMap[$warga['no_kk']]['kepala_nik']);

                    // Log debugging untuk setiap warga yang diproses
                    \Log::info('Processing kepala keluarga check', [
                        'warga_nik' => $wargaNik,
                        'expected_kepala_nik' => $expectedNik,
                        'kk_no' => $warga['no_kk'],
                        'keluarga_id' => $kepalaKeluargaMap[$warga['no_kk']]['keluarga_id'],
                        'is_match' => ($wargaNik === $expectedNik),
                        'warga_id' => $wargaModel->id,
                        'warga_name' => $warga['nama_lengkap']
                    ]);

                    if ($wargaNik === $expectedNik) {
                        \Log::info('Updating kepala keluarga', [
                            'keluarga_id' => $kepalaKeluargaMap[$warga['no_kk']]['keluarga_id'],
                            'kepala_keluarga_id' => $wargaModel->id
                        ]);

                        Keluarga::where('id', $kepalaKeluargaMap[$warga['no_kk']]['keluarga_id'])
                                ->update(['kepala_keluarga_id' => $wargaModel->id]);
                    }
                }
            }
        });
    }
}
