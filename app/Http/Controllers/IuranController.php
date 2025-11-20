<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\JenisIuran;
use App\Models\KeluargaIuran;
use App\Models\PembayaranIuran;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IuranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Iuran::with([
                'keluarga.kepalaKeluarga',
                'jenisIuran',
                'createdBy',
                'pembayaran'
            ])
            ->orderBy('periode_bulan', 'desc')
            ->orderBy('jenis_iuran_id');

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('keluarga', function ($keluarga) use ($search) {
                    $keluarga->where('no_kk', 'like', "%{$search}%")
                            ->orWhereHas('kepalaKeluarga', function ($kepala) use ($search) {
                                $kepala->where('nama_lengkap', 'like', "%{$search}%");
                            });
                })
                ->orWhereHas('jenisIuran', function ($jenis) use ($search) {
                    $jenis->where('nama', 'like', "%{$search}%");
                })
                ->orWhere('periode_bulan', 'like', "%{$search}%");
            });
        }

        // Filter by periode
        if ($request->has('periode') && $request->periode != '') {
            $query->where('periode_bulan', $request->periode);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by keluarga
        if ($request->has('keluarga_id') && $request->keluarga_id != '') {
            $query->where('kk_id', $request->keluarga_id);
        }

        // Filter by jenis iuran
        if ($request->has('jenis_iuran_id') && $request->jenis_iuran_id != '') {
            $query->where('jenis_iuran_id', $request->jenis_iuran_id);
        }

        $iurans = $query->paginate(15);

        // Get data for filters
        $keluargas = Keluarga::with(['kepalaKeluarga'])->orderBy('no_kk')->get();
        $jenisIurans = JenisIuran::where('is_aktif', true)->orderBy('nama')->get();
        $periodes = Iuran::select('periode_bulan')
            ->distinct()
            ->orderBy('periode_bulan', 'desc')
            ->pluck('periode_bulan');

        return view('admin.iuran.index', compact(
            'iurans',
            'keluargas',
            'jenisIurans',
            'periodes'
        ));
    }

    /**
     * API: Get iuran data for AJAX loading
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $query = Iuran::with([
                'keluarga.kepalaKeluarga',
                'jenisIuran',
                'createdBy',
                'pembayaran'
            ])
            ->orderBy('periode_bulan', 'desc')
            ->orderBy('jenis_iuran_id');

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('keluarga', function ($keluarga) use ($search) {
                    $keluarga->where('no_kk', 'like', "%{$search}%")
                            ->orWhereHas('kepalaKeluarga', function ($kepala) use ($search) {
                                $kepala->where('nama_lengkap', 'like', "%{$search}%");
                            });
                })
                ->orWhereHas('jenisIuran', function ($jenis) use ($search) {
                    $jenis->where('nama', 'like', "%{$search}%");
                })
                ->orWhere('periode_bulan', 'like', "%{$search}%");
            });
        }

        // Filter by periode
        if ($request->has('periode') && $request->periode != '') {
            $query->where('periode_bulan', $request->periode);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by keluarga
        if ($request->has('keluarga_id') && $request->keluarga_id != '') {
            $query->where('kk_id', $request->keluarga_id);
        }

        // Filter by jenis iuran
        if ($request->has('jenis_iuran_id') && $request->jenis_iuran_id != '') {
            $query->where('jenis_iuran_id', $request->jenis_iuran_id);
        }

        // Debug: Get total count for comparison
        $totalCount = Iuran::count();

        $iurans = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $iurans->items(),
            'pagination' => [
                'current_page' => $iurans->currentPage(),
                'last_page' => $iurans->lastPage(),
                'per_page' => $iurans->perPage(),
                'total' => $iurans->total(),
                'from' => $iurans->firstItem(),
                'to' => $iurans->lastItem(),
            ],
            'debug' => [
                'total_all_iurans' => $totalCount,
                'filtered_count' => $iurans->total(),
                'has_filters' => [
                    'search' => $request->has('search') && $request->search != '',
                    'periode' => $request->has('periode') && $request->periode != '',
                    'status' => $request->has('status') && $request->status != '',
                    'keluarga_id' => $request->has('keluarga_id') && $request->keluarga_id != '',
                    'jenis_iuran_id' => $request->has('jenis_iuran_id') && $request->jenis_iuran_id != ''
                ],
                'filter_values' => [
                    'search' => $request->search,
                    'periode' => $request->periode,
                    'status' => $request->status,
                    'keluarga_id' => $request->keluarga_id,
                    'jenis_iuran_id' => $request->jenis_iuran_id
                ]
            ]
        ]);
    }

    /**
     * API: Get single iuran for modal operations
     */
    public function apiShow(Iuran $iuran): JsonResponse
    {
        $iuran->load([
            'keluarga.kepalaKeluarga',
            'jenisIuran',
            'createdBy',
            'pembayaran'
        ]);

        return response()->json([
            'success' => true,
            'data' => $iuran
        ]);
    }

    /**
     * API: Get iuran data for editing
     */
    public function apiEdit(Iuran $iuran): JsonResponse
    {
        $iuran->load(['keluarga', 'jenisIuran']);

        return response()->json([
            'success' => true,
            'data' => $iuran
        ]);
    }

    /**
     * API: Delete iuran
     */
    public function apiDestroy(Iuran $iuran): JsonResponse
    {
        try {
            // Check if iuran has payments
            if ($iuran->pembayaran()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus iuran yang sudah memiliki pembayaran'
                ], 422);
            }

            $iuran->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data iuran berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data iuran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kk_id' => 'required|exists:keluargas,id',
            'jenis_iuran_id' => 'required|exists:jenis_iurans,id',
            'periode_bulan' => 'required|date_format:Y-m',
            'nominal' => 'required|numeric|min:0',
            'status' => 'required|in:belum_bayar,sebagian,lunas,batal',
            'jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for duplicates
            $exists = Iuran::where('kk_id', $request->kk_id)
                ->where('jenis_iuran_id', $request->jenis_iuran_id)
                ->where('periode_bulan', $request->periode_bulan)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan untuk keluarga, jenis iuran, dan periode ini sudah ada'
                ], 400);
            }

            $iuran = Iuran::create([
                'kk_id' => $request->kk_id,
                'jenis_iuran_id' => $request->jenis_iuran_id,
                'periode_bulan' => $request->periode_bulan,
                'nominal' => $request->nominal,
                'status' => $request->status,
                'jatuh_tempo' => $request->jatuh_tempo,
                'keterangan' => $request->keterangan,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tagihan iuran berhasil dibuat',
                'data' => $iuran->load(['keluarga', 'jenisIuran'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tagihan iuran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Iuran $iuran)
    {
        $iuran->load([
            'keluarga.kepalaKeluarga',
            'jenisIuran',
            'createdBy',
            'pembayaran'
        ]);

        return view('admin.iuran.show', compact('iuran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Iuran $iuran)
    {
        $iuran->load(['keluarga', 'jenisIuran']);

        $keluargas = Keluarga::with(['kepalaKeluarga'])
            ->orderBy('no_kk')
            ->get();

        $jenisIurans = JenisIuran::where('is_aktif', true)
            ->orderBy('nama')
            ->get();

        return view('admin.iuran.edit', compact('iuran', 'keluargas', 'jenisIurans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Iuran $iuran): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kk_id' => 'required|exists:keluargas,id',
            'jenis_iuran_id' => 'required|exists:jenis_iurans,id',
            'periode_bulan' => 'required|date_format:Y-m',
            'nominal' => 'required|numeric|min:0',
            'status' => 'required|in:belum_bayar,sebagian,lunas,batal',
            'denda_terlambatan' => 'nullable|numeric|min:0',
            'jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for duplicates (excluding current record)
            $exists = Iuran::where('kk_id', $request->kk_id)
                ->where('jenis_iuran_id', $request->jenis_iuran_id)
                ->where('periode_bulan', $request->periode_bulan)
                ->where('id', '!=', $iuran->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan untuk keluarga, jenis iuran, dan periode ini sudah ada'
                ], 400);
            }

            $iuran->update([
                'kk_id' => $request->kk_id,
                'jenis_iuran_id' => $request->jenis_iuran_id,
                'periode_bulan' => $request->periode_bulan,
                'nominal' => $request->nominal,
                'status' => $request->status,
                'denda_terlambatan' => $request->denda_terlambatan ?? 0,
                'jatuh_tempo' => $request->jatuh_tempo,
                'keterangan' => $request->keterangan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tagihan iuran berhasil diupdate',
                'data' => $iuran->load(['keluarga', 'jenisIuran'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate tagihan iuran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Iuran $iuran): JsonResponse
    {
        try {
            // Check if there are any payments
            if ($iuran->pembayaran()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus tagihan yang sudah memiliki pembayaran'
                ], 400);
            }

            $iuran->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tagihan iuran berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tagihan iuran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate bulk iuran for periode
     */
    public function generateBulk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'periode_bulan' => 'required|date_format:Y-m',
            'jenis_iuran_ids' => 'required|array',
            'jenis_iuran_ids.*' => 'exists:jenis_iurans,id',
            'keluarga_ids' => 'nullable|array',
            'keluarga_ids.*' => 'exists:keluargas,id',
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
            $jenisIuranIds = $request->jenis_iuran_ids;
            $keluargaIds = $request->keluarga_ids ?? Keluarga::where('status_keluarga', 'Aktif')->pluck('id');

            $createdCount = 0;
            $skippedCount = 0;

            foreach ($keluargaIds as $kkId) {
                foreach ($jenisIuranIds as $jiId) {
                    // Check if already exists
                    $exists = Iuran::where('kk_id', $kkId)
                        ->where('jenis_iuran_id', $jiId)
                        ->where('periode_bulan', $periode)
                        ->exists();

                    if (!$exists) {
                        // Get nominal from keluarga_iuran or jenis_iuran
                        $keluargaIuran = KeluargaIuran::where('keluarga_id', $kkId)
                            ->where('jenis_iuran_id', $jiId)
                            ->where('status_aktif', true)
                            ->first();

                        $jenisIuran = JenisIuran::find($jiId);

                        if ($keluargaIuran || $jenisIuran) {
                            Iuran::create([
                                'kk_id' => $kkId,
                                'jenis_iuran_id' => $jiId,
                                'periode_bulan' => $periode,
                                'nominal' => $keluargaIuran?->nominal_custom ?? $jenisIuran->jumlah,
                                'status' => 'belum_bayar',
                                'jatuh_tempo' => Carbon::parse($periode . '-01')->endOfMonth(),
                                'keterangan' => "Generate otomatis periode " . Carbon::parse($periode . '-01')->format('F Y'),
                                'created_by' => auth()->id(),
                            ]);
                            $createdCount++;
                        }
                    } else {
                        $skippedCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Generate tagihan selesai. {$createdCount} dibuat, {$skippedCount} sudah ada"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate tagihan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get keluarga jenis iuran connections for dropdown
     */
    public function getKeluargaJenisIuran($keluargaId): JsonResponse
    {
        try {
            $keluarga = Keluarga::findOrFail($keluargaId);
            $jenisIurans = $keluarga->jenisIuran()
                ->wherePivot('status_aktif', true)
                ->where('jenis_iurans.is_aktif', true)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $jenisIurans
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            // Build query same as apiIndex
            $query = Iuran::with([
                'keluarga.kepalaKeluarga',
                'jenisIuran',
                'createdBy',
                'pembayaran'
            ]);

            // Apply same filters as apiIndex
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('keluarga', function ($keluarga) use ($search) {
                        $keluarga->where('no_kk', 'like', "%{$search}%")
                                ->orWhereHas('kepalaKeluarga', function ($kepala) use ($search) {
                                    $kepala->where('nama_lengkap', 'like', "%{$search}%");
                                });
                    })
                    ->orWhereHas('jenisIuran', function ($jenis) use ($search) {
                        $jenis->where('nama', 'like', "%{$search}%");
                    })
                    ->orWhere('periode_bulan', 'like', "%{$search}%");
                });
            }

            // Filter by periode
            if ($request->has('periode') && $request->periode != '') {
                $query->where('periode_bulan', $request->periode);
            }

            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            // Filter by keluarga
            if ($request->has('keluarga_id') && $request->keluarga_id != '') {
                $query->where('kk_id', $request->keluarga_id);
            }

            // Filter by jenis iuran
            if ($request->has('jenis_iuran_id') && $request->jenis_iuran_id != '') {
                $query->where('jenis_iuran_id', $request->jenis_iuran_id);
            }

            $filteredIurans = $query->get();

            // Calculate statistics
            $total = $filteredIurans->count();
            $belumBayar = $filteredIurans->where('status', 'belum_bayar')->count();
            $sebagian = $filteredIurans->where('status', 'sebagian')->count();
            $lunas = $filteredIurans->where('status', 'lunas')->count();

            $totalNominal = $filteredIurans->sum('nominal');
            $totalDenda = $filteredIurans->sum('denda_terlambatan');

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'belum_bayar' => $belumBayar,
                    'sebagian' => $sebagian,
                    'lunas' => $lunas,
                    'total_nominal' => $totalNominal,
                    'total_denda' => $totalDenda,
                    'persentase_lunas' => $total > 0 ? round(($lunas / $total) * 100, 2) : 0,
                    'debug' => [
                        'total_all_iurans' => Iuran::count(),
                        'filtered_count' => $filteredIurans->count(),
                        'has_filters' => [
                            'search' => $request->has('search') && $request->search != '',
                            'periode' => $request->has('periode') && $request->periode != '',
                            'status' => $request->has('status') && $request->status != '',
                            'keluarga_id' => $request->has('keluarga_id') && $request->keluarga_id != '',
                            'jenis_iuran_id' => $request->has('jenis_iuran_id') && $request->jenis_iuran_id != ''
                        ],
                        'filter_values' => [
                            'search' => $request->search,
                            'periode' => $request->periode,
                            'status' => $request->status,
                            'keluarga_id' => $request->keluarga_id,
                            'jenis_iuran_id' => $request->jenis_iuran_id
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment for iuran
     */
    public function processPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'iuran_id' => 'required|exists:iurans,id',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,transfer,qris,ewallet',
            'keterangan' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $iuran = Iuran::findOrFail($request->iuran_id);

            // Check if iuran allows payment
            if (in_array($iuran->status, ['batal', 'lunas'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan ini tidak dapat diproses pembayarannya'
                ], 400);
            }

            // Create payment record - simplified to match actual database schema
            $pembayaran = PembayaranIuran::create([
                'iuran_id' => $iuran->id,
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode_pembayaran' => $request->metode_pembayaran, // Note: database expects 'cash', not 'tunai'
                'created_by' => auth()->id(),
                'keterangan' => $request->keterangan ?? 'Pembayaran cash',
                'nomor_referensi' => PembayaranIuran::generateNomorReferensi(), // Generate nomor referensi
            ]);

            // Update iuran status based on total payments
            $totalDibayar = $iuran->pembayaran()->sum('jumlah_bayar');
            $totalTagihan = $iuran->total_tagihan ?? $iuran->nominal + $iuran->denda_terlambatan;

            if ($totalDibayar >= $totalTagihan) {
                $iuran->update(['status' => 'lunas']);
            } elseif ($totalDibayar > 0) {
                $iuran->update(['status' => 'sebagian']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses',
                'data' => [
                    'payment_id' => $pembayaran->id,
                    'jumlah_bayar' => $pembayaran->jumlah_bayar,
                    'metode_pembayaran' => $pembayaran->metode_pembayaran,
                    'status_iuran' => $iuran->fresh()->status,
                    'total_dibayar' => $totalDibayar,
                    'sisa_tagihan' => max(0, $totalTagihan - $totalDibayar),
                    'nomor_referensi' => $pembayaran->nomor_referensi
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history for an iuran
     */
    public function getPaymentHistory($iuranId): JsonResponse
    {
        try {
            $iuran = Iuran::findOrFail($iuranId);

            $payments = $iuran->pembayaran()
                ->with(['createdBy' => function($query) {
                    $query->select('id', 'name');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'jumlah' => $payment->jumlah,
                        'metode_pembayaran' => $payment->metode_pembayaran,
                        'nama_bank' => $payment->nama_bank,
                        'nomor_rekening' => $payment->nomor_rekening,
                        'nama_pengirim' => $payment->nama_pengirim,
                        'waktu_pembayaran' => $payment->waktu_pembayaran,
                        'status' => $payment->status,
                        'keterangan' => $payment->keterangan,
                        'bukti_pembayaran' => $payment->bukti_pembayaran,
                        'created_at' => $payment->created_at,
                        'created_by_name' => $payment->createdBy?->name
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment (for non-cash payments)
     */
    public function verifyPayment(Request $request, PembayaranIuran $pembayaran): JsonResponse
    {
        try {
            if ($pembayaran->status === 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran sudah diverifikasi'
                ], 400);
            }

            $pembayaran->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => Carbon::now()
            ]);

            // Update iuran status
            $iuran = $pembayaran->iuran;
            $totalDibayar = $iuran->pembayaran()->sum('jumlah_bayar');
            $totalTagihan = $iuran->total_tagihan ?? $iuran->nominal + $iuran->denda_terlambatan;

            if ($totalDibayar >= $totalTagihan) {
                $iuran->update(['status' => 'lunas']);
            } elseif ($totalDibayar > 0) {
                $iuran->update(['status' => 'sebagian']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diverifikasi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal verifikasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject payment (for non-cash payments)
     */
    public function rejectPayment(Request $request, PembayaranIuran $pembayaran): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'alasan_penolakan' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($pembayaran->status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran sudah ditolak'
                ], 400);
            }

            $pembayaran->update([
                'status' => 'rejected',
                'alasan_penolakan' => $request->alasan_penolakan,
                'rejected_by' => auth()->id(),
                'rejected_at' => Carbon::now()
            ]);

            // Update iuran status
            $iuran = $pembayaran->iuran;
            $totalDibayar = $iuran->pembayaran()->sum('jumlah_bayar');
            $totalTagihan = $iuran->total_tagihan ?? $iuran->nominal + $iuran->denda_terlambatan;

            if ($totalDibayar >= $totalTagihan) {
                $iuran->update(['status' => 'lunas']);
            } elseif ($totalDibayar > 0) {
                $iuran->update(['status' => 'sebagian']);
            } else {
                $iuran->update(['status' => 'belum_bayar']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil ditolak'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
